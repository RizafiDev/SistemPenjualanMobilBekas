<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use App\Models\Presensi;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPresensis extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Presensi'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn() => Presensi::count()),

            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->hariIni())
                ->badge(fn() => Presensi::hariIni()->count()),

            'hadir' => Tab::make('Hadir')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Presensi::STATUS_HADIR))
                ->badge(fn() => Presensi::where('status', Presensi::STATUS_HADIR)->count()),

            'terlambat' => Tab::make('Terlambat')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Presensi::STATUS_TERLAMBAT))
                ->badge(fn() => Presensi::where('status', Presensi::STATUS_TERLAMBAT)->count()),

            'tidak_hadir' => Tab::make('Tidak Hadir')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Presensi::STATUS_TIDAK_HADIR))
                ->badge(fn() => Presensi::where('status', Presensi::STATUS_TIDAK_HADIR)->count()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return PresensiResource::getWidgets();
    }
}