<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKaryawan extends CreateRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash password jika tidak kosong
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }
}