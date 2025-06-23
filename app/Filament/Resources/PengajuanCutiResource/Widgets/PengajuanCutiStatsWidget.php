<?php

// File: app/Filament/Widgets/PengajuanCutiStatsWidget.php


namespace App\Filament\Resources\PengajuanCutiResource\Widgets;

use App\Models\PengajuanCuti;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengajuanCutiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalMenunggu = PengajuanCuti::where('status', PengajuanCuti::STATUS_MENUNGGU)->count();
        $totalDisetujui = PengajuanCuti::where('status', PengajuanCuti::STATUS_DISETUJUI)->count();
        $totalDitolak = PengajuanCuti::where('status', PengajuanCuti::STATUS_DITOLAK)->count();
        $totalBulanIni = PengajuanCuti::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Menunggu Persetujuan', $totalMenunggu)
                ->description('Pengajuan yang perlu diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.admin.resources.pengajuan-cutis.index', ['tableFilters[status][value]' => 'menunggu'])),

            Stat::make('Disetujui', $totalDisetujui)
                ->description('Total pengajuan disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ditolak', $totalDitolak)
                ->description('Total pengajuan ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Pengajuan Bulan Ini', $totalBulanIni)
                ->description('Total pengajuan ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}