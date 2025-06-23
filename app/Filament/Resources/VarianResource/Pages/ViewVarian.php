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
                // Header dengan informasi utama
                Infolists\Components\Section::make('Informasi Dasar')
                    ->description('Detail utama varian mobil')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('mobil.nama')
                                    ->label('Mobil')
                                    ->icon('heroicon-m-truck')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Varian')
                                    ->icon('heroicon-m-tag')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('kode')
                                    ->label('Kode Varian')
                                    ->icon('heroicon-m-hashtag')
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('harga_otr')
                                    ->label('Harga OTR')
                                    ->icon('heroicon-m-banknotes')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('warning'),

                                Infolists\Components\IconEntry::make('aktif')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size('lg'),
                            ])
                            ->columnSpan(2),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->visible(fn($record) => !empty($record->deskripsi))
                            ->prose()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'bg-gray-50 p-4 rounded-lg dark:bg-gray-800']),
                    ]),

                // Spesifikasi Mesin dengan styling yang lebih menarik
                Infolists\Components\Section::make('Spesifikasi Mesin')
                    ->description('Detail performa dan mesin kendaraan')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('tipe_mesin')
                                    ->label('Tipe Mesin')
                                    ->icon('heroicon-m-wrench-screwdriver')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('kapasitas_mesin_cc')
                                    ->label('Kapasitas Mesin')
                                    ->icon('heroicon-m-beaker')
                                    ->suffix(' cc')
                                    ->weight('semibold')
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('silinder')
                                    ->label('Jumlah Silinder')
                                    ->icon('heroicon-m-squares-2x2')
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('transmisi')
                                    ->label('Transmisi')
                                    ->icon('heroicon-m-arrow-path')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('jumlah_gigi')
                                    ->label('Jumlah Gigi')
                                    ->icon('heroicon-m-numbered-list')
                                    ->badge()
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('daya_hp')
                                    ->label('Daya Maksimal')
                                    ->icon('heroicon-m-bolt')
                                    ->suffix(' HP')
                                    ->weight('bold')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('torsi_nm')
                                    ->label('Torsi Maksimal')
                                    ->icon('heroicon-m-arrow-trending-up')
                                    ->suffix(' Nm')
                                    ->weight('bold')
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('jenis_bahan_bakar')
                                    ->label('Jenis Bahan Bakar')
                                    ->icon('heroicon-m-fire')
                                    ->badge()
                                    ->color('orange'),

                                Infolists\Components\TextEntry::make('konsumsi_bahan_bakar_kota')
                                    ->label('Konsumsi BB Kota')
                                    ->icon('heroicon-m-building-office')
                                    ->suffix(' km/L')
                                    ->color('green'),

                                Infolists\Components\TextEntry::make('konsumsi_bahan_bakar_jalan')
                                    ->label('Konsumsi BB Jalan')
                                    ->icon('heroicon-m-map')
                                    ->suffix(' km/L')
                                    ->color('green'),
                            ]),
                    ]),

                // Dimensi & Kapasitas dengan visual yang lebih baik
                Infolists\Components\Section::make('Dimensi & Kapasitas')
                    ->description('Ukuran dan kapasitas kendaraan')
                    ->icon('heroicon-m-square-3-stack-3d')
                    ->schema([
                        Infolists\Components\Section::make('Dimensi Eksternal')
                            ->icon('heroicon-m-arrows-pointing-out')
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('panjang_mm')
                                            ->label('Panjang')
                                            ->icon('heroicon-m-arrows-right-left')
                                            ->suffix(' mm')
                                            ->color('blue'),

                                        Infolists\Components\TextEntry::make('lebar_mm')
                                            ->label('Lebar')
                                            ->icon('heroicon-m-arrows-right-left')
                                            ->suffix(' mm')
                                            ->color('blue'),

                                        Infolists\Components\TextEntry::make('tinggi_mm')
                                            ->label('Tinggi')
                                            ->icon('heroicon-m-arrows-up-down')
                                            ->suffix(' mm')
                                            ->color('blue'),

                                        Infolists\Components\TextEntry::make('jarak_sumbu_roda_mm')
                                            ->label('Jarak Sumbu Roda')
                                            ->icon('heroicon-m-arrows-right-left')
                                            ->suffix(' mm')
                                            ->color('indigo'),

                                        Infolists\Components\TextEntry::make('ground_clearance_mm')
                                            ->label('Ground Clearance')
                                            ->icon('heroicon-m-arrow-up')
                                            ->suffix(' mm')
                                            ->color('green'),
                                    ]),
                            ]),

                        Infolists\Components\Section::make('Berat & Kapasitas')
                            ->icon('heroicon-m-scale')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('berat_kosong_kg')
                                            ->label('Berat Kosong')
                                            ->icon('heroicon-m-minus')
                                            ->suffix(' kg')
                                            ->color('gray'),

                                        Infolists\Components\TextEntry::make('berat_isi_kg')
                                            ->label('Berat Isi')
                                            ->icon('heroicon-m-plus')
                                            ->suffix(' kg')
                                            ->color('slate'),

                                        Infolists\Components\TextEntry::make('kapasitas_bagasi_l')
                                            ->label('Kapasitas Bagasi')
                                            ->icon('heroicon-m-archive-box')
                                            ->suffix(' L')
                                            ->color('amber'),

                                        Infolists\Components\TextEntry::make('kapasitas_tangki_l')
                                            ->label('Kapasitas Tangki')
                                            ->icon('heroicon-m-beaker')
                                            ->suffix(' L')
                                            ->color('orange'),
                                    ]),
                            ]),
                    ]),

                // Performa dengan styling yang eye-catching
                Infolists\Components\Section::make('Performa')
                    ->description('Kemampuan performa kendaraan')
                    ->icon('heroicon-m-rocket-launch')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('akselerasi_0_100_kmh')
                                    ->label('Akselerasi 0-100 km/h')
                                    ->icon('heroicon-m-forward')
                                    ->suffix(' detik')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('kecepatan_maksimal_kmh')
                                    ->label('Kecepatan Maksimal')
                                    ->icon('heroicon-m-bolt')
                                    ->suffix(' km/h')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('warning'),
                            ]),
                    ]),

                // Fitur dengan tampilan yang lebih menarik
                Infolists\Components\Section::make('Fitur Kendaraan')
                    ->description('Fitur-fitur yang tersedia pada kendaraan')
                    ->icon('heroicon-m-star')
                    ->schema([
                        Infolists\Components\Section::make('Fitur Keamanan')
                            ->icon('heroicon-m-shield-check')
                            ->description('Fitur untuk keselamatan berkendara')
                            ->schema([
                                Infolists\Components\TextEntry::make('fitur_keamanan')
                                    ->label('Fitur Keamanan')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->extraAttributes(['class' => 'prose-sm']),
                            ])
                            ->collapsible()
                            ->collapsed(),

                        Infolists\Components\Section::make('Fitur Kenyamanan')
                            ->icon('heroicon-m-home')
                            ->description('Fitur untuk kenyamanan perjalanan')
                            ->schema([
                                Infolists\Components\TextEntry::make('fitur_kenyamanan')
                                    ->label('Fitur Kenyamanan')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->extraAttributes(['class' => 'prose-sm']),
                            ])
                            ->collapsible()
                            ->collapsed(),

                        Infolists\Components\Section::make('Fitur Hiburan')
                            ->icon('heroicon-m-musical-note')
                            ->description('Fitur multimedia dan hiburan')
                            ->schema([
                                Infolists\Components\TextEntry::make('fitur_hiburan')
                                    ->label('Fitur Hiburan')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->extraAttributes(['class' => 'prose-sm']),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ]),

                // Informasi sistem dengan styling minimal
                Infolists\Components\Section::make('Informasi Sistem')
                    ->description('Metadata dan informasi sistem')
                    ->icon('heroicon-m-computer-desktop')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->icon('heroicon-m-plus-circle')
                                    ->dateTime('d M Y H:i')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->icon('heroicon-m-pencil-square')
                                    ->dateTime('d M Y H:i')
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->icon('heroicon-m-trash')
                                    ->dateTime('d M Y H:i')
                                    ->visible(fn($record) => $record->trashed())
                                    ->color('danger'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}