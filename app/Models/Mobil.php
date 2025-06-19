<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\FotoMobil;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mobils';

    protected $fillable = [
        'nama',
        'slug',
        'merek_id',
        'kategori_id',
        'tahun_mulai',
        'tahun_akhir',
        'kapasitas_penumpang',
        'tipe_bodi',
        'status',
        'deskripsi',
        'fitur_unggulan',
    ];

    protected $casts = [
        'tahun_mulai' => 'integer',
        'tahun_akhir' => 'integer',
        'kapasitas_penumpang' => 'integer',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Constants untuk enum values
    const TIPE_BODI = [
        'sedan' => 'Sedan',
        'hatchback' => 'Hatchback',
        'suv' => 'SUV',
        'mpv' => 'MPV',
        'pickup' => 'Pickup',
        'coupe' => 'Coupe',
        'convertible' => 'Convertible',
        'wagon' => 'Wagon',
    ];

    const STATUS = [
        'aktif' => 'Aktif',
        'dihentikan' => 'Dihentikan',
        'akan_datang' => 'Akan Datang',
    ];

    // Boot method untuk auto generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nama);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama') && empty($model->slug)) {
                $model->slug = Str::slug($model->nama);
            }
        });
    }

    // Relationships
    public function merek(): BelongsTo
    {
        return $this->belongsTo(Merek::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDihentikan($query)
    {
        return $query->where('status', 'dihentikan');
    }

    public function scopeAkanDatang($query)
    {
        return $query->where('status', 'akan_datang');
    }

    public function scopeByMerek($query, $merekId)
    {
        return $query->where('merek_id', $merekId);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    public function scopeByTipeBodi($query, $tipeBodi)
    {
        return $query->where('tipe_bodi', $tipeBodi);
    }

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun_mulai', '<=', $tahun)
            ->where(function ($q) use ($tahun) {
                $q->whereNull('tahun_akhir')
                    ->orWhere('tahun_akhir', '>=', $tahun);
            });
    }

    // Accessors
    public function getTipeBodiBadgeAttribute()
    {
        return self::TIPE_BODI[$this->tipe_bodi] ?? $this->tipe_bodi;
    }

    public function getStatusBadgeAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getNamaLengkapAttribute()
    {
        return $this->merek->nama . ' ' . $this->nama;
    }

    public function getRentangTahunAttribute()
    {
        if ($this->tahun_akhir) {
            return $this->tahun_mulai . ' - ' . $this->tahun_akhir;
        }
        return $this->tahun_mulai . ' - Sekarang';
    }

    public function getIsAktifAttribute()
    {
        return $this->status === 'aktif';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'aktif' => 'success',
            'dihentikan' => 'danger',
            'akan_datang' => 'warning',
            default => 'gray',
        };
    }

    public function getTipeBodiBadgeColorAttribute()
    {
        return match ($this->tipe_bodi) {
            'sedan' => 'blue',
            'hatchback' => 'green',
            'suv' => 'orange',
            'mpv' => 'purple',
            'pickup' => 'red',
            'coupe' => 'pink',
            'convertible' => 'yellow',
            'wagon' => 'indigo',
            default => 'gray',
        };
    }

    // Helper methods
    public function isMasihProduksi()
    {
        return is_null($this->tahun_akhir) && $this->status === 'aktif';
    }

    public function getSudahDihentikan()
    {
        return !is_null($this->tahun_akhir) || $this->status === 'dihentikan';
    }
    public function fotoMobils(): HasMany
    {
        return $this->hasMany(FotoMobil::class);
    }

    /**
     * Relasi untuk mendapatkan foto gambar saja, diurutkan berdasarkan urutan tampil
     */
    public function gambarMobils(): HasMany
    {
        return $this->hasMany(FotoMobil::class)
            ->where('jenis_media', 'gambar')
            ->orderBy('urutan_tampil', 'asc');
    }

    /**
     * Relasi untuk mendapatkan video mobil
     */
    public function videoMobils(): HasMany
    {
        return $this->hasMany(FotoMobil::class)
            ->where('jenis_media', 'video')
            ->orderBy('urutan_tampil', 'asc');
    }

    /**
     * Relasi untuk mendapatkan brosur mobil
     */
    public function brosurMobils(): HasMany
    {
        return $this->hasMany(FotoMobil::class)
            ->where('jenis_media', 'brosur')
            ->orderBy('urutan_tampil', 'asc');
    }

    /**
     * Relasi untuk mendapatkan thumbnail mobil
     */
    public function thumbnailMobil(): HasMany
    {
        return $this->hasMany(FotoMobil::class)
            ->where('jenis_media', 'gambar')
            ->where('jenis_gambar', 'thumbnail')
            ->orderBy('urutan_tampil', 'asc');
    }

    /**
     * Accessor untuk mendapatkan thumbnail utama
     */
    public function getThumbnailUtamaAttribute()
    {
        return $this->thumbnailMobil()->first();
    }

    /**
     * Accessor untuk mendapatkan semua foto eksterior
     */
    public function getFotoEksteriorAttribute()
    {
        return $this->gambarMobils()
            ->where('jenis_gambar', 'eksterior')
            ->get();
    }

    /**
     * Accessor untuk mendapatkan semua foto interior
     */
    public function getFotoInteriorAttribute()
    {
        return $this->gambarMobils()
            ->where('jenis_gambar', 'interior')
            ->get();
    }

    /**
     * Accessor untuk mendapatkan semua foto fitur
     */
    public function getFotoFiturAttribute()
    {
        return $this->gambarMobils()
            ->where('jenis_gambar', 'fitur')
            ->get();
    }

    /**
     * Accessor untuk mendapatkan galeri foto
     */
    public function getGaleriFotoAttribute()
    {
        return $this->gambarMobils()
            ->where('jenis_gambar', 'galeri')
            ->get();
    }

    /**
     * Method untuk mendapatkan foto berdasarkan jenis
     */
    public function getFotoByJenis(string $jenisGambar)
    {
        return $this->gambarMobils()
            ->where('jenis_gambar', $jenisGambar)
            ->get();
    }

    /**
     * Method untuk mendapatkan semua media (gambar, video, brosur)
     */
    public function getAllMedia()
    {
        return $this->fotoMobils()
            ->orderBy('jenis_media', 'asc')
            ->orderBy('urutan_tampil', 'asc')
            ->get();
    }
}