<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use App\Filament\Resources\KaryawanResource\Widgets\KaryawanOverviewStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KaryawanOverviewStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(fn() => $this->getResource()::getEloquentQuery()->count()),

            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('aktif', 'aktif'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->where('aktif', 'aktif')->count()),

            'nonaktif' => Tab::make('Non-Aktif')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('aktif', 'nonaktif'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->where('aktif', 'nonaktif')->count()),

            'tetap' => Tab::make('Karyawan Tetap')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'tetap'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->where('status', 'tetap')->count()),

            'kontrak' => Tab::make('Kontrak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'kontrak'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->where('status', 'kontrak')->count()),

            'magang' => Tab::make('Magang')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'magang'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->where('status', 'magang')->count()),
        ];
    }
}