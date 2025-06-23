<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengajuan_cutis';

    protected $fillable = [
        'karyawan_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'dokumen',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'jumlah_hari' => 'integer',
    ];

    // Enum constants
    const JENIS_TAHUNAN = 'tahunan';
    const JENIS_SAKIT = 'sakit';
    const JENIS_DARURAT = 'darurat';
    const JENIS_LAINNYA = 'lainnya';

    const STATUS_MENUNGGU = 'menunggu';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';

    public static function getJenisOptions(): array
    {
        return [
            self::JENIS_TAHUNAN => 'Cuti Tahunan',
            self::JENIS_SAKIT => 'Cuti Sakit',
            self::JENIS_DARURAT => 'Cuti Darurat',
            self::JENIS_LAINNYA => 'Lainnya',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_MENUNGGU => 'Menunggu Persetujuan',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
        ];
    }

    // Relationships
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Accessors
    public function getJenisLabelAttribute(): string
    {
        return self::getJenisOptions()[$this->jenis] ?? $this->jenis;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'warning',
            self::STATUS_DISETUJUI => 'success',
            self::STATUS_DITOLAK => 'danger',
            default => 'gray'
        };
    }

    // Scopes
    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
            ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
    }

    // Methods
    public function approve($userId = null, $tanggalPersetujuan = null): bool
    {
        return $this->update([
            'status' => self::STATUS_DISETUJUI,
            'disetujui_oleh' => $userId,
            'tanggal_persetujuan' => $tanggalPersetujuan ?? now(),
            'alasan_penolakan' => null,
        ]);
    }

    public function reject($alasanPenolakan, $userId = null): bool
    {
        return $this->update([
            'status' => self::STATUS_DITOLAK,
            'disetujui_oleh' => $userId,
            'tanggal_persetujuan' => now(),
            'alasan_penolakan' => $alasanPenolakan,
        ]);
    }

    public function isDiproses(): bool
    {
        return in_array($this->status, [self::STATUS_DISETUJUI, self::STATUS_DITOLAK]);
    }

    public function isMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    // Boot method untuk auto-calculate jumlah_hari
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->tanggal_mulai && $model->tanggal_selesai) {
                $model->jumlah_hari = $model->tanggal_mulai->diffInDays($model->tanggal_selesai) + 1;
            }
        });
    }
}