<?php

namespace App\Filament\Resources\JanjiTemuResource\Pages;

use App\Filament\Resources\JanjiTemuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListJanjiTemus extends ListRecords
{
    protected static string $resource = JanjiTemuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'pending' => Tab::make('Menunggu')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => $this->getModel()::where('status', 'pending')->count()),
            'dikonfirmasi' => Tab::make('Dikonfirmasi')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'dikonfirmasi'))
                ->badge(fn() => $this->getModel()::where('status', 'dikonfirmasi')->count()),
            'terjadwal' => Tab::make('Terjadwal')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'terjadwal'))
                ->badge(fn() => $this->getModel()::where('status', 'terjadwal')->count()),
            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('waktu_mulai', today()))
                ->badge(fn() => $this->getModel()::whereDate('waktu_mulai', today())->count()),
        ];
    }
}