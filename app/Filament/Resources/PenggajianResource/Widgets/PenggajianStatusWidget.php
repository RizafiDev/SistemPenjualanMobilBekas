<?php

namespace App\Filament\Resources\PenggajianResource\Widgets;

use App\Models\Penggajian;
use Filament\Widgets\ChartWidget;

class PenggajianStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Status Penggajian Bulan Ini';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentMonth = now()->format('Y-m');

        $draftCount = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_DRAFT)
            ->count();

        $dibayarCount = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->count();

        $batalCount = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_BATAL)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status Penggajian',
                    'data' => [$draftCount, $dibayarCount, $batalCount],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)', // warning - draft
                        'rgb(16, 185, 129)', // success - dibayar
                        'rgb(239, 68, 68)',  // danger - batal
                    ],
                    'borderColor' => [
                        'rgb(251, 191, 36)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Draft', 'Dibayar', 'Batal'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => false,
                ],
            ],
        ];
    }
}