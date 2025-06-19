<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;

    public function getTitle(): string
    {
        return 'Tambah Kategori Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Kategori berhasil ditambahkan';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto set urutan_tampil jika 0
        if ($data['urutan_tampil'] === 0) {
            $maxUrutan = $this->getModel()::max('urutan_tampil') ?? 0;
            $data['urutan_tampil'] = $maxUrutan + 1;
        }

        return $data;
    }
}