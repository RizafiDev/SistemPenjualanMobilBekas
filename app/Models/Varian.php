<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Varian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'varians';

    protected $fillable = [
        'mobil_id',
        'nama',
        'kode',
        'deskripsi',
        'harga_otr',
        'tipe_mesin',
        'kapasitas_mesin_cc',
        'silinder',
        'transmisi',
        'jumlah_gigi',
        'daya_hp',
        'torsi_nm',
        'jenis_bahan_bakar',
        'konsumsi_bahan_bakar_kota',
        'konsumsi_bahan_bakar_jalan',
        'panjang_mm',
        'lebar_mm',
        'tinggi_mm',
        'jarak_sumbu_roda_mm',
        'ground_clearance_mm',
        'berat_kosong_kg',
        'berat_isi_kg',
        'kapasitas_bagasi_l',
        'kapasitas_tangki_l',
        'akselerasi_0_100_kmh',
        'kecepatan_maksimal_kmh',
        'fitur_keamanan',
        'fitur_kenyamanan',
        'fitur_hiburan',
        'aktif',
    ];

    protected $casts = [
        'harga_otr' => 'decimal:2',
        'konsumsi_bahan_bakar_kota' => 'decimal:2',
        'konsumsi_bahan_bakar_jalan' => 'decimal:2',
        'akselerasi_0_100_kmh' => 'decimal:2',
        'fitur_keamanan' => 'array',
        'fitur_kenyamanan' => 'array',
        'fitur_hiburan' => 'array',
        'aktif' => 'boolean',
    ];

    // Relationships
    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class);
    }

    // Constants for enum values
    public const TIPE_MESIN = [
        'bensin' => 'Bensin',
        'diesel' => 'Diesel',
        'hybrid' => 'Hybrid',
        'listrik' => 'Listrik',
        'lpg' => 'LPG',
        'cng' => 'CNG',
    ];

    public const TRANSMISI = [
        'manual' => 'Manual',
        'automatic' => 'Automatic',
        'cvt' => 'CVT',
        'amt' => 'AMT',
        'dct' => 'DCT',
    ];

    // Accessor for formatted price
    public function getFormattedHargaOtrAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_otr, 0, ',', '.');
    }

    // Accessor for engine capacity with unit
    public function getKapasitasMesinAttribute(): string
    {
        return $this->kapasitas_mesin_cc ? $this->kapasitas_mesin_cc . ' cc' : '-';
    }

    // Accessor for power with unit
    public function getDayaAttribute(): string
    {
        return $this->daya_hp ? $this->daya_hp . ' HP' : '-';
    }

    // Accessor for torque with unit
    public function getTorsiAttribute(): string
    {
        return $this->torsi_nm ? $this->torsi_nm . ' Nm' : '-';
    }

    // Scope for active variants
    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }

    // Scope for specific engine type
    public function scopeByTipeMesin($query, $tipe)
    {
        return $query->where('tipe_mesin', $tipe);
    }

    // Scope for specific transmission
    public function scopeByTransmisi($query, $transmisi)
    {
        return $query->where('transmisi', $transmisi);
    }
}