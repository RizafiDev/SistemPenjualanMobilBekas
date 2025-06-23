<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PengajuanCuti;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function getAuthIdentifierName()
    {
        return 'nip';
    }
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }


    // Relationships
    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    public function pengajuanCutiMenunggu(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class)->where('status', PengajuanCuti::STATUS_MENUNGGU);
    }

    public function pengajuanCutiDisetujui(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class)->where('status', PengajuanCuti::STATUS_DISETUJUI);
    }

    public function pengajuanCutiDitolak(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class)->where('status', PengajuanCuti::STATUS_DITOLAK);
    }

    // Helper methods untuk cuti
    public function getTotalHariCutiTahunIni(): int
    {
        return $this->pengajuanCutiDisetujui()
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('jumlah_hari');
    }

    public function getSisaCutiTahunan(): int
    {
        $kuotaCutiTahunan = 12; // Default 12 hari per tahun
        $terpakai = $this->pengajuanCutiDisetujui()
            ->whereYear('tanggal_mulai', now()->year)
            ->where('jenis', PengajuanCuti::JENIS_TAHUNAN)
            ->sum('jumlah_hari');

        return max(0, $kuotaCutiTahunan - $terpakai);
    }

    public function hasPendingCuti(): bool
    {
        return $this->pengajuanCutiMenunggu()->exists();
    }

    public function penggajians(): HasMany
    {
        return $this->hasMany(Penggajian::class);
    }

    public function penggajianBulanIni(): HasOne
    {
        return $this->hasOne(Penggajian::class)
            ->where('periode', now()->format('Y-m'))
            ->latest();
    }

    public function penggajianTerbaru(): HasOne
    {
        return $this->hasOne(Penggajian::class)->latest();
    }

    // Helper methods untuk penggajian
    public function getTotalGajiTahunIni(): float
    {
        return $this->penggajians()
            ->whereYear('created_at', now()->year)
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->sum('gaji_bersih');
    }

    public function getRataRataGajiBersih(): float
    {
        return $this->penggajians()
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->avg('gaji_bersih') ?? 0;
    }

    public function hasPenggajianPeriode(string $periode): bool
    {
        return $this->penggajians()
            ->where('periode', $periode)
            ->exists();
    }
}