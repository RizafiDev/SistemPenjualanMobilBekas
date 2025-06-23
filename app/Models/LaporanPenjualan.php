<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Penjualan;
use App\Models\Merek;
use App\Models\Mobil;
use App\Models\Kategori;
use App\Models\Karyawan;
use Carbon\Carbon;

class LaporanPenjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'tahun',
        'bulan',
        'minggu',
        'total_penjualan',
        'total_nilai_penjualan',
        'rata_rata_penjualan',
        'penjualan_tunai',
        'penjualan_kredit',
        'top_merek',
        'top_model',
        'top_kategori',
        'top_sales',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tahun' => 'integer',
        'bulan' => 'integer',
        'minggu' => 'integer',
        'total_penjualan' => 'integer',
        'total_nilai_penjualan' => 'decimal:2',
        'rata_rata_penjualan' => 'decimal:2',
        'penjualan_tunai' => 'integer',
        'penjualan_kredit' => 'integer',
        'top_merek' => 'array',
        'top_model' => 'array',
        'top_kategori' => 'array',
        'top_sales' => 'array',
    ];

    // Scope untuk filtering
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    public function scopeByMinggu($query, $minggu)
    {
        return $query->where('minggu', $minggu);
    }

    public function scopeByPeriode($query, $tanggalMulai, $tanggalSelesai)
    {
        return $query->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
    }

    // Accessor untuk format mata uang
    public function getFormattedTotalNilaiPenjualanAttribute()
    {
        return 'Rp ' . number_format($this->total_nilai_penjualan, 0, ',', '.');
    }

    public function getFormattedRataRataPenjualanAttribute()
    {
        return 'Rp ' . number_format($this->rata_rata_penjualan, 0, ',', '.');
    }

    // Accessor untuk nama bulan
    public function getNamaBulanAttribute()
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $namaBulan[$this->bulan] ?? '';
    }

    // Accessor untuk persentase penjualan tunai
    public function getPersentaseTunaiAttribute()
    {
        if ($this->total_penjualan == 0)
            return 0;
        return round(($this->penjualan_tunai / $this->total_penjualan) * 100, 2);
    }

    // Accessor untuk persentase penjualan kredit
    public function getPersentaseKreditAttribute()
    {
        if ($this->total_penjualan == 0)
            return 0;
        return round(($this->penjualan_kredit / $this->total_penjualan) * 100, 2);
    }

    // Method untuk mendapatkan top merek dengan format yang rapi
    public function getTopMerekFormatted()
    {
        if (!$this->top_merek)
            return '-';

        $result = [];
        foreach ($this->top_merek as $merek) {
            $result[] = $merek['nama'] . ' (' . $merek['jumlah'] . ' unit)';
        }

        return implode(', ', $result);
    }

    // Method untuk mendapatkan top model dengan format yang rapi
    public function getTopModelFormatted()
    {
        if (!$this->top_model)
            return '-';

        $result = [];
        foreach ($this->top_model as $model) {
            $result[] = $model['nama'] . ' (' . $model['jumlah'] . ' unit)';
        }

        return implode(', ', $result);
    }

    // Method untuk mendapatkan top kategori dengan format yang rapi
    public function getTopKategoriFormatted()
    {
        if (!$this->top_kategori)
            return '-';

        $result = [];
        foreach ($this->top_kategori as $kategori) {
            $result[] = $kategori['nama'] . ' (' . $kategori['jumlah'] . ' unit)';
        }

        return implode(', ', $result);
    }

    // Method untuk mendapatkan top sales dengan format yang rapi
    public function getTopSalesFormatted()
    {
        if (!$this->top_sales)
            return '-';

        $result = [];
        foreach ($this->top_sales as $sales) {
            $result[] = $sales['nama'] . ' (' . $sales['jumlah'] . ' penjualan)';
        }

        return implode(', ', $result);
    }

    // Static method untuk membuat laporan
    public static function generateLaporan($periode = 'harian', $customStart = null, $customEnd = null)
    {
        // Tentukan rentang tanggal
        $now = now();
        switch ($periode) {
            case 'harian':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case '3_hari':
                $start = $now->copy()->subDays(2)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'mingguan':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'bulanan':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case '6_bulan':
                $start = $now->copy()->subMonths(5)->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case '12_bulan':
                $start = $now->copy()->subMonths(11)->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'custom':
                $start = Carbon::parse($customStart)->startOfDay();
                $end = Carbon::parse($customEnd)->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
        }

        // Query Penjualan
        $penjualanQuery = \App\Models\Penjualan::whereBetween('tanggal_penjualan', [$start, $end]);
        $totalPenjualan = $penjualanQuery->count();
        $totalNilaiPenjualan = $penjualanQuery->sum('total');
        $rataRataPenjualan = $totalPenjualan > 0 ? $totalNilaiPenjualan / $totalPenjualan : 0;
        $penjualanTunai = $penjualanQuery->where('metode_pembayaran', 'tunai')->count();
        $penjualanKredit = $penjualanQuery->whereIn('metode_pembayaran', ['kredit', 'leasing'])->count();

        $topMerek = (new self)->getTopMerek($start, $end);
        $topModel = (new self)->getTopModel($start, $end);
        $topKategori = (new self)->getTopKategori($start, $end);
        $topSales = (new self)->getTopSales($start, $end);

        // Simpan atau update laporan
        return self::updateOrCreate(
            [
                'tanggal' => $start,
                'tahun' => $start->year,
                'bulan' => $start->month,
                'minggu' => ceil($start->day / 7),
            ],
            [
                'total_penjualan' => $totalPenjualan,
                'total_nilai_penjualan' => $totalNilaiPenjualan,
                'rata_rata_penjualan' => $rataRataPenjualan,
                'penjualan_tunai' => $penjualanTunai,
                'penjualan_kredit' => $penjualanKredit,
                'top_merek' => $topMerek,
                'top_model' => $topModel,
                'top_kategori' => $topKategori,
                'top_sales' => $topSales,
            ]
        );
    }

    public function getTopMerek($startDate, $endDate, $limit = 5)
    {
        return Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->join('stok_mobils', 'penjualans.stok_mobil_id', '=', 'stok_mobils.id')
            ->join('varians', 'stok_mobils.varian_id', '=', 'varians.id')
            ->join('mobils', 'varians.mobil_id', '=', 'mobils.id')
            ->join('mereks', 'mobils.merek_id', '=', 'mereks.id')
            ->selectRaw('mereks.id as merek_id, mereks.nama as merek_nama, COUNT(*) as jumlah')
            ->groupBy('mereks.id', 'mereks.nama')
            ->orderByDesc('jumlah')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'nama' => $row->merek_nama,
                    'jumlah' => $row->jumlah,
                ];
            });
    }

    public function getTopModel($startDate, $endDate, $limit = 5)
    {
        return Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->join('stok_mobils', 'penjualans.stok_mobil_id', '=', 'stok_mobils.id')
            ->join('varians', 'stok_mobils.varian_id', '=', 'varians.id')
            ->join('mobils', 'varians.mobil_id', '=', 'mobils.id')
            ->selectRaw('mobils.id as mobil_id, mobils.nama as mobil_nama, varians.nama as varian_nama, COUNT(*) as jumlah')
            ->groupBy('mobils.id', 'mobils.nama', 'varians.nama')
            ->orderByDesc('jumlah')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'nama' => $row->mobil_nama . ' - ' . $row->varian_nama,
                    'jumlah' => $row->jumlah,
                ];
            });
    }

    public function getTopKategori($startDate, $endDate, $limit = 5)
    {
        return Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->join('stok_mobils', 'penjualans.stok_mobil_id', '=', 'stok_mobils.id')
            ->join('varians', 'stok_mobils.varian_id', '=', 'varians.id')
            ->join('mobils', 'varians.mobil_id', '=', 'mobils.id')
            ->join('kategoris', 'mobils.kategori_id', '=', 'kategoris.id')
            ->selectRaw('kategoris.id as kategori_id, kategoris.nama as kategori_nama, COUNT(*) as jumlah')
            ->groupBy('kategoris.id', 'kategoris.nama')
            ->orderByDesc('jumlah')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'nama' => $row->kategori_nama,
                    'jumlah' => $row->jumlah,
                ];
            });
    }

    public function getTopSales($startDate, $endDate, $limit = 5)
    {
        return Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->join('karyawans', 'penjualans.karyawan_id', '=', 'karyawans.id')
            ->selectRaw('karyawans.id as karyawan_id, karyawans.nama_lengkap, COUNT(*) as jumlah')
            ->groupBy('karyawans.id', 'karyawans.nama_lengkap')
            ->orderByDesc('jumlah')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'nama' => $row->nama_lengkap,
                    'jumlah' => $row->jumlah,
                ];
            });
    }

    // Tambahkan juga method accessor untuk format display di Resource
    public function getTopMerekFormattedAttribute()
    {
        return $this->getTopMerekFormatted();
    }

    public function getTopModelFormattedAttribute()
    {
        return $this->getTopModelFormatted();
    }

    public function getTopKategoriFormattedAttribute()
    {
        return $this->getTopKategoriFormatted();
    }

    public function getTopSalesFormattedAttribute()
    {
        return $this->getTopSalesFormatted();
    }
}