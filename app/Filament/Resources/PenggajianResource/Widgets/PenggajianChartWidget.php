<?php

namespace App\Filament\Resources\PenggajianResource\Widgets;

use App\Models\Penggajian;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PenggajianChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Penggajian 6 Bulan Terakhir';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = [];
        $totalGaji = [];
        $jumlahKaryawan = [];

        // Generate data untuk 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $periode = $date->format('Y-m');
            $monthName = $date->format('M Y');

            $months[] = $monthName;

            // Total gaji bersih yang dibayar
            $totalGajiBulan = Penggajian::where('periode', $periode)
                ->where('status', Penggajian::STATUS_DIBAYAR)
                ->sum('gaji_bersih');

            // Jumlah karyawan yang digaji
            $jumlahKaryawanBulan = Penggajian::where('periode', $periode)
                ->where('status', Penggajian::STATUS_DIBAYAR)
                ->count();

            $totalGaji[] = $totalGajiBulan / 1000000; // Konversi ke juta
            $jumlahKaryawan[] = $jumlahKaryawanBulan;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Gaji (Juta Rp)',
                    'data' => $totalGaji,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Jumlah Karyawan',
                    'data' => $jumlahKaryawan,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Total Gaji (Juta Rp)'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Karyawan'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'title' => [
                    'display' => false,
                ],
            ],
        ];
    }
}