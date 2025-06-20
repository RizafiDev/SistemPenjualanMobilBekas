<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'karyawans';

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'email',
        'password',
        'no_telepon',
        'jenis_kelamin',
        'tanggal_lahir',
        'nik',
        'jabatan',
        'departemen',
        'gaji_pokok',
        'tanggal_masuk',
        'status',
        'aktif',
        'alamat',
        'foto',
        'data_tambahan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'gaji_pokok' => 'decimal:2',
        'data_tambahan' => 'array',
        'password' => 'hashed',
    ];

    // Accessor untuk umur
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    // Accessor untuk masa kerja
    public function getMasaKerjaAttribute()
    {
        return $this->tanggal_masuk ? $this->tanggal_masuk->diffInYears(now()) : null;
    }

    // Scope untuk karyawan aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', 'aktif');
    }

    // Scope untuk karyawan non-aktif
    public function scopeNonAktif($query)
    {
        return $query->where('aktif', 'nonaktif');
    }

    // Scope berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope berdasarkan departemen
    public function scopeDepartemen($query, $departemen)
    {
        return $query->where('departemen', $departemen);
    }
}