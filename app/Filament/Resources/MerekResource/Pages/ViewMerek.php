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
                // Header Section dengan Brand Identity
                Infolists\Components\Section::make('Informasi Merek')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('nama')
                                        ->label('Nama Merek')
                                        ->weight('bold')
                                        ->size('xl')
                                        ->color('primary')
                                        ->icon('heroicon-o-building-storefront'),

                                    Infolists\Components\TextEntry::make('slug')
                                        ->label('Slug')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->copyMessage('Slug berhasil disalin')
                                        ->icon('heroicon-o-link')
                                        ->color('gray')
                                        ->prefix('@'),

                                    Infolists\Components\TextEntry::make('negara_asal')
                                        ->label('Negara Asal')
                                        ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state)))
                                        ->badge()
                                        ->color('info')
                                        ->icon('heroicon-o-flag'),

                                    Infolists\Components\TextEntry::make('tahun_berdiri')
                                        ->label('Tahun Berdiri')
                                        ->numeric()
                                        ->icon('heroicon-o-calendar')
                                        ->color('success')
                                        ->weight('medium'),

                                    Infolists\Components\IconEntry::make('aktif')
                                        ->label('Status')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-badge')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger')
                                        ->size('lg'),
                                ]),

                            Infolists\Components\ImageEntry::make('logo')
                                ->label('Logo')
                                ->circular()
                                ->size(120)
                                ->defaultImageUrl(url('/images/placeholder-logo.png'))
                                ->extraAttributes(['class' => 'shadow-lg ring-4 ring-white dark:ring-gray-800'])
                                ->grow(false),
                        ])->from('lg'),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('primary'),

                // Deskripsi dengan styling yang lebih menarik
                Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('')
                            ->prose()
                            ->markdown()
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => 'text-justify leading-relaxed bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border'
                            ]),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->iconColor('success')
                    ->visible(fn($record) => !empty($record->deskripsi))
                    ->collapsible(),

                // Informasi Sistem dengan styling yang lebih baik
                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success')
                                    ->weight('medium'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('info')
                                    ->weight('medium'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->weight('medium')
                                    ->visible(fn($record) => $record->trashed()),
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}