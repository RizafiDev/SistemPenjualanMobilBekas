<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoris extends ListRecords
{
    protected static string $resource = KategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reorderKategori')
                ->label('Atur Ulang Urutan')
                ->icon('heroicon-o-bars-3')
                ->color('gray')
                ->url(fn() => static::getUrl(['reorder' => 'true']))
                ->visible(fn() => !request()->has('reorder')),

            Actions\CreateAction::make()
                ->label('Tambah Kategori')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Daftar Kategori';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa ditambahkan widget stats jika diperlukan
        ];
    }
}