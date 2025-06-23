<?php

namespace App\Filament\Resources\PengajuanCutiResource\Pages;

use App\Filament\Resources\PengajuanCutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PengajuanCuti;

class ListPengajuanCutis extends ListRecords
{
    protected static string $resource = PengajuanCutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge($this->getModel()::count()),

            'menunggu' => Tab::make('Menunggu Persetujuan')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PengajuanCuti::STATUS_MENUNGGU))
                ->badge($this->getModel()::where('status', PengajuanCuti::STATUS_MENUNGGU)->count())
                ->badgeColor('warning'),

            'disetujui' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PengajuanCuti::STATUS_DISETUJUI))
                ->badge($this->getModel()::where('status', PengajuanCuti::STATUS_DISETUJUI)->count())
                ->badgeColor('success'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PengajuanCuti::STATUS_DITOLAK))
                ->badge($this->getModel()::where('status', PengajuanCuti::STATUS_DITOLAK)->count())
                ->badgeColor('danger'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return PengajuanCutiResource::getWidgets();
    }
}

