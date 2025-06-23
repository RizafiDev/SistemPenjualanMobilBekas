<?php

namespace App\Filament\Resources\PenggajianResource\Widgets;

use App\Models\Penggajian;
use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PenggajianStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');

        // Total Karyawan
        $totalKaryawan = Karyawan::count();

        // Penggajian Bulan Ini
        $penggajianBulanIni = Penggajian::where('periode', $currentMonth)->count();
        $penggajianBulanLalu = Penggajian::where('periode', $previousMonth)->count();
        $trendPenggajian = $penggajianBulanLalu > 0
            ? (($penggajianBulanIni - $penggajianBulanLalu) / $penggajianBulanLalu) * 100
            : 0;

        // Total Gaji Dibayar Bulan Ini
        $totalGajiBulanIni = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->sum('gaji_bersih');

        $totalGajiBulanLalu = Penggajian::where('periode', $previousMonth)
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->sum('gaji_bersih');

        $trendGaji = $totalGajiBulanLalu > 0
            ? (($totalGajiBulanIni - $totalGajiBulanLalu) / $totalGajiBulanLalu) * 100
            : 0;

        // Status Penggajian Bulan Ini
        $draftCount = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_DRAFT)
            ->count();

        $dibayarCount = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->count();

        $batalCount = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_BATAL)
            ->count();

        // Rata-rata Gaji Karyawan
        $rataRataGaji = Penggajian::where('periode', $currentMonth)
            ->where('status', Penggajian::STATUS_DIBAYAR)
            ->avg('gaji_bersih') ?? 0;

        return [
            Stat::make('Total Karyawan', $totalKaryawan)
                ->description('Total karyawan aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Penggajian Bulan Ini', $penggajianBulanIni)
                ->description($trendPenggajian >= 0 ? '+' . number_format($trendPenggajian, 1) . '% dari bulan lalu' : number_format($trendPenggajian, 1) . '% dari bulan lalu')
                ->descriptionIcon($trendPenggajian >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trendPenggajian >= 0 ? 'success' : 'danger'),

            Stat::make('Total Gaji Dibayar', 'Rp ' . number_format($totalGajiBulanIni, 0, ',', '.'))
                ->description($trendGaji >= 0 ? '+' . number_format($trendGaji, 1) . '% dari bulan lalu' : number_format($trendGaji, 1) . '% dari bulan lalu')
                ->descriptionIcon($trendGaji >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trendGaji >= 0 ? 'success' : 'danger'),

            Stat::make('Status Draft', $draftCount)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Status Dibayar', $dibayarCount)
                ->description('Sudah dibayarkan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Rata-rata Gaji', 'Rp ' . number_format($rataRataGaji, 0, ',', '.'))
                ->description('Gaji bersih rata-rata bulan ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 3; // Menampilkan 3 kolom per baris
    }
}