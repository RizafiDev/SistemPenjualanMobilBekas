<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggans';

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'no_telepon',
        'email',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        'pekerjaan',
        'perusahaan',
        'data_tambahan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'data_tambahan' => 'array',
    ];

    // Constants for enum values
    public const JENIS_KELAMIN = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];

    // Accessor for formatted gender
    public function getJenisKelaminLabelAttribute(): string
    {
        return self::JENIS_KELAMIN[$this->jenis_kelamin] ?? '-';
    }

    // Accessor for age calculation
    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir ? Carbon::parse($this->tanggal_lahir)->age : null;
    }

    // Accessor for formatted phone number
    public function getFormattedNoTeleponAttribute(): string
    {
        if (!$this->no_telepon)
            return '-';

        // Format Indonesian phone number
        $phone = preg_replace('/[^0-9]/', '', $this->no_telepon);

        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }

        return $this->no_telepon;
    }

    // Accessor for full contact info
    public function getKontakLengkapAttribute(): string
    {
        $kontak = [];

        if ($this->no_telepon) {
            $kontak[] = 'Tel: ' . $this->no_telepon;
        }

        if ($this->email) {
            $kontak[] = 'Email: ' . $this->email;
        }

        return implode(' | ', $kontak) ?: '-';
    }

    // Scope for searching by name, NIK, phone, or email
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('no_telepon', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Scope for filtering by gender
    public function scopeByJenisKelamin($query, $jenisKelamin)
    {
        return $query->where('jenis_kelamin', $jenisKelamin);
    }

    // Scope for filtering by age range
    public function scopeByAgeRange($query, $minAge, $maxAge)
    {
        $minDate = Carbon::now()->subYears($maxAge)->format('Y-m-d');
        $maxDate = Carbon::now()->subYears($minAge)->format('Y-m-d');

        return $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
    }

    // Check if customer has complete contact info
    public function hasCompleteContact(): bool
    {
        return !empty($this->no_telepon) && !empty($this->email);
    }

    // Check if customer has complete identity info
    public function hasCompleteIdentity(): bool
    {
        return !empty($this->nik) && !empty($this->tanggal_lahir) && !empty($this->alamat);
    }

    // Get completion percentage
    public function getCompletionPercentage(): int
    {
        $fields = [
            'nama_lengkap',
            'nik',
            'no_telepon',
            'email',
            'alamat',
            'tanggal_lahir',
            'jenis_kelamin',
            'pekerjaan'
        ];

        $filledFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        return round(($filledFields / count($fields)) * 100);
    }
}