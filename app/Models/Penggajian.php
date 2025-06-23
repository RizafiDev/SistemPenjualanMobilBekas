<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Penggajian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penggajians';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_DIBAYAR = 'dibayar';
    const STATUS_BATAL = 'batal';

    protected $fillable = [
        'karyawan_id',
        'periode',
        'tanggal_gaji',
        'gaji_pokok',
        'tunjangan',
        'bonus',
        'lembur',
        'insentif',
        'potongan_terlambat',
        'potongan_absensi',
        'potongan_lainnya',
        'total_gaji',
        'total_potongan',
        'gaji_bersih',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_gaji' => 'date',
        'gaji_pokok' => 'decimal:2',
        'tunjangan' => 'decimal:2',
        'bonus' => 'decimal:2',
        'lembur' => 'decimal:2',
        'insentif' => 'decimal:2',
        'potongan_terlambat' => 'decimal:2',
        'potongan_absensi' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'total_gaji' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
    ];

    // Relationships
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Accessors
    public function getBulanTahunAttribute(): string
    {
        return Carbon::createFromFormat('Y-m', $this->periode)->format('F Y');
    }

    public function getTotalPendapatanAttribute(): float
    {
        return $this->gaji_pokok + $this->tunjangan + $this->bonus + $this->lembur + $this->insentif;
    }

    public function getTotalPotonganRealAttribute(): float
    {
        return $this->potongan_terlambat + $this->potongan_absensi + $this->potongan_lainnya;
    }

    // Scopes
    public function scopePeriode($query, string $periode)
    {
        return $query->where('periode', $periode);
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('periode', 'like', $tahun . '%');
    }

    public function scopeBulan($query, int $bulan, int $tahun = null)
    {
        $tahun = $tahun ?? now()->year;
        $periode = sprintf('%04d-%02d', $tahun, $bulan);
        return $query->where('periode', $periode);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDibayar($query)
    {
        return $query->where('status', self::STATUS_DIBAYAR);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeBatal($query)
    {
        return $query->where('status', self::STATUS_BATAL);
    }

    // Helper methods
    public function calculateTotals(): void
    {
        $this->total_gaji = $this->getTotalPendapatanAttribute();
        $this->total_potongan = $this->getTotalPotonganRealAttribute();
        $this->gaji_bersih = $this->total_gaji - $this->total_potongan;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isDibayar(): bool
    {
        return $this->status === self::STATUS_DIBAYAR;
    }

    public function isBatal(): bool
    {
        return $this->status === self::STATUS_BATAL;
    }

    public function markAsDibayar(): void
    {
        $this->update(['status' => self::STATUS_DIBAYAR]);
    }

    public function markAsBatal(): void
    {
        $this->update(['status' => self::STATUS_BATAL]);
    }

    // Static methods
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DIBAYAR => 'Dibayar',
            self::STATUS_BATAL => 'Batal',
        ];
    }

    public static function generatePeriode(int $tahun = null, int $bulan = null): string
    {
        $tahun = $tahun ?? now()->year;
        $bulan = $bulan ?? now()->month;
        return sprintf('%04d-%02d', $tahun, $bulan);
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($penggajian) {
            $penggajian->calculateTotals();
        });
    }
}