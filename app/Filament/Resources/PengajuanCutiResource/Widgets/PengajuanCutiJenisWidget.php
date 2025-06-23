<?php


namespace App\Filament\Resources\PengajuanCutiResource\Widgets;

use App\Models\PengajuanCuti;
use Filament\Widgets\ChartWidget;

class PengajuanCutiJenisWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Cuti';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = PengajuanCuti::selectRaw('jenis, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('jenis')
            ->pluck('count', 'jenis')
            ->toArray();

        $labels = [];
        $values = [];
        $colors = [];

        foreach (PengajuanCuti::getJenisOptions() as $key => $label) {
            if (isset($data[$key])) {
                $labels[] = $label;
                $values[] = $data[$key];

                // Set colors based on jenis
                switch ($key) {
                    case PengajuanCuti::JENIS_TAHUNAN:
                        $colors[] = '#3B82F6'; // Blue
                        break;
                    case PengajuanCuti::JENIS_SAKIT:
                        $colors[] = '#EAB308'; // Yellow
                        break;
                    case PengajuanCuti::JENIS_DARURAT:
                        $colors[] = '#EF4444'; // Red
                        break;
                    case PengajuanCuti::JENIS_LAINNYA:
                        $colors[] = '#6B7280'; // Gray
                        break;
                    default:
                        $colors[] = '#8B5CF6'; // Purple
                        break;
                }
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}