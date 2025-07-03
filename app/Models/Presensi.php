<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_pulang' => 'decimal:8',
        'longitude_pulang' => 'decimal:8',
        'jam_kerja' => 'decimal:2',
        'menit_terlambat' => 'integer',
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
        return self::getStatusOptions()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getFotoMasukUrlAttribute(): ?string
    {
        return $this->foto_masuk ? asset($this->foto_masuk) : null;
    }

    public function getFotoPulangUrlAttribute(): ?string
    {
        return $this->foto_pulang ? asset($this->foto_pulang) : null;
    }

    public function getJamKerjaFormattedAttribute(): string
    {
        if (is_null($this->jam_kerja) || $this->jam_kerja <= 0)
            return '-';
        $totalMinutes = round($this->jam_kerja * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d jam %02d mnt', $hours, $minutes);
    }

    public function getKeteranganTerlambatAttribute(): string
    {
        if ($this->menit_terlambat <= 0)
            return '-';
        $hours = floor($this->menit_terlambat / 60);
        $minutes = $this->menit_terlambat % 60;
        if ($hours > 0)
            return "Terlambat {$hours} jam {$minutes} mnt";
        return "Terlambat {$minutes} mnt";
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
        if (!$this->jam_masuk || !$this->jam_pulang) {
            $this->jam_kerja = null;
            return;
        }

        try {
            $pengaturanKantor = PengaturanKantor::aktif()->first();

            $masukKaryawan = Carbon::parse($this->jam_masuk);
            $pulangKaryawan = Carbon::parse($this->jam_pulang);

            // Handle shift semalam (pulang di hari berikutnya)
            if ($pulangKaryawan->lt($masukKaryawan)) {
                $pulangKaryawan->addDay();
            }

            // Hitung durasi total karyawan dalam jam desimal
            $durasiKaryawanDecimal = $pulangKaryawan->diffInMinutes($masukKaryawan) / 60.0;

            // Nilai default jika pengaturan kantor tidak ada
            $jamIstirahat = 1.0;
            $minimalJamKerjaUntukIstirahat = 4.0;
            $durasiKantorDecimal = 9.0; // Contoh: 9 jam (08:00 - 17:00)

            if ($pengaturanKantor && $pengaturanKantor->jam_masuk && $pengaturanKantor->jam_pulang) {
                $jamMasukKantor = Carbon::parse($pengaturanKantor->jam_masuk->format('H:i:s'));
                $jamPulangKantor = Carbon::parse($pengaturanKantor->jam_pulang->format('H:i:s'));
                if ($jamPulangKantor->lt($jamMasukKantor)) {
                    $jamPulangKantor->addDay();
                }
                $durasiKantorDecimal = $jamPulangKantor->diffInMinutes($jamMasukKantor) / 60.0;
                $jamIstirahat = $pengaturanKantor->jam_istirahat ?? 1.0;
                $minimalJamKerjaUntukIstirahat = $pengaturanKantor->minimal_jam_kerja_istirahat ?? 4.0;
            }

            // Durasi kerja aktual karyawan, sebelum dibatasi
            $jamKerjaKaryawan = $durasiKaryawanDecimal;
            if ($jamKerjaKaryawan >= $minimalJamKerjaUntukIstirahat) {
                $jamKerjaKaryawan -= $jamIstirahat;
            }

            // Durasi kerja standar kantor setelah dikurangi istirahat
            $jamKerjaKantor = $durasiKantorDecimal;
            if ($jamKerjaKantor >= $minimalJamKerjaUntukIstirahat) {
                $jamKerjaKantor -= $jamIstirahat;
            }

            // Jika durasi kerja karyawan > standar, batasi ke standar. Jika tidak, gunakan durasi aktual.
            $jamKerjaFinal = min($jamKerjaKaryawan, $jamKerjaKantor);

            $this->jam_kerja = max(0, round($jamKerjaFinal, 2));

            Log::info("Menghitung jam kerja - Durasi Karyawan: {$durasiKaryawanDecimal}, Durasi Kantor: {$durasiKantorDecimal}, Jam Kerja Final: {$this->jam_kerja}");

        } catch (\Exception $e) {
            Log::error("Gagal menghitung jam kerja untuk presensi ID {$this->id}: " . $e->getMessage());
            $this->jam_kerja = null;
        }
    }

    public function hitungTerlambat(string $jamMasukStandarStr, int $toleransiMenit): void
    {
        if ($this->jam_masuk) {
            try {
                $jamMasukKaryawan = $this->jam_masuk instanceof Carbon ? $this->jam_masuk : Carbon::parse($this->jam_masuk);

                // Create standard time for the same date
                $jamStandarCarbon = Carbon::createFromTimeString($jamMasukStandarStr)
                    ->setDateFrom($jamMasukKaryawan);

                $jamStandarToleransi = $jamStandarCarbon->copy()->addMinutes($toleransiMenit);

                if ($jamMasukKaryawan->gt($jamStandarToleransi)) {
                    $this->menit_terlambat = $jamMasukKaryawan->diffInMinutes($jamStandarCarbon);
                    if (empty($this->status) || $this->status === self::STATUS_HADIR) {
                        $this->status = self::STATUS_TERLAMBAT;
                    }
                } else {
                    $this->menit_terlambat = 0;
                    if (empty($this->status) || $this->status === self::STATUS_TERLAMBAT) {
                        $this->status = self::STATUS_HADIR;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Format jamMasukStandar tidak valid: {$jamMasukStandarStr}. Error: " . $e->getMessage());
                $this->menit_terlambat = 0;
            }
        } else {
            $this->menit_terlambat = 0;
        }
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Presensi $presensi) {
            Log::info("Presensi saving event - ID: {$presensi->id}, jam_masuk: {$presensi->jam_masuk}, jam_pulang: {$presensi->jam_pulang}");

            $pengaturanKantor = PengaturanKantor::aktif()->first();
            $jamMasukStandar = '08:00:00'; // Default
            $toleransiMenit = 0; // Default

            if ($pengaturanKantor) {
                if ($pengaturanKantor->jam_masuk) {
                    $jamMasukStandar = $pengaturanKantor->jam_masuk->format('H:i:s');
                }
                if (isset($pengaturanKantor->toleransi_terlambat)) {
                    $toleransiMenit = (int) $pengaturanKantor->toleransi_terlambat;
                }
            }

            // Hitung terlambat jika ada jam masuk
            if ($presensi->jam_masuk) {
                $presensi->hitungTerlambat($jamMasukStandar, $toleransiMenit);
            } else {
                $presensi->menit_terlambat = 0;
            }

            // Hitung jam kerja jika jam masuk dan jam pulang sudah ada
            if ($presensi->jam_masuk && $presensi->jam_pulang) {
                $presensi->hitungJamKerja();
            } else {
                $presensi->jam_kerja = null;
            }
        });
    }
}
