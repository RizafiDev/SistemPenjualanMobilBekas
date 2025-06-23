<?php

namespace App\Filament\Resources\RiwayatServisResource\Pages;

use App\Filament\Resources\RiwayatServisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewRiwayatServis extends ViewRecord
{
    protected static string $resource = RiwayatServisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Riwayat Servis: ' . $this->record->stokMobil->mobil->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('🚗 Informasi Mobil')
                    ->description('Detail kendaraan yang diservis')
                    ->icon('heroicon-o-truck')
                    ->iconColor('primary')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('stokMobil.mobil.nama')
                                        ->label('Nama Mobil')
                                        ->icon('heroicon-o-identification')
                                        ->weight('bold')
                                        ->size('xl')
                                        ->color('primary')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('stokMobil.no_rangka')
                                        ->label('No. Rangka')
                                        ->icon('heroicon-o-hashtag')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->badge()
                                        ->color('gray')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('tanggal_servis')
                                        ->label('Tanggal Servis')
                                        ->icon('heroicon-o-calendar-days')
                                        ->date('d F Y')
                                        ->badge()
                                        ->color('info')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('kilometer_servis')
                                        ->label('Kilometer')
                                        ->icon('heroicon-o-map-pin')
                                        ->numeric()
                                        ->suffix(' km')
                                        ->badge()
                                        ->color('warning')
                                        ->weight('bold'),
                                ]),

                            Infolists\Components\ImageEntry::make('foto_bukti')
                                ->label('📸 Foto Bukti Servis')
                                ->circular()
                                ->stacked()
                                ->size(150)
                                ->grow(false)
                                ->ring(2)
                                ->visible(fn($record) => !empty($record->foto_bukti)),
                        ])->from('lg'),
                    ]),

                Infolists\Components\Section::make('🔧 Detail Servis')
                    ->description('Informasi lengkap tentang servis yang dilakukan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->iconColor('success')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('jenis_servis')
                                    ->label('Jenis Servis')
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->badge()
                                    ->color('success')
                                    ->size('lg')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('tempat_servis')
                                    ->label('Tempat Servis')
                                    ->icon('heroicon-o-map-pin')
                                    ->badge()
                                    ->color('purple')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('biaya')
                                    ->label('Biaya Servis')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('danger'),
                            ]),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('📋 Deskripsi Servis')
                            ->icon('heroicon-o-document-text')
                            ->prose()
                            ->columnSpanFull()
                            ->color('gray'),
                    ]),

                Infolists\Components\Section::make('🔩 Sparepart yang Digunakan')
                    ->description('Daftar sparepart yang dipasang atau diganti')
                    ->icon('heroicon-o-squares-plus')
                    ->iconColor('warning')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('sparepart')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Sparepart')
                                    ->icon('heroicon-o-cube')
                                    ->weight('bold')
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('kode')
                                    ->label('Kode')
                                    ->icon('heroicon-o-qr-code')
                                    ->fontFamily('mono')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('jumlah')
                                    ->label('Jumlah')
                                    ->icon('heroicon-o-calculator')
                                    ->suffix(' pcs')
                                    ->badge()
                                    ->color('info')
                                    ->numeric(),

                                Infolists\Components\TextEntry::make('harga')
                                    ->label('Harga')
                                    ->icon('heroicon-o-banknotes')
                                    ->money('IDR')
                                    ->color('success')
                                    ->weight('bold'),
                            ])
                            ->columns(4)
                            ->columnSpanFull()

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn($record) => !empty($record->sparepart)),

                Infolists\Components\Section::make('💰 Ringkasan Biaya')
                    ->description('Total keseluruhan biaya servis')
                    ->icon('heroicon-o-calculator')
                    ->iconColor('success')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('biaya')
                                    ->label('Biaya Jasa')
                                    ->icon('heroicon-o-wrench')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('total_sparepart')
                                    ->label('Total Sparepart')
                                    ->icon('heroicon-o-cube-transparent')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('warning')
                                    ->state(function ($record) {
                                        return collect($record->sparepart ?? [])->sum(function ($item) {
                                            return ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
                                        });
                                    }),

                                Infolists\Components\TextEntry::make('total_keseluruhan')
                                    ->label('Total Keseluruhan')
                                    ->icon('heroicon-o-banknotes')
                                    ->money('IDR')
                                    ->size('xl')
                                    ->weight('bold')
                                    ->color('success')
                                    ->state(function ($record) {
                                        $biayaJasa = $record->biaya ?? 0;
                                        $totalSparepart = collect($record->sparepart ?? [])->sum(function ($item) {
                                            return ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
                                        });
                                        return $biayaJasa + $totalSparepart;
                                    }),
                            ])
                    ])
                    ->visible(fn($record) => !empty($record->sparepart)),

                Infolists\Components\Section::make('⚙️ Informasi Sistem')
                    ->description('Data audit dan riwayat perubahan')
                    ->icon('heroicon-o-server')
                    ->iconColor('gray')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->icon('heroicon-o-plus-circle')
                                    ->dateTime('d M Y H:i')
                                    ->color('success')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->icon('heroicon-o-pencil-square')
                                    ->dateTime('d M Y H:i')
                                    ->color('warning')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->icon('heroicon-o-trash')
                                    ->dateTime('d M Y H:i')
                                    ->color('danger')
                                    ->badge()
                                    ->visible(fn($record) => $record->trashed()),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}