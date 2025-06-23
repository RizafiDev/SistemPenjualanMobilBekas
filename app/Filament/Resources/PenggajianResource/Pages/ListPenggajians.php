<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Penggajian;

class ListPenggajians extends ListRecords
{
    protected static string $resource = PenggajianResource::class;

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

            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Penggajian::STATUS_DRAFT))
                ->badge($this->getModel()::where('status', Penggajian::STATUS_DRAFT)->count()),

            'dibayar' => Tab::make('Dibayar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Penggajian::STATUS_DIBAYAR))
                ->badge($this->getModel()::where('status', Penggajian::STATUS_DIBAYAR)->count()),

            'batal' => Tab::make('Batal')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Penggajian::STATUS_BATAL))
                ->badge($this->getModel()::where('status', Penggajian::STATUS_BATAL)->count()),
        ];
    }
}