<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKaryawan extends EditRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash password jika diisi dan berbeda dari yang lama
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            // Jika password kosong, hapus dari data agar tidak mengubah password lama
            unset($data['password']);
        }

        return $data;
    }
}
