<?php

namespace App\Filament\Resources\VarianResource\Pages;

use App\Filament\Resources\VarianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVarian extends CreateRecord
{
    protected static string $resource = VarianResource::class;

    public function getTitle(): string
    {
        return 'Tambah Varian Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Varian berhasil ditambahkan';
    }
}
