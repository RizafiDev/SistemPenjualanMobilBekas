<?php

namespace App\Filament\Resources\LaporanPenjualanResource\Pages;

use App\Filament\Resources\LaporanPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Models\LaporanPenjualan;

class ListLaporanPenjualans extends ListRecords
{
    protected static string $resource = LaporanPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('generate_laporan')
                ->label('Generate Laporan')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('periode')
                        ->label('Periode')
                        ->options([
                            'harian' => 'Hari Ini',
                            '3_hari' => '3 Hari Terakhir',
                            'mingguan' => 'Minggu Ini',
                            'bulanan' => 'Bulan Ini',
                            '6_bulan' => '6 Bulan Terakhir',
                            '12_bulan' => '12 Bulan Terakhir',
                            'custom' => 'Custom Range',
                        ])
                        ->required()
                        ->reactive(),
                    \Filament\Forms\Components\DatePicker::make('custom_start')
                        ->label('Tanggal Mulai')
                        ->visible(fn($get) => $get('periode') === 'custom')
                        ->required(fn($get) => $get('periode') === 'custom'),
                    \Filament\Forms\Components\DatePicker::make('custom_end')
                        ->label('Tanggal Selesai')
                        ->visible(fn($get) => $get('periode') === 'custom')
                        ->required(fn($get) => $get('periode') === 'custom'),
                ])
                ->action(function (array $data) {
                    LaporanPenjualan::generateLaporan(
                        $data['periode'],
                        $data['custom_start'] ?? null,
                        $data['custom_end'] ?? null
                    );
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation()
                ->modalHeading('Generate Laporan Penjualan')
                ->modalDescription('Pilih periode laporan yang ingin digenerate.'),
        ];
    }
}