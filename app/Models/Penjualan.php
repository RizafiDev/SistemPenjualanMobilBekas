<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penjualans';

    protected $fillable = [
        'no_faktur',
        'stok_mobil_id',
        'pelanggan_id',
        'karyawan_id',
        'harga_jual',
        'diskon',
        'ppn',
        'biaya_tambahan',
        'total',
        'metode_pembayaran',
        'leasing_bank',
        'tenor_bulan',
        'uang_muka',
        'cicilan_bulanan',
        'trade_in',
        'dokumen',
        'tanggal_penjualan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'diskon' => 'decimal:2',
        'ppn' => 'decimal:2',
        'biaya_tambahan' => 'decimal:2',
        'total' => 'decimal:2',
        'uang_muka' => 'decimal:2',
        'cicilan_bulanan' => 'decimal:2',
        'trade_in' => 'array',
        'dokumen' => 'array',
        'tanggal_penjualan' => 'date',
    ];

    // Constants
    const METODE_PEMBAYARAN = [
        'tunai' => 'Tunai',
        'kredit' => 'Kredit',
        'leasing' => 'Leasing',
        'trade_in' => 'Trade In',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'booking' => 'Booking',
        'lunas' => 'Lunas',
        'kredit' => 'Kredit',
        'batal' => 'Batal',
    ];

    const LEASING_BANKS = [
        'bca_finance' => 'BCA Finance',
        'mandiri_finance' => 'Mandiri Finance',
        'bni_finance' => 'BNI Finance',
        'bri_finance' => 'BRI Finance',
        'mega_finance' => 'Mega Finance',
        'oto_finance' => 'OTO Finance',
        'adira_finance' => 'Adira Finance',
        'acc_finance' => 'ACC Finance',
        'wom_finance' => 'WOM Finance',
        'clipan_finance' => 'Clipan Finance',
    ];

    // Relations
    public function stokMobil(): BelongsTo
    {
        return $this->belongsTo(StokMobil::class);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Accessors
    public function getFormattedHargaJualAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    public function getFormattedDiskonAttribute(): string
    {
        return 'Rp ' . number_format($this->diskon, 0, ',', '.');
    }

    public function getFormattedPpnAttribute(): string
    {
        return 'Rp ' . number_format($this->ppn, 0, ',', '.');
    }

    public function getFormattedBiayaTambahanAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_tambahan, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedUangMukaAttribute(): string
    {
        return $this->uang_muka ? 'Rp ' . number_format($this->uang_muka, 0, ',', '.') : '-';
    }

    public function getFormattedCicilanBulananAttribute(): string
    {
        return $this->cicilan_bulanan ? 'Rp ' . number_format($this->cicilan_bulanan, 0, ',', '.') : '-';
    }

    public function getMetodePembayaranLabelAttribute(): string
    {
        return self::METODE_PEMBAYARAN[$this->metode_pembayaran] ?? '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? '-';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'booking' => 'warning',
            'lunas' => 'success',
            'kredit' => 'info',
            'batal' => 'danger',
            default => 'gray',
        };
    }

    public function getLeasingBankLabelAttribute(): string
    {
        return $this->leasing_bank ? (self::LEASING_BANKS[$this->leasing_bank] ?? $this->leasing_bank) : '-';
    }

    // Mutators
    public function setNoFakturAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['no_faktur'] = $this->generateNoFaktur();
        } else {
            $this->attributes['no_faktur'] = $value;
        }
    }

    // Methods
    public function generateNoFaktur(): string
    {
        $date = Carbon::now();
        $prefix = 'PJ';
        $yearMonth = $date->format('Ym');

        $lastPenjualan = self::where('no_faktur', 'like', $prefix . $yearMonth . '%')
            ->orderBy('no_faktur', 'desc')
            ->first();

        if ($lastPenjualan) {
            $lastNumber = (int) substr($lastPenjualan->no_faktur, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $yearMonth . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->harga_jual - $this->diskon;
        $this->total = $subtotal + $this->ppn + $this->biaya_tambahan;
    }

    public function calculatePpn(float $rate = 0.11): void
    {
        $subtotal = $this->harga_jual - $this->diskon;
        $this->ppn = $subtotal * $rate;
    }

    public function isKredit(): bool
    {
        return in_array($this->metode_pembayaran, ['kredit', 'leasing']);
    }

    public function hasTradeIn(): bool
    {
        return !empty($this->trade_in);
    }

    public function getTradeInValue(): float
    {
        if (!$this->hasTradeIn()) {
            return 0;
        }

        return $this->trade_in['nilai_trade_in'] ?? 0;
    }

    public function getSisaBayar(): float
    {
        if (!$this->isKredit()) {
            return 0;
        }

        return $this->total - ($this->uang_muka ?? 0);
    }

    public function getLabaKotor(): float
    {
        $hargaBeli = $this->stokMobil->harga_beli ?? 0;
        return $this->harga_jual - $hargaBeli;
    }

    public function getPersentaseDiskon(): float
    {
        if ($this->harga_jual == 0) {
            return 0;
        }

        return ($this->diskon / $this->harga_jual) * 100;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByMetodePembayaran($query, $metode)
    {
        return $query->where('metode_pembayaran', $metode);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_penjualan', [$startDate, $endDate]);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal_penjualan', $year)
            ->whereMonth('tanggal_penjualan', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal_penjualan', $year);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_penjualan', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_penjualan', now()->month)
            ->whereYear('tanggal_penjualan', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('tanggal_penjualan', now()->year);
    }

    public function scopeWithSales($query)
    {
        return $query->whereNotNull('karyawan_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('no_faktur', 'like', "%{$search}%")
                ->orWhereHas('pelanggan', function ($query) use ($search) {
                    $query->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->orWhereHas('stokMobil.mobil', function ($query) use ($search) {
                    $query->where('merk', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
        });
    }

    // Event handlers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($penjualan) {
            if (empty($penjualan->no_faktur)) {
                $penjualan->no_faktur = $penjualan->generateNoFaktur();
            }
        });

        static::saved(function ($penjualan) {
            // Update status stok mobil ketika penjualan berubah
            if ($penjualan->isDirty('status')) {
                $penjualan->updateStokMobilStatus();
            }
        });
    }

    private function updateStokMobilStatus(): void
    {
        $stokMobil = $this->stokMobil;

        if (!$stokMobil) {
            return;
        }

        switch ($this->status) {
            case 'booking':
                $stokMobil->update(['status' => 'booking']);
                break;
            case 'lunas':
            case 'kredit':
                $stokMobil->update([
                    'status' => 'terjual',
                    'tanggal_keluar' => $this->tanggal_penjualan
                ]);
                break;
            case 'batal':
                $stokMobil->update(['status' => 'tersedia']);
                break;
        }
    }
}