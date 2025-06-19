<?php

namespace App\Filament\Resources\StokMobilResource\Pages;

use App\Filament\Resources\StokMobilResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStokMobil extends CreateRecord
{
    protected static string $resource = StokMobilResource::class;

    public function getTitle(): string
    {
        return 'Tambah Stok Mobil Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Stok mobil berhasil ditambahkan';
    }
}
