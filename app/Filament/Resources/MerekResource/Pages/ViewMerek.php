<?php

namespace App\Filament\Resources\MerekResource\Pages;

use App\Filament\Resources\MerekResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewMerek extends ViewRecord
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Merek: ' . $this->record->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Merek')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('nama')
                                        ->label('Nama Merek')
                                        ->weight('bold')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('slug')
                                        ->label('Slug')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->copyMessage('Slug berhasil disalin'),

                                    Infolists\Components\TextEntry::make('negara_asal')
                                        ->label('Negara Asal')
                                        ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state)))
                                        ->badge()
                                        ->color('info'),

                                    Infolists\Components\TextEntry::make('tahun_berdiri')
                                        ->label('Tahun Berdiri')
                                        ->numeric(),

                                    Infolists\Components\IconEntry::make('aktif')
                                        ->label('Status')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-badge')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ]),

                            Infolists\Components\ImageEntry::make('logo')
                                ->label('Logo')
                                ->circular()
                                ->size(120)
                                ->defaultImageUrl(url('/images/placeholder-logo.png'))
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