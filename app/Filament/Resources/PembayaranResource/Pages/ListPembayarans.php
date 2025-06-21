<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pembayaran;

class ListPembayarans extends ListRecords
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge($this->getModel()::count()),

            'today' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->today())
                ->badge($this->getModel()::today()->count()),

            'this_month' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->thisMonth())
                ->badge($this->getModel()::thisMonth()->count()),

            'dp' => Tab::make('Down Payment')
                ->modifyQueryUsing(fn(Builder $query) => $query->byJenis('dp'))
                ->badge($this->getModel()::byJenis('dp')->count()),

            'cicilan' => Tab::make('Cicilan')
                ->modifyQueryUsing(fn(Builder $query) => $query->byJenis('cicilan'))
                ->badge($this->getModel()::byJenis('cicilan')->count()),

            'pelunasan' => Tab::make('Pelunasan')
                ->modifyQueryUsing(fn(Builder $query) => $query->byJenis('pelunasan'))
                ->badge($this->getModel()::byJenis('pelunasan')->count()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PembayaranResource\Widgets\PembayaranStatsOverview::class,
        ];
    }
}