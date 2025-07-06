<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Accessors - Perbaiki accessor
    public function getTotalPembayaranAttribute(): float
    {
        return (float) $this->pembayarans()->sum('jumlah');
    }

    public function getSisaPembayaranAttribute(): float
    {
        return (float) ($this->total - $this->total_pembayaran);
    }

    public function getPersentaseProgressPembayaranAttribute(): float
    {
        if ($this->total == 0) {
            return 0;
        }
        return ($this->total_pembayaran / $this->total) * 100;
    }

    // Methods - Perbaiki method yang error
    public function isPaidOff(): bool
    {
        return $this->total_pembayaran >= $this->total;
    }

    public function hasPartialPayment(): bool
    {
        return $this->total_pembayaran > 0 && $this->total_pembayaran < $this->total;
    }

    public function getSisaBayar(): float
    {
        if ($this->isKredit()) {
            return (float) ($this->total - ($this->uang_muka ?? 0));
        }
        return $this->sisa_pembayaran;
    }

    public function getLabaKotor(): float
    {
        $hargaBeli = $this->stokMobil->harga_beli ?? 0;
        return (float) ($this->harga_jual - $hargaBeli);
    }

    // Perbaiki scope yang error
    public function scopePaidOff($query)
    {
        return $query->whereExists(function ($subquery) {
            $subquery->selectRaw('1')
                ->from('pembayarans')
                ->whereColumn('pembayarans.penjualan_id', 'penjualans.id')
                ->groupBy('penjualan_id')
                ->havingRaw('SUM(jumlah) >= penjualans.total');
        });
    }

    public function scopePartialPayment($query)
    {
        return $query->whereExists(function ($subquery) {
            $subquery->selectRaw('1')
                ->from('pembayarans')
                ->whereColumn('pembayarans.penjualan_id', 'penjualans.id')
                ->groupBy('penjualan_id')
                ->havingRaw('SUM(jumlah) > 0 AND SUM(jumlah) < penjualans.total');
        });
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

    public function scopeWithPayments($query)
    {
        return $query->with([
            'pembayarans' => function ($query) {
                $query->orderBy('tanggal_bayar', 'desc');
            }
        ]);
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

    // Tambahkan method ini ke model Penjualan
    public function isKredit(): bool
    {
        return $this->metode_pembayaran === 'kredit';
    }

    public function isLeasing(): bool
    {
        return $this->metode_pembayaran === 'leasing';
    }

    public function isTunai(): bool
    {
        return $this->metode_pembayaran === 'tunai';
    }

    public function isTradeIn(): bool
    {
        return $this->metode_pembayaran === 'trade_in';
    }

    // Tambahkan method ini setelah method isTradeIn()
    public function requiresInstallment(): bool
    {
        return in_array($this->metode_pembayaran, ['kredit', 'leasing']);
    }

    // Method untuk mendapatkan tanggal pembayaran terakhir
    public function getLastPaymentDate(): ?Carbon
    {
        $lastPayment = $this->pembayarans()
            ->orderBy('tanggal_bayar', 'desc')
            ->first();

        return $lastPayment ? $lastPayment->tanggal_bayar : null;
    }

    // Method untuk mendapatkan pembayaran terakhir
    public function getLastPayment(): ?Pembayaran
    {
        return $this->pembayarans()
            ->orderBy('tanggal_bayar', 'desc')
            ->first();
    }

    // Method untuk mendapatkan pembayaran pertama (DP)
    public function getFirstPayment(): ?Pembayaran
    {
        return $this->pembayarans()
            ->orderBy('tanggal_bayar', 'asc')
            ->first();
    }

    // Method untuk mendapatkan total pembayaran DP
    public function getTotalDP(): float
    {
        return (float) $this->pembayarans()
            ->where('jenis', 'dp')
            ->sum('jumlah');
    }

    // Method untuk mendapatkan total cicilan
    public function getTotalCicilan(): float
    {
        return (float) $this->pembayarans()
            ->where('jenis', 'cicilan')
            ->sum('jumlah');
    }

    // Method untuk mendapatkan total pelunasan
    public function getTotalPelunasan(): float
    {
        return (float) $this->pembayarans()
            ->where('jenis', 'pelunasan')
            ->sum('jumlah');
    }

    // Method untuk cek apakah sudah ada DP
    public function hasDP(): bool
    {
        return $this->pembayarans()
            ->where('jenis', 'dp')
            ->exists();
    }

    // Method untuk cek apakah sudah lunas
    public function isFullyPaid(): bool
    {
        return $this->isPaidOff();
    }

    // Method untuk mendapatkan jumlah cicilan yang sudah dibayar
    public function getPaidInstallments(): int
    {
        return $this->pembayarans()
            ->where('jenis', 'cicilan')
            ->count();
    }

    // Method untuk mendapatkan sisa cicilan (jika kredit)
    public function getRemainingInstallments(): int
    {
        if (!$this->requiresInstallment() || !$this->tenor_bulan) {
            return 0;
        }

        $paidInstallments = $this->getPaidInstallments();
        return max(0, $this->tenor_bulan - $paidInstallments);
    }

    // Method untuk mendapatkan status pembayaran dalam bentuk text
    public function getPaymentStatusText(): string
    {
        if ($this->isFullyPaid()) {
            return 'Lunas';
        } elseif ($this->hasPartialPayment()) {
            if ($this->hasDP()) {
                return 'DP Sudah Dibayar';
            } else {
                return 'Pembayaran Sebagian';
            }
        } else {
            return 'Belum Ada Pembayaran';
        }
    }

    // Method untuk generate nomor faktur
    public static function generateNoFaktur(): string
{
    $date = Carbon::now();
    $prefix = 'INV';
    $yearMonth = $date->format('Ym');

    return \DB::transaction(function () use ($prefix, $yearMonth) {
        $lastPenjualan = self::where('no_faktur', 'like', $prefix . $yearMonth . '%')
            ->lockForUpdate()
            ->orderBy('no_faktur', 'desc')
            ->first();

        if ($lastPenjualan) {
            $lastNumber = (int) substr($lastPenjualan->no_faktur, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $newNoFaktur = $prefix . $yearMonth . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Double check untuk memastikan nomor belum ada
        while (self::where('no_faktur', $newNoFaktur)->exists()) {
            $newNumber++;
            $newNoFaktur = $prefix . $yearMonth . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $newNoFaktur;
    });
}

    // Method untuk mendapatkan ringkasan pembayaran
    public function getPaymentSummary(): array
    {
        return [
            'total_harga' => $this->total,
            'total_pembayaran' => $this->total_pembayaran,
            'sisa_pembayaran' => $this->sisa_pembayaran,
            'persentase_progress' => $this->persentase_progress_pembayaran,
            'jumlah_pembayaran' => $this->pembayarans()->count(),
            'total_dp' => $this->getTotalDP(),
            'total_cicilan' => $this->getTotalCicilan(),
            'total_pelunasan' => $this->getTotalPelunasan(),
            'tanggal_pembayaran_terakhir' => $this->getLastPaymentDate(),
            'status_pembayaran' => $this->getPaymentStatusText(),
            'is_lunas' => $this->isFullyPaid(),
            'has_dp' => $this->hasDP(),
        ];
    }
    public function getNamaMobilLengkapAttribute(): string
    {
        if (!$this->stokMobil) {
            return 'Mobil tidak ditemukan';
        }

        return $this->stokMobil->nama_lengkap ?? 'Mobil tidak lengkap';
    }

    public function getMerekMobilAttribute(): string
    {
        return $this->stokMobil?->mobil?->merek?->nama ?? 'Tidak ada merek';
    }

    public function getModelMobilAttribute(): string
    {
        return $this->stokMobil?->mobil?->nama ?? 'Tidak ada model';
    }

    public function getVarianMobilAttribute(): string
    {
        return $this->stokMobil?->varian?->nama ?? 'Tidak ada varian';
    }
}
