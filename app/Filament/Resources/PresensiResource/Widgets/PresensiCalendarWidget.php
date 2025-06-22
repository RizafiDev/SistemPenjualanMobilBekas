<?php
namespace App\Filament\Resources\PresensiResource\Widgets;

use App\Models\Presensi;
use App\Models\Karyawan;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PresensiCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.presensi-calendar';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $currentMonth = now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Get all days in current month
        $days = collect();
        $date = $startOfMonth->copy();

        while ($date->lte($endOfMonth)) {
            $dayData = [
                'date' => $date->copy(),
                'day' => $date->day,
                'isToday' => $date->isToday(),
                'isWeekend' => $date->isWeekend(),
                'presensi' => $this->getPresensiForDate($date),
            ];

            $days->push($dayData);
            $date->addDay();
        }

        return [
            'currentMonth' => $currentMonth,
            'days' => $days,
            'totalKaryawan' => Karyawan::where('aktif', 'aktif')->count(),
        ];
    }

    private function getPresensiForDate(Carbon $date): Collection
    {
        return Presensi::whereDate('tanggal', $date)
            ->with('karyawan')
            ->get()
            ->groupBy('status')
            ->map(fn($group) => $group->count());
    }
}