<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Presensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'presensis';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'foto_masuk',
        'foto_pulang',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_pulang',
        'longitude_pulang',
        'status',
        'keterangan',
        'jam_kerja',
        'menit_terlambat',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_pulang' => 'decimal:8',
        'longitude_pulang' => 'decimal:8',
        'jam_kerja' => 'decimal:2',
        'menit_terlambat' => 'integer',
    ];

    protected $dates = [
        'tanggal',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Status options
    const STATUS_HADIR = 'hadir';
    const STATUS_TERLAMBAT = 'terlambat';
    const STATUS_TIDAK_HADIR = 'tidak_hadir';
    const STATUS_SAKIT = 'sakit';
    const STATUS_IZIN = 'izin';
    const STATUS_CUTI = 'cuti';
    const STATUS_LIBUR = 'libur';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_HADIR => 'Hadir',
            self::STATUS_TERLAMBAT => 'Terlambat',
            self::STATUS_TIDAK_HADIR => 'Tidak Hadir',
            self::STATUS_SAKIT => 'Sakit',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_CUTI => 'Cuti',
            self::STATUS_LIBUR => 'Libur',
        ];
    }

    // Relationships
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getFotoMasukUrlAttribute(): ?string
    {
        return $this->foto_masuk ? asset('storage/' . $this->foto_masuk) : null;
    }

    public function getFotoPulangUrlAttribute(): ?string
    {
        return $this->foto_pulang ? asset('storage/' . $this->foto_pulang) : null;
    }

    public function getJamKerjaFormattedAttribute(): string
    {
        if (!$this->jam_kerja)
            return '-';

        $hours = floor($this->jam_kerja);
        $minutes = ($this->jam_kerja - $hours) * 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function getKeteranganTerlambatAttribute(): string
    {
        if ($this->menit_terlambat <= 0)
            return '';

        $hours = floor($this->menit_terlambat / 60);
        $minutes = $this->menit_terlambat % 60;

        if ($hours > 0) {
            return "Terlambat {$hours} jam {$minutes} menit";
        }

        return "Terlambat {$minutes} menit";
    }

    // Scopes
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);
    }

    public function scopeTahunIni($query)
    {
        return $query->whereYear('tanggal', now()->year);
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    // Helper Methods
    public function hitungJamKerja(): void
    {
        if ($this->jam_masuk && $this->jam_pulang) {
            $masuk = Carbon::createFromFormat('H:i:s', $this->jam_masuk);
            $pulang = Carbon::createFromFormat('H:i:s', $this->jam_pulang);

            // Jika pulang besok hari (shift malam)
            if ($pulang->lt($masuk)) {
                $pulang->addDay();
            }

            $jamKerja = $pulang->diffInMinutes($masuk) / 60;

            // Kurangi jam istirahat (misal 1 jam)
            if ($jamKerja > 4) { // Jika kerja lebih dari 4 jam, kurangi 1 jam istirahat
                $jamKerja -= 1;
            }

            $this->jam_kerja = round($jamKerja, 2);
        }
    }

    public function hitungTerlambat($jamMasukStandar = '08:00'): void
    {
        if ($this->jam_masuk) {
            $jamMasuk = Carbon::createFromFormat('H:i:s', $this->jam_masuk);
            $jamStandar = Carbon::createFromFormat('H:i', $jamMasukStandar);

            if ($jamMasuk->gt($jamStandar)) {
                $this->menit_terlambat = $jamMasuk->diffInMinutes($jamStandar);

                if ($this->status === self::STATUS_HADIR) {
                    $this->status = self::STATUS_TERLAMBAT;
                }
            } else {
                $this->menit_terlambat = 0;
            }
        }
    }

    public function isLengkap(): bool
    {
        return $this->jam_masuk && $this->jam_pulang;
    }

    public function isTerlambat(): bool
    {
        return $this->menit_terlambat > 0;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($presensi) {
            $presensi->hitungJamKerja();
            $presensi->hitungTerlambat();
        });
    }
}