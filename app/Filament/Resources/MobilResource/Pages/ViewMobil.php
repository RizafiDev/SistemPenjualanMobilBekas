<?php

namespace App\Filament\Resources\MobilResource\Pages;

use App\Filament\Resources\MobilResource;
use App\Models\Mobil;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewMobil extends ViewRecord
{
    protected static string $resource = MobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Mobil: ' . $this->record->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Mobil')
                                    ->weight('bold')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label('Slug')
                                    ->fontFamily('mono')
                                    ->copyable()
                                    ->copyMessage('Slug berhasil disalin'),

                                Infolists\Components\TextEntry::make('merek.nama')
                                    ->label('Merek')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('kategori.nama')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Spesifikasi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('rentang_tahun')
                                    ->label('Tahun Produksi')
                                    ->formatStateUsing(function ($record): string {
                                        if ($record->tahun_akhir) {
                                            return $record->tahun_mulai . ' - ' . $record->tahun_akhir;
                                        }
                                        return $record->tahun_mulai . ' - Sekarang';
                                    }),

                                Infolists\Components\TextEntry::make('tipe_bodi')
                                    ->label('Tipe Bodi')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => Mobil::TIPE_BODI[$state])
                                    ->color(fn(string $state): string => match ($state) {
                                        'sedan' => 'blue',
                                        'hatchback' => 'green',
                                        'suv' => 'orange',
                                        'mpv' => 'purple',
                                        'pickup' => 'red',
                                        'coupe' => 'pink',
                                        'convertible' => 'yellow',
                                        'wagon' => 'indigo',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('kapasitas_penumpang')
                                    ->label('Kapasitas Penumpang')
                                    ->suffix(' orang'),

                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => Mobil::STATUS[$state])
                                    ->color(fn(string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'dihentikan' => 'danger',
                                        'akan_datang' => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => !empty($record->deskripsi)),

                Infolists\Components\Section::make('Fitur Unggulan')
                    ->schema([
                        Infolists\Components\TextEntry::make('fitur_unggulan')
                            ->label('')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => !empty($record->fitur_unggulan)),

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