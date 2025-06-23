<?php


namespace App\Filament\Resources\PengajuanCutiResource\Widgets;

use App\Models\PengajuanCuti;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PengajuanCutiChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Statistik Pengajuan Cuti (6 Bulan Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get data untuk 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $monthlyData = PengajuanCuti::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $data['menunggu'][] = $monthlyData[PengajuanCuti::STATUS_MENUNGGU] ?? 0;
            $data['disetujui'][] = $monthlyData[PengajuanCuti::STATUS_DISETUJUI] ?? 0;
            $data['ditolak'][] = $monthlyData[PengajuanCuti::STATUS_DITOLAK] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Menunggu',
                    'data' => $data['menunggu'],
                    'backgroundColor' => 'rgba(234, 179, 8, 0.2)',
                    'borderColor' => 'rgba(234, 179, 8, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Disetujui',
                    'data' => $data['disetujui'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $data['ditolak'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}