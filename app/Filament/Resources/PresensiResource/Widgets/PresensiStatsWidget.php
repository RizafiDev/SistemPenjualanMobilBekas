<?php

namespace App\Filament\Resources\PresensiResource\Widgets;

use App\Models\Presensi;
use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PresensiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        // Data hari ini
        $hadirHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', Presensi::STATUS_HADIR)
            ->count();

        $terlambatHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', Presensi::STATUS_TERLAMBAT)
            ->count();

        $tidakHadirHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', Presensi::STATUS_TIDAK_HADIR)
            ->count();

        $totalKaryawan = Karyawan::where('aktif', 'aktif')->count();
        $totalPresensiHariIni = $hadirHariIni + $terlambatHariIni;

        // Persentase kehadiran bulan ini
        $totalHariBulanIni = now()->daysInMonth;
        $totalPresensiSeharusnya = $totalKaryawan * $totalHariBulanIni;
        $totalPresensiAktual = Presensi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->whereIn('status', [Presensi::STATUS_HADIR, Presensi::STATUS_TERLAMBAT])
            ->count();

        $persentaseKehadiran = $totalPresensiSeharusnya > 0
            ? round(($totalPresensiAktual / $totalPresensiSeharusnya) * 100, 1)
            : 0;

        return [
            Stat::make('Hadir Hari Ini', $hadirHariIni)
                ->description('Dari ' . $totalKaryawan . ' karyawan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Terlambat Hari Ini', $terlambatHariIni)
                ->description('Perlu perhatian')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Tidak Hadir', $tidakHadirHariIni)
                ->description('Hari ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Tingkat Kehadiran', $persentaseKehadiran . '%')
                ->description('Bulan ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($persentaseKehadiran >= 85 ? 'success' : ($persentaseKehadiran >= 70 ? 'warning' : 'danger')),
        ];
    }
}