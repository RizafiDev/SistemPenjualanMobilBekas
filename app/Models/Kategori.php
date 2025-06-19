<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Kategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategoris';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'ikon',
        'urutan_tampil',
        'unggulan',
    ];

    protected $casts = [
        'unggulan' => 'boolean',
        'urutan_tampil' => 'integer',
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

            // Auto set urutan_tampil jika belum diset
            if ($model->urutan_tampil === 0) {
                $maxUrutan = static::max('urutan_tampil') ?? 0;
                $model->urutan_tampil = $maxUrutan + 1;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama') && empty($model->slug)) {
                $model->slug = Str::slug($model->nama);
            }
        });
    }

    // Scope untuk data unggulan
    public function scopeUnggulan($query)
    {
        return $query->where('unggulan', true);
    }

    // Scope untuk data tidak unggulan
    public function scopeTidakUnggulan($query)
    {
        return $query->where('unggulan', false);
    }

    // Scope untuk ordering berdasarkan urutan tampil
    public function scopeByUrutan($query)
    {
        return $query->orderBy('urutan_tampil', 'asc');
    }

    // Method untuk mendapatkan status unggulan
    public function getStatusUnggulanAttribute()
    {
        return $this->unggulan ? 'Unggulan' : 'Biasa';
    }

    // Method untuk mendapatkan ikon dengan fallback
    public function getIkonDisplayAttribute()
    {
        return $this->ikon ?: 'heroicon-o-tag';
    }

    // Method untuk reorder urutan tampil
    public static function reorderUrutan(array $urutanBaru)
    {
        foreach ($urutanBaru as $urutan => $id) {
            static::where('id', $id)->update(['urutan_tampil' => $urutan + 1]);
        }
    }
}