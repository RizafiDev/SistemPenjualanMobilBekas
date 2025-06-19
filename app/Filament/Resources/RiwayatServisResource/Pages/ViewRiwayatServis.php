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
                Infolists\Components\Section::make('Informasi Mobil')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('stokMobil.mobil.nama')
                                        ->label('Mobil')
                                        ->weight('bold')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('stokMobil.no_rangka')
                                        ->label('No. Rangka')
                                        ->fontFamily('mono')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('tanggal_servis')
                                        ->label('Tanggal Servis')
                                        ->date(),

                                    Infolists\Components\TextEntry::make('kilometer_servis')
                                        ->label('Kilometer')
                                        ->numeric(),
                                ]),

                            Infolists\Components\ImageEntry::make('foto_bukti')
                                ->label('Foto Bukti')
                                ->circular()
                                ->stacked()
                                ->size(120)
                                ->grow(false)
                                ->visible(fn($record) => !empty($record->foto_bukti)),
                        ])->from('lg'),
                    ]),

                Infolists\Components\Section::make('Detail Servis')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('jenis_servis')
                                    ->label('Jenis Servis'),

                                Infolists\Components\TextEntry::make('tempat_servis')
                                    ->label('Tempat Servis'),

                                Infolists\Components\TextEntry::make('biaya')
                                    ->label('Biaya')
                                    ->money('IDR'),
                            ]),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Sparepart')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('sparepart')
                            ->schema([
                                Infolists\Components\TextEntry::make('nama'),
                                Infolists\Components\TextEntry::make('kode'),
                                Infolists\Components\TextEntry::make('jumlah'),
                                Infolists\Components\TextEntry::make('harga')
                                    ->money('IDR'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn($record) => !empty($record->sparepart)),

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