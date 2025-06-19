<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokMobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_mobils';

    protected $fillable = [
        'mobil_id',
        'varian_id',
        'warna',
        'no_rangka',
        'no_mesin',
        'tahun',
        'kilometer',
        'kondisi',
        'status',
        'harga_beli',
        'harga_jual',
        'tanggal_masuk',
        'tanggal_keluar',
        'lokasi',
        'catatan',
        'kelengkapan',
        'riwayat_perbaikan',
        'dokumen',
        'foto_kondisi',
        'kondisi_fitur',
    ];

    protected $casts = [
        'kelengkapan' => 'array',
        'riwayat_perbaikan' => 'array',
        'dokumen' => 'array',
        'foto_kondisi' => 'array',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'kondisi_fitur' => 'array',
    ];

    const KONDISI = [
        'sangat_baik' => 'Sangat Baik',
        'baik' => 'Baik',
        'cukup' => 'Cukup',
        'butuh_perbaikan' => 'Butuh Perbaikan',
        'project' => 'Project Car',
    ];

    const STATUS = [
        'tersedia' => 'Tersedia',
        'terjual' => 'Terjual',
        'booking' => 'Booking',
        'indent' => 'Indent',
        'dalam_perbaikan' => 'Dalam Perbaikan',
    ];

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class);
    }

    public function varian(): BelongsTo
    {
        return $this->belongsTo(Varian::class);
    }

    public function riwayatServis(): HasMany
    {
        return $this->hasMany(RiwayatServis::class);
    }

    // Accessors
    public function getFormattedHargaBeliAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getFormattedHargaJualAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    public function getFormattedLabaKotorAttribute(): string
    {
        return 'Rp ' . number_format($this->laba_kotor, 0, ',', '.');
    }

    public function getKondisiBadgeColorAttribute(): string
    {
        return match ($this->kondisi) {
            'sangat_baik' => 'success',
            'baik' => 'info',
            'cukup' => 'warning',
            'butuh_perbaikan' => 'danger',
            'project' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'success',
            'terjual' => 'info',
            'booking' => 'warning',
            'indent' => 'gray',
            'dalam_perbaikan' => 'danger',
            default => 'gray',
        };
    }

    // Accessor untuk menghitung total biaya service
    public function getTotalBiayaServiceAttribute(): float
    {
        if (!$this->riwayat_perbaikan) {
            return 0;
        }

        return collect($this->riwayat_perbaikan)
            ->sum('biaya');
    }

    // Accessor untuk menghitung laba bersih
    public function getLabaBersihAttribute(): float
    {
        return $this->laba_kotor - $this->total_biaya_service;
    }
}
