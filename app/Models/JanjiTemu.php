<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JanjiTemu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_pelanggan',
        'email_pelanggan',
        'telepon_pelanggan',
        'alamat_pelanggan',
        'stok_mobil_id',
        'karyawan_id',
        'waktu_mulai',
        'waktu_selesai',
        'jenis',
        'tujuan',
        'pesan_tambahan',
        'status',
        'catatan_internal',
        'lokasi',
        'metode',
        'waktu_alternatif',
        'tanggal_request',
        'tanggal_konfirmasi',
        'dikonfirmasi_oleh',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'tanggal_request' => 'datetime',
        'tanggal_konfirmasi' => 'datetime',
        'waktu_alternatif' => 'array',
    ];

    // Relasi ke stok mobil
    public function stokMobil(): BelongsTo
    {
        return $this->belongsTo(StokMobil::class);
    }

    // Relasi ke karyawan yang menangani
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Relasi ke karyawan yang konfirmasi
    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    // Scope untuk filter berdasarkan status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDikonfirmasi($query)
    {
        return $query->where('status', 'dikonfirmasi');
    }

    public function scopeTerjadwal($query)
    {
        return $query->where('status', 'terjadwal');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Scope untuk filter berdasarkan jenis
    public function scopeTestDrive($query)
    {
        return $query->where('jenis', 'test_drive');
    }

    public function scopeKonsultasi($query)
    {
        return $query->where('jenis', 'konsultasi');
    }

    // Scope untuk hari ini
    public function scopeHariIni($query)
    {
        return $query->whereDate('waktu_mulai', today());
    }

    // Scope untuk minggu ini
    public function scopeMingguIni($query)
    {
        return $query->whereBetween('waktu_mulai', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Accessor untuk format status
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'dikonfirmasi' => 'Dikonfirmasi',
            'terjadwal' => 'Terjadwal',
            'selesai' => 'Selesai',
            'batal' => 'Dibatalkan',
            'tidak_hadir' => 'Tidak Hadir',
            default => $this->status
        };
    }

    // Accessor untuk format jenis
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'test_drive' => 'Test Drive',
            'konsultasi' => 'Konsultasi',
            'negosiasi' => 'Negosiasi',
            'survey_mobil' => 'Survey Mobil',
            'lainnya' => 'Lainnya',
            default => $this->jenis
        };
    }

    // Method untuk cek apakah sudah lewat waktu
    public function isExpired(): bool
    {
        return $this->waktu_selesai < now() && in_array($this->status, ['pending', 'dikonfirmasi', 'terjadwal']);
    }

    // Method untuk konfirmasi janji temu
    public function konfirmasi(Karyawan $karyawan): void
    {
        $this->update([
            'status' => 'dikonfirmasi',
            'tanggal_konfirmasi' => now(),
            'dikonfirmasi_oleh' => $karyawan->id,
        ]);
    }

    // Method untuk assign karyawan
    public function assignKaryawan(Karyawan $karyawan): void
    {
        $this->update([
            'karyawan_id' => $karyawan->id,
            'status' => 'terjadwal',
        ]);
    }
}