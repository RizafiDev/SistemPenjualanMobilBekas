<?php

namespace App\Filament\Resources\KaryawanResource\Widgets;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PengajuanCuti;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KaryawanAbsensiStats extends BaseWidget
{
    public ?Karyawan $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        // Statistik Bulan Ini
        $absensiStats = $this->getAbsensiStats($this->record->id, $startOfMonth, $endOfMonth);
        $cutiStats = $this->getCutiStats($this->record->id, $startOfMonth, $endOfMonth);

        // Statistik Tahun Ini
        $absensiStatsYear = $this->getAbsensiStats($this->record->id, $startOfYear, $endOfYear);
        $cutiStatsYear = $this->getCutiStats($this->record->id, $startOfYear, $endOfYear);

        return [
            // Bulan Ini
            Stat::make('Hadir Bulan Ini', $absensiStats['hadir'])
                ->description('Total kehadiran bulan ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 4, 8, 6, 12, 9, 14, 10, 16, 8, 12, 15]),

            Stat::make('Terlambat Bulan Ini', $absensiStats['terlambat'])
                ->description('Total keterlambatan bulan ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([2, 1, 3, 2, 1, 4, 2, 3, 1, 2, 1, 0]),

            Stat::make('Tidak Hadir Bulan Ini', $absensiStats['tidak_hadir'])
                ->description('Total tidak hadir bulan ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([0, 1, 0, 2, 0, 1, 0, 0, 1, 0, 0, 0]),

            Stat::make('Sakit Bulan Ini', $absensiStats['sakit'])
                ->description('Total sakit bulan ini')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info')
                ->chart([1, 0, 2, 0, 1, 0, 0, 1, 0, 0, 1, 0]),

            Stat::make('Izin Bulan Ini', $absensiStats['izin'])
                ->description('Total izin bulan ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray')
                ->chart([0, 1, 0, 1, 0, 0, 1, 0, 0, 1, 0, 0]),

            Stat::make('Cuti Disetujui Bulan Ini', $cutiStats['disetujui'])
                ->description('Total cuti disetujui bulan ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success')
                ->chart([2, 1, 0, 3, 1, 2, 0, 1, 2, 0, 1, 1]),

            // Tahun Ini (Summary)
            Stat::make('Total Hadir Tahun Ini', $absensiStatsYear['hadir'])
                ->description('Kehadiran sepanjang tahun')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('Total Cuti Tahun Ini', $cutiStatsYear['disetujui'])
                ->description('Cuti yang diambil tahun ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }

    private function getAbsensiStats($karyawanId, $startDate, $endDate): array
    {
        $stats = Presensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'hadir' => ($stats[Presensi::STATUS_HADIR] ?? 0) + ($stats[Presensi::STATUS_TERLAMBAT] ?? 0),
            'terlambat' => $stats[Presensi::STATUS_TERLAMBAT] ?? 0,
            'tidak_hadir' => $stats[Presensi::STATUS_TIDAK_HADIR] ?? 0,
            'sakit' => $stats[Presensi::STATUS_SAKIT] ?? 0,
            'izin' => $stats[Presensi::STATUS_IZIN] ?? 0,
            'cuti' => $stats[Presensi::STATUS_CUTI] ?? 0,
            'libur' => $stats[Presensi::STATUS_LIBUR] ?? 0,
        ];
    }

    private function getCutiStats($karyawanId, $startDate, $endDate): array
    {
        $stats = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('tanggal_mulai', '<=', $startDate)
                            ->where('tanggal_selesai', '>=', $endDate);
                    });
            })
            ->select('status', DB::raw('count(*) as total'), DB::raw('sum(jumlah_hari) as total_hari'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'menunggu' => $stats->get(PengajuanCuti::STATUS_MENUNGGU)?->total ?? 0,
            'disetujui' => $stats->get(PengajuanCuti::STATUS_DISETUJUI)?->total ?? 0,
            'ditolak' => $stats->get(PengajuanCuti::STATUS_DITOLAK)?->total ?? 0,
            'hari_disetujui' => $stats->get(PengajuanCuti::STATUS_DISETUJUI)?->total_hari ?? 0,
        ];
    }
}