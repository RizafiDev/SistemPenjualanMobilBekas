<?php

namespace App\Filament\Resources\RiwayatServisResource\Pages;

use App\Filament\Resources\RiwayatServisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatServis extends EditRecord
{
    protected static string $resource = RiwayatServisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Riwayat Servis: ' . $this->record->stokMobil->mobil->nama;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Riwayat servis berhasil diperbarui';
    }
}
