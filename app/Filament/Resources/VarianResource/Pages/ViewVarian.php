<?php

namespace App\Filament\Resources\VarianResource\Pages;

use App\Filament\Resources\VarianResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewVarian extends ViewRecord
{
    protected static string $resource = VarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Varian: ' . $this->record->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('mobil.nama')
                                    ->label('Mobil')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Varian')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('kode')
                                    ->label('Kode Varian'),

                                Infolists\Components\TextEntry::make('harga_otr')
                                    ->label('Harga OTR')
                                    ->money('IDR'),

                                Infolists\Components\IconEntry::make('aktif')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->visible(fn($record) => !empty($record->deskripsi))
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Spesifikasi Mesin')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('tipe_mesin')
                                    ->label('Tipe Mesin')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('kapasitas_mesin_cc')
                                    ->label('Kapasitas Mesin')
                                    ->suffix(' cc'),

                                Infolists\Components\TextEntry::make('silinder')
                                    ->label('Jumlah Silinder'),

                                Infolists\Components\TextEntry::make('transmisi')
                                    ->label('Transmisi')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('jumlah_gigi')
                                    ->label('Jumlah Gigi'),

                                Infolists\Components\TextEntry::make('daya_hp')
                                    ->label('Daya Maksimal')
                                    ->suffix(' HP'),

                                Infolists\Components\TextEntry::make('torsi_nm')
                                    ->label('Torsi Maksimal')
                                    ->suffix(' Nm'),

                                Infolists\Components\TextEntry::make('jenis_bahan_bakar')
                                    ->label('Jenis Bahan Bakar'),

                                Infolists\Components\TextEntry::make('konsumsi_bahan_bakar_kota')
                                    ->label('Konsumsi BB Kota')
                                    ->suffix(' km/L'),

                                Infolists\Components\TextEntry::make('konsumsi_bahan_bakar_jalan')
                                    ->label('Konsumsi BB Jalan')
                                    ->suffix(' km/L'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Dimensi & Kapasitas')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('panjang_mm')
                                    ->label('Panjang')
                                    ->suffix(' mm'),

                                Infolists\Components\TextEntry::make('lebar_mm')
                                    ->label('Lebar')
                                    ->suffix(' mm'),

                                Infolists\Components\TextEntry::make('tinggi_mm')
                                    ->label('Tinggi')
                                    ->suffix(' mm'),

                                Infolists\Components\TextEntry::make('jarak_sumbu_roda_mm')
                                    ->label('Jarak Sumbu Roda')
                                    ->suffix(' mm'),

                                Infolists\Components\TextEntry::make('ground_clearance_mm')
                                    ->label('Ground Clearance')
                                    ->suffix(' mm'),

                                Infolists\Components\TextEntry::make('berat_kosong_kg')
                                    ->label('Berat Kosong')
                                    ->suffix(' kg'),

                                Infolists\Components\TextEntry::make('berat_isi_kg')
                                    ->label('Berat Isi')
                                    ->suffix(' kg'),

                                Infolists\Components\TextEntry::make('kapasitas_bagasi_l')
                                    ->label('Kapasitas Bagasi')
                                    ->suffix(' L'),

                                Infolists\Components\TextEntry::make('kapasitas_tangki_l')
                                    ->label('Kapasitas Tangki')
                                    ->suffix(' L'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Performa')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('akselerasi_0_100_kmh')
                                    ->label('Akselerasi 0-100 km/h')
                                    ->suffix(' detik'),

                                Infolists\Components\TextEntry::make('kecepatan_maksimal_kmh')
                                    ->label('Kecepatan Maksimal')
                                    ->suffix(' km/h'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Fitur')
                    ->schema([
                        Infolists\Components\Section::make('Fitur Keamanan')
                            ->schema([
                                Infolists\Components\TextEntry::make('fitur_keamanan')
                                    ->label('Fitur Keamanan')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                            ])
                            ->collapsible(),

                        Infolists\Components\Section::make('Fitur Kenyamanan')
                            ->schema([
                                Infolists\Components\TextEntry::make('fitur_kenyamanan')
                                    ->label('Fitur Kenyamanan')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                            ])
                            ->collapsible(),

                        Infolists\Components\Section::make('Fitur Hiburan')
                            ->schema([
                                Infolists\Components\TextEntry::make('fitur_hiburan')
                                    ->label('Fitur Hiburan')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                            ])
                            ->collapsible(),
                    ]),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->dateTime('d M Y H:i')
                                    ->visible(fn($record) => $record->trashed()),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}