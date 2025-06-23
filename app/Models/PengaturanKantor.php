<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengaturanKantor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengaturan_kantors';

    protected $fillable = [
        'nama_kantor',
        'alamat_kantor',
        'latitude',
        'longitude',
        'radius_meter',
        'jam_masuk',
        'jam_pulang',
        'toleransi_terlambat',
        'aktif',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meter' => 'integer',
        // Perbaikan: gunakan 'datetime' saja, bukan 'datetime:H:i'
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
        'toleransi_terlambat' => 'integer',
        'aktif' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Accessor untuk format jam yang lebih readable
    public function getJamMasukFormatAttribute()
    {
        return $this->jam_masuk ? $this->jam_masuk->format('H:i') : null;
    }

    public function getJamPulangFormatAttribute()
    {
        return $this->jam_pulang ? $this->jam_pulang->format('H:i') : null;
    }

    // Scope untuk kantor aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // Method untuk mendapatkan koordinat sebagai string
    public function getKoordinatAttribute()
    {
        return $this->latitude . ', ' . $this->longitude;
    }

    // Method untuk validasi apakah lokasi dalam radius
    public function isWithinRadius($userLatitude, $userLongitude)
    {
        $earthRadius = 6371000; // radius bumi dalam meter

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($userLatitude);
        $lonTo = deg2rad($userLongitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance <= $this->radius_meter;
    }
}