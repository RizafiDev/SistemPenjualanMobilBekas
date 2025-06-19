<?php

namespace App\Filament\Resources\MobilResource\Pages;

use App\Filament\Resources\MobilResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMobil extends CreateRecord
{
    protected static string $resource = MobilResource::class;

    public function getTitle(): string
    {
        return 'Tambah Mobil Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Mobil berhasil ditambahkan';
    }
}
