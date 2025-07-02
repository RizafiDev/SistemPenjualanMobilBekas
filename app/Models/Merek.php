<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Merek extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mereks';

    protected $fillable = [
        'nama',
        'slug',
        'logo',
        'negara_asal',
        'deskripsi',
        'tahun_berdiri',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'tahun_berdiri' => 'integer',
    ];

    protected $dates = [
        'deleted_at',
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

    // Accessor untuk URL logo
    public function getLogoUrlAttribute()
    {
        if ($this->logo && \Storage::exists($this->logo)) {
            return \Storage::url($this->logo);
        }
        return null;
    }

    // Scope untuk data aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // Scope untuk data tidak aktif
    public function scopeTidakAktif($query)
    {
        return $query->where('aktif', false);
    }

    // Method untuk mendapatkan status
    public function getStatusAttribute()
    {
        return $this->aktif ? 'Aktif' : 'Tidak Aktif';
    }
    public function mobils(): HasMany
    {
        return $this->hasMany(Mobil::class);
    }
}