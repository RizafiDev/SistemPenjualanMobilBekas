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
        return 'Rp ' . number_format((float) $this->harga_beli, 0, ',', '.');
    }

    public function getFormattedHargaJualAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->harga_jual, 0, ',', '.');
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
    // RELASI BARU UNTUK JANJI TEMU
    public function janjiTemus(): HasMany
    {
        return $this->hasMany(JanjiTemu::class);
    }

    // Relasi janji temu yang masih aktif (belum selesai/batal)
    public function janjiTemusAktif(): HasMany
    {
        return $this->hasMany(JanjiTemu::class)
            ->whereIn('status', ['pending', 'dikonfirmasi', 'terjadwal']);
    }

    // Relasi janji temu hari ini
    public function janjiTemusHariIni(): HasMany
    {
        return $this->hasMany(JanjiTemu::class)
            ->whereDate('waktu_mulai', today())
            ->whereIn('status', ['dikonfirmasi', 'terjadwal']);
    }

    // ACCESSOR & METHODS UNTUK JANJI TEMU

    // Cek apakah mobil sedang ada janji temu
    public function hasActiveAppointment(): bool
    {
        return $this->janjiTemusAktif()->exists();
    }

    // Cek apakah mobil ada janji temu hari ini
    public function hasTodayAppointment(): bool
    {
        return $this->janjiTemusHariIni()->exists();
    }

    // Get janji temu terdekat
    public function getNextAppointment()
    {
        return $this->janjiTemusAktif()
            ->where('waktu_mulai', '>=', now())
            ->orderBy('waktu_mulai')
            ->first();
    }

    // Get total janji temu untuk mobil ini
    public function getTotalAppointments(): int
    {
        return $this->janjiTemus()->count();
    }

    // Get jumlah test drive yang sudah dilakukan
    public function getTotalTestDrives(): int
    {
        return $this->janjiTemus()
            ->where('jenis', 'test_drive')
            ->where('status', 'selesai')
            ->count();
    }

    // SCOPE UNTUK FILTER BERDASARKAN JANJI TEMU

    // Mobil yang tersedia untuk janji temu (tidak ada janji aktif)
    public function scopeAvailableForAppointment($query)
    {
        return $query->whereDoesntHave('janjiTemusAktif');
    }

    // Mobil yang sedang ada janji temu
    public function scopeWithActiveAppointment($query)
    {
        return $query->whereHas('janjiTemusAktif');
    }

    // Mobil yang sering di-test drive
    public function scopePopularForTestDrive($query, $limit = 10)
    {
        return $query->withCount([
            'janjiTemus as test_drive_count' => function ($query) {
                $query->where('jenis', 'test_drive')->where('status', 'selesai');
            }
        ])
            ->orderBy('test_drive_count', 'desc')
            ->limit($limit);
    }

    // ACCESSOR UNTUK DISPLAY

    // Nama lengkap mobil untuk display
    public function getNamaLengkapAttribute(): string
    {
        $mobil = $this->mobil;
        $varian = $this->varian;

        // Ambil nama merek dari relasi mobil->merek->nama
        $merek = $mobil?->merek?->nama ?? '';
        $model = $mobil?->nama ?? '';
        $varianNama = $varian?->nama ?? '';

        // Gabungkan dengan format yang konsisten
        return trim("{$merek} {$model} {$varianNama}");
    }

    // Get first available foto kondisi for catalog display
    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto_kondisi)) {
            return null;
        }

        $fotoKondisi = is_array($this->foto_kondisi) ? $this->foto_kondisi : json_decode((string) $this->foto_kondisi, true);

        if (empty($fotoKondisi) || !is_array($fotoKondisi)) {
            return null;
        }

        // Get first available photo from foto_kondisi
        $firstPhoto = reset($fotoKondisi);

        if (is_string($firstPhoto) && !empty($firstPhoto)) {
            // If it's already a full URL, return as is
            if (str_starts_with($firstPhoto, 'http://') || str_starts_with($firstPhoto, 'https://')) {
                return $firstPhoto;
            }

            // If it starts with storage/, prefix with the storage URL
            if (str_starts_with($firstPhoto, 'storage/')) {
                return url($firstPhoto);
            }

            // Otherwise, assume it's a path relative to storage/
            return url('storage/' . $firstPhoto);
        }

        return null;
    }

    // Status availability untuk janji temu
    public function getAvailabilityStatusAttribute(): string
    {
        if ($this->status !== 'tersedia') {
            return 'Tidak Tersedia';
        }

        if ($this->hasActiveAppointment()) {
            return 'Ada Janji Temu';
        }

        return 'Tersedia';
    }

    // Warna badge untuk status availability
    public function getAvailabilityColorAttribute(): string
    {
        return match ($this->availability_status) {
            'Tersedia' => 'success',
            'Ada Janji Temu' => 'warning',
            'Tidak Tersedia' => 'danger',
            default => 'gray'
        };
    }
}
