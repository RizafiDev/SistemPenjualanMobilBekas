<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePenggajian extends CreateRecord
{
    protected static string $resource = PenggajianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penggajian berhasil dibuat')
            ->body('Data penggajian karyawan berhasil ditambahkan.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate totals before creating
        $totalGaji = ($data['gaji_pokok'] ?? 0) +
            ($data['tunjangan'] ?? 0) +
            ($data['bonus'] ?? 0) +
            ($data['lembur'] ?? 0) +
            ($data['insentif'] ?? 0);

        $totalPotongan = ($data['potongan_terlambat'] ?? 0) +
            ($data['potongan_absensi'] ?? 0) +
            ($data['potongan_lainnya'] ?? 0);

        $data['total_gaji'] = $totalGaji;
        $data['total_potongan'] = $totalPotongan;
        $data['gaji_bersih'] = $totalGaji - $totalPotongan;

        return $data;
    }
}