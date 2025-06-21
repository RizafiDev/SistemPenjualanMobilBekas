<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use App\Models\Penjualan;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Colors\Color;

class ListPenjualans extends ListRecords
{
    protected static string $resource = PenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Penjualan')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(Penjualan::count()),

            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'draft'))
                ->badge(Penjualan::where('status', 'draft')->count())
                ->badgeColor('gray'),

            'booking' => Tab::make('Booking')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'booking'))
                ->badge(Penjualan::where('status', 'booking')->count())
                ->badgeColor('warning'),

            'lunas' => Tab::make('Lunas')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'lunas'))
                ->badge(Penjualan::where('status', 'lunas')->count())
                ->badgeColor('success'),

            'kredit' => Tab::make('Kredit')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'kredit'))
                ->badge(Penjualan::where('status', 'kredit')->count())
                ->badgeColor('info'),

            'batal' => Tab::make('Batal')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'batal'))
                ->badge(Penjualan::where('status', 'batal')->count())
                ->badgeColor('danger'),

            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('tanggal_penjualan', today()))
                ->badge(Penjualan::whereDate('tanggal_penjualan', today())->count())
                ->badgeColor('primary'),

            'bulan_ini' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('tanggal_penjualan', now()->month)
                    ->whereYear('tanggal_penjualan', now()->year))
                ->badge(Penjualan::whereMonth('tanggal_penjualan', now()->month)
                    ->whereYear('tanggal_penjualan', now()->year)->count())
                ->badgeColor('primary'),
        ];
    }
}