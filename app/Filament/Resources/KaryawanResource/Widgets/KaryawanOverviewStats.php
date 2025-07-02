<?php

namespace App\Filament\Resources\KaryawanResource\Widgets;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PengajuanCuti;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KaryawanOverviewStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Statistik Karyawan
        $totalKaryawan = Karyawan::where('aktif', 'aktif')->count();
        $totalKaryawanTetap = Karyawan::where('aktif', 'aktif')->where('status', 'tetap')->count();
        $totalKaryawanKontrak = Karyawan::where('aktif', 'aktif')->where('status', 'kontrak')->count();

        // Statistik Absensi Hari Ini
        $absensiHariIni = Presensi::whereDate('tanggal', $today)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $hadirHariIni = ($absensiHariIni[Presensi::STATUS_HADIR] ?? 0) +
            ($absensiHariIni[Presensi::STATUS_TERLAMBAT] ?? 0);
        $terlambatHariIni = $absensiHariIni[Presensi::STATUS_TERLAMBAT] ?? 0;
        $tidakHadirHariIni = $absensiHariIni[Presensi::STATUS_TIDAK_HADIR] ?? 0;

        // Statistik Cuti Bulan Ini
        $cutiMenunggu = PengajuanCuti::where('status', PengajuanCuti::STATUS_MENUNGGU)
            ->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
            ->count();

        $cutiDisetujui = PengajuanCuti::where('status', PengajuanCuti::STATUS_DISETUJUI)
            ->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
            ->sum('jumlah_hari');

        return [
            Stat::make('Total Karyawan Aktif', $totalKaryawan)
                ->description('Karyawan yang masih aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([5, 10, 8, 12, 15, 18, 20, 22, 25, 28, 30, $totalKaryawan]),

            Stat::make('Karyawan Tetap', $totalKaryawanTetap)
                ->description('Status karyawan tetap')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),

            Stat::make('Karyawan Kontrak', $totalKaryawanKontrak)
                ->description('Status karyawan kontrak')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Hadir Hari Ini', $hadirHariIni)
                ->description('Karyawan yang hadir hari ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Terlambat Hari Ini', $terlambatHariIni)
                ->description('Karyawan yang terlambat')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Tidak Hadir Hari Ini', $tidakHadirHariIni)
                ->description('Karyawan yang tidak hadir')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Pengajuan Cuti Menunggu', $cutiMenunggu)
                ->description('Menunggu persetujuan bulan ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Hari Cuti Disetujui', $cutiDisetujui)
                ->description('Total hari cuti bulan ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}