<?php

namespace App\Filament\Resources\PengajuanCutiResource\Pages;

use App\Filament\Resources\PengajuanCutiResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuanCuti extends CreateRecord
{
    protected static string $resource = PengajuanCutiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto calculate jumlah_hari jika belum ada
        if (isset($data['tanggal_mulai']) && isset($data['tanggal_selesai']) && !isset($data['jumlah_hari'])) {
            $startDate = \Carbon\Carbon::parse($data['tanggal_mulai']);
            $endDate = \Carbon\Carbon::parse($data['tanggal_selesai']);
            $data['jumlah_hari'] = $startDate->diffInDays($endDate) + 1;
        }

        return $data;
    }
}