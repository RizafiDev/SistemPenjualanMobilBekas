<?php

namespace App\Filament\Resources\RiwayatServisResource\Pages;

use App\Filament\Resources\RiwayatServisResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRiwayatServis extends CreateRecord
{
    protected static string $resource = RiwayatServisResource::class;

    public function getTitle(): string
    {
        return 'Tambah Riwayat Servis Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Riwayat servis berhasil ditambahkan';
    }
}
