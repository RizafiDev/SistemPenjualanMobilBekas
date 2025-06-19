<?php

namespace App\Filament\Resources\MerekResource\Pages;

use App\Filament\Resources\MerekResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMerek extends CreateRecord
{
    protected static string $resource = MerekResource::class;

    public function getTitle(): string
    {
        return 'Tambah Merek Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Merek berhasil ditambahkan';
    }
}