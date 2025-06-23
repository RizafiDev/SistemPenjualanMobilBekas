<?php
namespace App\Filament\Resources\PengajuanCutiResource\Pages;

use App\Filament\Resources\PengajuanCutiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanCuti extends EditRecord
{
    protected static string $resource = PengajuanCutiResource::class;

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
        // Auto calculate jumlah_hari jika tanggal berubah
        if (isset($data['tanggal_mulai']) && isset($data['tanggal_selesai'])) {
            $startDate = \Carbon\Carbon::parse($data['tanggal_mulai']);
            $endDate = \Carbon\Carbon::parse($data['tanggal_selesai']);
            $data['jumlah_hari'] = $startDate->diffInDays($endDate) + 1;
        }

        return $data;
    }
}
