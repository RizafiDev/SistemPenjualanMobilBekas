<?php

namespace App\Filament\Resources\PembayaranResource\Widgets;

use App\Models\Pembayaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PembayaranStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total pembayaran hari ini
        $todayTotal = Pembayaran::today()->sum('jumlah');
        $todayCount = Pembayaran::today()->count();

        // Total pembayaran bulan ini
        $monthTotal = Pembayaran::thisMonth()->sum('jumlah');
        $monthCount = Pembayaran::thisMonth()->count();

        // Total pembayaran tahun ini
        $yearTotal = Pembayaran::thisYear()->sum('jumlah');

        // Pertumbuhan dari bulan sebelumnya
        $lastMonth = now()->subMonth();
        $lastMonthTotal = Pembayaran::byMonth($lastMonth->year, $lastMonth->month)->sum('jumlah');
        $monthlyGrowth = $lastMonthTotal > 0
            ? (($monthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        return [
            Stat::make('Pembayaran Hari Ini', 'Rp ' . number_format($todayTotal, 0, ',', '.'))
                ->description($todayCount . ' transaksi')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),

            Stat::make('Pembayaran Bulan Ini', 'Rp ' . number_format($monthTotal, 0, ',', '.'))
                ->description($monthCount . ' transaksi')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Total Tahun Ini', 'Rp ' . number_format($yearTotal, 0, ',', '.'))
                ->description(
                    ($monthlyGrowth >= 0 ? '+' : '') .
                    number_format($monthlyGrowth, 1) . '% dari bulan lalu'
                )
                ->descriptionIcon($monthlyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
}