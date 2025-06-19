<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatServis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'riwayat_servis';

    protected $fillable = [
        'stok_mobil_id',
        'tanggal_servis',
        'jenis_servis',
        'tempat_servis',
        'deskripsi',
        'biaya',
        'kilometer_servis',
        'foto_bukti',
        'sparepart',
    ];

    protected $casts = [
        'tanggal_servis' => 'date',
        'biaya' => 'decimal:2',
        'foto_bukti' => 'array',
        'sparepart' => 'array',
    ];

    public function stokMobil(): BelongsTo
    {
        return $this->belongsTo(StokMobil::class);
    }

    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }
}
