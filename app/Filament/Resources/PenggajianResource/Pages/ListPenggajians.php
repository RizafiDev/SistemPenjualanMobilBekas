<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use App\Filament\Resources\PenggajianResource\Widgets\PenggajianChartWidget;
use App\Filament\Resources\PenggajianResource\Widgets\PenggajianStatsWidget;
use App\Filament\Resources\PenggajianResource\Widgets\PenggajianStatusWidget;
use App\Filament\Resources\PenggajianResource\Widgets\RecentPenggajianWidget;
use App\Filament\Resources\PenggajianResource\Widgets\TopEarnersWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Penggajian;

class ListPenggajians extends ListRecords
{
    protected static string $resource = PenggajianResource::class;



    protected function getHeaderWidgets(): array
    {
        return [
            PenggajianChartWidget::class,
            PenggajianStatsWidget::class,
            PenggajianStatusWidget::class,
            TopEarnersWidget::class,
            RecentPenggajianWidget::class,

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
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}