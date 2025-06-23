<?php
namespace App\Filament\Resources\LaporanPenjualanResource\Pages;

use App\Filament\Resources\LaporanPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\LaporanPenjualan;

class ViewLaporanPenjualan extends ViewRecord
{
    protected static string $resource = LaporanPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('generate_ulang')
                ->label('Generate Ulang')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    LaporanPenjualan::generateLaporan($this->record->tanggal);
                    $this->refreshFormData(['record']);
                })
                ->requiresConfirmation()
                ->modalHeading('Generate Ulang Laporan')
                ->modalDescription('Generate ulang laporan untuk tanggal ' . $this->record->tanggal->format('d/m/Y') . '?'),
        ];
    }
}
