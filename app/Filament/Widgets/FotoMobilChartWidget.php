<?php
namespace App\Filament\Widgets;

use App\Models\FotoMobil;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class FotoMobilChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Upload Media per Bulan';

    protected static ?string $description = 'Statistik upload media dalam 6 bulan terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect();

        // Generate data untuk 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->translatedFormat('M Y');

            $gambar = FotoMobil::where('jenis_media', 'gambar')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $video = FotoMobil::where('jenis_media', 'video')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $brosur = FotoMobil::where('jenis_media', 'brosur')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data->push([
                'month' => $monthName,
                'gambar' => $gambar,
                'video' => $video,
                'brosur' => $brosur,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Gambar',
                    'data' => $data->pluck('gambar')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Video',
                    'data' => $data->pluck('video')->toArray(),
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Brosur',
                    'data' => $data->pluck('brosur')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}