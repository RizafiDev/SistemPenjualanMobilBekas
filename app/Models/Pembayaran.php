<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayarans';

    protected $fillable = [
        'penjualan_id',
        'no_kwitansi',
        'jumlah',
        'jenis',
        'keterangan',
        'metode',
        'bank',
        'no_referensi',
        'tanggal_bayar',
        'bukti_bayar',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    // Constants
    const JENIS = [
        'dp' => 'Down Payment',
        'cicilan' => 'Cicilan',
        'pelunasan' => 'Pelunasan',
        'tambahan' => 'Biaya Tambahan',
    ];

    const METODE = [
        'tunai' => 'Tunai',
        'transfer' => 'Transfer Bank',
        'debit' => 'Kartu Debit',
        'kredit' => 'Kartu Kredit',
        'ewallet' => 'E-Wallet',
        'cek' => 'Cek',
    ];

    const BANKS = [
        'bca' => 'BCA',
        'mandiri' => 'Mandiri',
        'bni' => 'BNI',
        'bri' => 'BRI',
        'cimb' => 'CIMB Niaga',
        'danamon' => 'Danamon',
        'permata' => 'Permata',
        'ocbc' => 'OCBC NISP',
        'maybank' => 'Maybank',
        'btpn' => 'BTPN',
        'bsi' => 'Bank Syariah Indonesia',
        'muamalat' => 'Muamalat',
        'mega' => 'Mega',
        'bukopin' => 'Bukopin',
        'panin' => 'Panin',
    ];

    // Relations
    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    // Accessors
    public function getFormattedJumlahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? '-';
    }

    public function getMetodeLabelAttribute(): string
    {
        return self::METODE[$this->metode] ?? '-';
    }

    public function getBankLabelAttribute(): string
    {
        return $this->bank ? (self::BANKS[$this->bank] ?? $this->bank) : '-';
    }

    public function getJenisBadgeColorAttribute(): string
    {
        return match ($this->jenis) {
            'dp' => 'warning',
            'cicilan' => 'info',
            'pelunasan' => 'success',
            'tambahan' => 'gray',
            default => 'gray',
        };
    }

    public function getMetodeBadgeColorAttribute(): string
    {
        return match ($this->metode) {
            'tunai' => 'success',
            'transfer' => 'info',
            'debit' => 'primary',
            'kredit' => 'warning',
            'ewallet' => 'purple',
            'cek' => 'gray',
            default => 'gray',
        };
    }

    // Mutators
    public function setNoKwitansiAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['no_kwitansi'] = $this->generateNoKwitansi();
        } else {
            $this->attributes['no_kwitansi'] = $value;
        }
    }

    // Methods
    public function generateNoKwitansi(): string
    {
        $date = Carbon::now();
        $prefix = 'KWT';
        $yearMonth = $date->format('Ym');

        // Gunakan microtime untuk uniqueness
        $microtime = (int) (microtime(true) * 1000); // Dalam milliseconds
        $uniqueId = substr($microtime, -4); // Ambil 4 digit terakhir

        $baseNumber = $prefix . $yearMonth . $uniqueId;

        // Jika masih ada duplikasi, tambahkan random number
        while (self::where('no_kwitansi', $baseNumber)->exists()) {
            $randomSuffix = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $baseNumber = $prefix . $yearMonth . $randomSuffix;
        }

        return $baseNumber;
    }

    public function isDP(): bool
    {
        return $this->jenis === 'dp';
    }

    public function isCicilan(): bool
    {
        return $this->jenis === 'cicilan';
    }

    public function isPelunasan(): bool
    {
        return $this->jenis === 'pelunasan';
    }

    public function isTambahan(): bool
    {
        return $this->jenis === 'tambahan';
    }

    public function requiresBank(): bool
    {
        return in_array($this->metode, ['transfer', 'debit', 'kredit']);
    }

    // Scopes
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeByMetode($query, $metode)
    {
        return $query->where('metode', $metode);
    }

    public function scopeByPenjualan($query, $penjualanId)
    {
        return $query->where('penjualan_id', $penjualanId);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal_bayar', $year)
            ->whereMonth('tanggal_bayar', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal_bayar', $year);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_bayar', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('tanggal_bayar', now()->year);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('no_kwitansi', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%")
                ->orWhere('no_referensi', 'like', "%{$search}%")
                ->orWhereHas('penjualan', function ($query) use ($search) {
                    $query->where('no_faktur', 'like', "%{$search}%");
                })
                ->orWhereHas('penjualan.pelanggan', function ($query) use ($search) {
                    $query->where('nama_lengkap', 'like', "%{$search}%");
                });
        });
    }

    // Event handlers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pembayaran) {
            if (empty($pembayaran->no_kwitansi)) {
                $pembayaran->no_kwitansi = $pembayaran->generateNoKwitansi();
            }
        });

        static::saved(function ($pembayaran) {
            // Update status penjualan berdasarkan total pembayaran
            $pembayaran->updatePenjualanStatus();
        });

        static::deleted(function ($pembayaran) {
            // Update status penjualan ketika pembayaran dihapus
            $pembayaran->updatePenjualanStatus();
        });
    }

    private function updatePenjualanStatus(): void
    {
        $penjualan = $this->penjualan;

        if (!$penjualan) {
            return;
        }

        $totalPembayaran = $penjualan->pembayarans()->sum('jumlah');
        $totalPenjualan = $penjualan->total;

        // Logika update status berdasarkan total pembayaran
        if ($totalPembayaran >= $totalPenjualan) {
            $penjualan->update(['status' => 'lunas']);
        } elseif ($totalPembayaran > 0) {
            // Jika ada pembayaran tapi belum lunas, set status berdasarkan metode pembayaran
            if ($penjualan->isKredit()) {
                $penjualan->update(['status' => 'kredit']);
            } else {
                $penjualan->update(['status' => 'booking']);
            }
        }
    }
}