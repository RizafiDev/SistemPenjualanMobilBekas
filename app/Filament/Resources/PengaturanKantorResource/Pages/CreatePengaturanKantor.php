<?php

namespace App\Filament\Resources\PengaturanKantorResource\Pages;

use App\Filament\Resources\PengaturanKantorResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengaturanKantor extends CreateRecord
{
    protected static string $resource = PengaturanKantorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengaturan kantor berhasil dibuat';
    }
}