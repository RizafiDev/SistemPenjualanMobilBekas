<?php
namespace App\Filament\Resources\PresensiResource\Widgets;

use App\Models\Presensi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PresensiChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Presensi 7 Hari Terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'date' => $date->format('d M'),
                'hadir' => Presensi::whereDate('tanggal', $date)
                    ->where('status', Presensi::STATUS_HADIR)
                    ->count(),
                'terlambat' => Presensi::whereDate('tanggal', $date)
                    ->where('status', Presensi::STATUS_TERLAMBAT)
                    ->count(),
                'tidak_hadir' => Presensi::whereDate('tanggal', $date)
                    ->where('status', Presensi::STATUS_TIDAK_HADIR)
                    ->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $data->pluck('hadir')->toArray(),
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $data->pluck('terlambat')->toArray(),
                    'backgroundColor' => '#F59E0B',
                    'borderColor' => '#F59E0B',
                ],
                [
                    'label' => 'Tidak Hadir',
                    'data' => $data->pluck('tidak_hadir')->toArray(),
                    'backgroundColor' => '#EF4444',
                    'borderColor' => '#EF4444',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}