<?php

// app/Filament/Widgets/FotoMobilStatsWidget.php

namespace App\Filament\Widgets;

use App\Models\FotoMobil;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FotoMobilStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // Total media per jenis
        $mediaStats = FotoMobil::select('jenis_media', DB::raw('count(*) as total'))
            ->groupBy('jenis_media')
            ->pluck('total', 'jenis_media')
            ->toArray();

        // Total gambar per jenis
        $gambarStats = FotoMobil::where('jenis_media', 'gambar')
            ->select('jenis_gambar', DB::raw('count(*) as total'))
            ->groupBy('jenis_gambar')
            ->pluck('total', 'jenis_gambar')
            ->toArray();

        // Total ukuran file
        $totalSize = FotoMobil::get()->sum(function ($record) {
            $filePath = storage_path('app/public/' . $record->path_file);
            return file_exists($filePath) ? filesize($filePath) : 0;
        });

        $totalSizeFormatted = $this->formatBytes($totalSize);

        return [
            Stat::make('Total Gambar', $mediaStats['gambar'] ?? 0)
                ->description('Foto mobil')
                ->descriptionIcon('heroicon-m-photo')
                ->color('success')
                ->chart([7, 12, 8, 15, 10, 18, 20]),

            Stat::make('Total Video', $mediaStats['video'] ?? 0)
                ->description('Video mobil')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('warning')
                ->chart([3, 5, 2, 7, 4, 6, 8]),

            Stat::make('Total Brosur', $mediaStats['brosur'] ?? 0)
                ->description('Brosur mobil')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([1, 2, 1, 3, 2, 4, 3]),

            Stat::make('Thumbnail', $gambarStats['thumbnail'] ?? 0)
                ->description('Gambar thumbnail')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),

            Stat::make('Total Storage', $totalSizeFormatted)
                ->description('Ukuran semua file')
                ->descriptionIcon('heroicon-m-server-stack')
                ->color('gray'),

            Stat::make('Media Terbaru', FotoMobil::count())
                ->description('Total semua media')
                ->descriptionIcon('heroicon-m-folder')
                ->color('secondary'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}