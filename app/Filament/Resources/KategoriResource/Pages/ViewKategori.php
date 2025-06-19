<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewKategori extends ViewRecord
{
    protected static string $resource = KategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Kategori: ' . $this->record->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Kategori')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('nama')
                                        ->label('Nama Kategori')
                                        ->weight('bold')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('slug')
                                        ->label('Slug')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->copyMessage('Slug berhasil disalin'),

                                    Infolists\Components\TextEntry::make('urutan_tampil')
                                        ->label('Urutan Tampil')
                                        ->numeric()
                                        ->badge()
                                        ->color('info'),

                                    Infolists\Components\IconEntry::make('unggulan')
                                        ->label('Status Unggulan')
                                        ->boolean()
                                        ->trueIcon('heroicon-s-star')
                                        ->falseIcon('heroicon-o-star')
                                        ->trueColor('warning')
                                        ->falseColor('gray'),
                                ]),

                            Infolists\Components\IconEntry::make('ikon')
                                ->label('Ikon')
                                ->icon(fn(string $state): string => $state ?: 'heroicon-o-tag')
                                ->size('2xl')
                                ->color('primary')
                                ->grow(false),
                        ])->from('lg'),
                    ]),

                Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => !empty($record->deskripsi)),

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