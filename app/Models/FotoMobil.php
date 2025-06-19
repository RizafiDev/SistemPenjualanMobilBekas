<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class FotoMobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mobil_id',
        'path_file',
        'jenis_media',
        'jenis_gambar',
        'urutan_tampil',
        'teks_alternatif',
        'keterangan',
    ];

    protected $casts = [
        'urutan_tampil' => 'integer',
    ];

    /**
     * Relasi ke model Mobil
     */
    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class);
    }

    /**
     * Bulk create method untuk efisiensi
     */
    public static function bulkCreate(array $data): int
    {
        $records = [];
        $timestamp = now();

        foreach ($data as $item) {
            $records[] = array_merge($item, [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        return self::insert($records);
    }

    /**
     * Method untuk auto-generate urutan tampil untuk bulk insert
     */
    public static function getNextOrderNumbers(int $mobilId, string $jenisMedia, int $count): array
    {
        $maxOrder = self::where('mobil_id', $mobilId)
            ->where('jenis_media', $jenisMedia)
            ->max('urutan_tampil') ?? 0;

        return range($maxOrder + 1, $maxOrder + $count);
    }

    /**
     * Accessor untuk mendapatkan URL file
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->path_file);
    }

    /**
     * Accessor untuk mendapatkan nama file
     */
    public function getFileNameAttribute(): string
    {
        return basename($this->path_file);
    }

    /**
     * Accessor untuk mendapatkan ukuran file
     */
    public function getFileSizeAttribute(): string
    {
        $filePath = storage_path('app/public/' . $this->path_file);
        if (file_exists($filePath)) {
            $bytes = filesize($filePath);
            return $this->formatBytes($bytes);
        }
        return 'Unknown';
    }

    /**
     * Helper method untuk format bytes
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Accessor untuk mendapatkan ekstensi file
     */
    public function getFileExtensionAttribute(): string
    {
        return strtoupper(pathinfo($this->path_file, PATHINFO_EXTENSION));
    }

    /**
     * Accessor untuk cek apakah file adalah gambar
     */
    public function getIsImageAttribute(): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(strtolower($this->file_extension), $imageExtensions);
    }

    /**
     * Accessor untuk cek apakah file adalah video
     */
    public function getIsVideoAttribute(): bool
    {
        $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'webm'];
        return in_array(strtolower($this->file_extension), $videoExtensions);
    }

    /**
     * Scope untuk filter berdasarkan jenis media
     */
    public function scopeByJenisMedia($query, string $jenisMedia)
    {
        return $query->where('jenis_media', $jenisMedia);
    }

    /**
     * Scope untuk filter berdasarkan jenis gambar
     */
    public function scopeByJenisGambar($query, string $jenisGambar)
    {
        return $query->where('jenis_gambar', $jenisGambar);
    }

    /**
     * Scope untuk mengurutkan berdasarkan urutan tampil
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan_tampil', 'asc');
    }

    /**
     * Scope untuk hanya gambar
     */
    public function scopeGambar($query)
    {
        return $query->where('jenis_media', 'gambar');
    }

    /**
     * Scope untuk hanya video
     */
    public function scopeVideo($query)
    {
        return $query->where('jenis_media', 'video');
    }

    /**
     * Scope untuk hanya brosur
     */
    public function scopeBrosur($query)
    {
        return $query->where('jenis_media', 'brosur');
    }

    /**
     * Scope untuk thumbnail
     */
    public function scopeThumbnail($query)
    {
        return $query->where('jenis_gambar', 'thumbnail');
    }

    /**
     * Boot method untuk auto-increment urutan tampil (hanya untuk single create)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Skip auto-increment jika sudah ada urutan tampil
            if (empty($model->urutan_tampil)) {
                $maxOrder = static::where('mobil_id', $model->mobil_id)
                    ->where('jenis_media', $model->jenis_media)
                    ->max('urutan_tampil');
                $model->urutan_tampil = ($maxOrder ?? 0) + 1;
            }
        });

        // Event untuk cleanup file ketika record dihapus
        static::deleting(function ($model) {
            if ($model->path_file && Storage::exists('public/' . $model->path_file)) {
                Storage::delete('public/' . $model->path_file);
            }
        });
    }

    /**
     * Method untuk reorder urutan tampil
     */
    public static function reorderItems(int $mobilId, string $jenisMedia): void
    {
        $items = self::where('mobil_id', $mobilId)
            ->where('jenis_media', $jenisMedia)
            ->orderBy('urutan_tampil')
            ->get();

        foreach ($items as $index => $item) {
            $item->update(['urutan_tampil' => $index + 1]);
        }
    }
}