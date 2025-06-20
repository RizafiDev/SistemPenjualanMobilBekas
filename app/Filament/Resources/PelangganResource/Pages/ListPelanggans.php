<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use App\Filament\Resources\PelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPelanggans extends ListRecords
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pelanggan'),
            'complete_contact' => Tab::make('Kontak Lengkap')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('no_telepon')->whereNotNull('email'))
                ->badge(fn() => $this->getModel()::whereNotNull('no_telepon')->whereNotNull('email')->count()),
            'complete_identity' => Tab::make('Identitas Lengkap')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('nik')->whereNotNull('tanggal_lahir')->whereNotNull('alamat'))
                ->badge(fn() => $this->getModel()::whereNotNull('nik')->whereNotNull('tanggal_lahir')->whereNotNull('alamat')->count()),
            'incomplete' => Tab::make('Data Belum Lengkap')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where(function ($q) {
                        $q->whereNull('no_telepon')
                            ->orWhereNull('email')
                            ->orWhereNull('nik')
                            ->orWhereNull('tanggal_lahir')
                            ->orWhereNull('alamat');
                    });
                })
                ->badge(fn() => $this->getModel()::where(function ($q) {
                    $q->whereNull('no_telepon')
                        ->orWhereNull('email')
                        ->orWhereNull('nik')
                        ->orWhereNull('tanggal_lahir')
                        ->orWhereNull('alamat');
                })->count())
                ->badgeColor('warning'),
        ];
    }
}