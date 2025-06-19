<?php

namespace App\Filament\Resources\MerekResource\Pages;

use App\Filament\Resources\MerekResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMereks extends ListRecords
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Merek')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Daftar Merek';
    }
}