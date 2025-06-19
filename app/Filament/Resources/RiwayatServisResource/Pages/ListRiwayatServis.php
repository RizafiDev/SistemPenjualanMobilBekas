<?php

namespace App\Filament\Resources\RiwayatServisResource\Pages;

use App\Filament\Resources\RiwayatServisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatServis extends ListRecords
{
    protected static string $resource = RiwayatServisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Riwayat Servis')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Daftar Riwayat Servis';
    }
}
