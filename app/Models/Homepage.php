<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Homepage extends Model
{
    use HasFactory;

    protected $table = 'homepages';
    protected $fillable = [
        'pelanggan_puas',
        'rating_puas',
        'foto_homepage',
    ];

    protected $casts = [
        'foto_homepage' => 'array',
    ];

    // Tambahkan method untuk cek apakah sudah ada data
    public static function canCreate(): bool
    {
        return static::count() === 0;
    }

    // Override boot method untuk validasi
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (static::count() > 0) {
                throw new \Exception('Hanya boleh ada satu data homepage!');
            }
        });
    }
}
