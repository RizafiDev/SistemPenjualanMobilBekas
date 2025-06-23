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
                // Header Section dengan informasi utama
                Infolists\Components\Section::make('')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Mobil')
                                    ->weight('bold')
                                    ->size('xl')
                                    ->color('primary')
                                    ->icon('heroicon-o-star'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label('Slug')
                                    ->fontFamily('mono')
                                    ->copyable()
                                    ->copyMessage('Slug berhasil disalin')
                                    ->icon('heroicon-o-hashtag')
                                    ->color('gray'),
                            ]),
                    ])
                    ->compact(),

                // Brand & Category dengan visual yang menarik
                Infolists\Components\Section::make('Klasifikasi')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('merek.nama')
                                    ->label('Merek')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg')
                                    ->icon('heroicon-o-building-office'),

                                Infolists\Components\TextEntry::make('kategori.nama')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('info')
                                    ->size('lg')
                                    ->icon('heroicon-o-tag'),
                            ]),
                    ])
                    ->icon('heroicon-o-folder')
                    ->iconColor('primary'),

                // Spesifikasi dengan layout yang lebih rapi
                Infolists\Components\Section::make('Spesifikasi Teknis')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('rentang_tahun')
                                    ->label('Tahun Produksi')
                                    ->icon('heroicon-o-calendar-days')
                                    ->weight('semibold')
                                    ->formatStateUsing(function ($record): string {
                                        if ($record->tahun_akhir) {
                                            return $record->tahun_mulai . ' - ' . $record->tahun_akhir;
                                        }
                                        return $record->tahun_mulai . ' - Sekarang';
                                    }),

                                Infolists\Components\TextEntry::make('kapasitas_penumpang')
                                    ->label('Kapasitas Penumpang')
                                    ->suffix(' orang')
                                    ->icon('heroicon-o-users')
                                    ->weight('semibold')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('tipe_bodi')
                                    ->label('Tipe Bodi')
                                    ->badge()
                                    ->size('lg')
                                    ->icon('heroicon-o-truck')
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

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status Produksi')
                                    ->badge()
                                    ->size('lg')
                                    ->icon('heroicon-o-signal')
                                    ->formatStateUsing(fn(string $state): string => Mobil::STATUS[$state])
                                    ->color(fn(string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'dihentikan' => 'danger',
                                        'akan_datang' => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('info'),

                // Deskripsi dengan styling yang lebih menarik
                Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('')
                            ->prose()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'text-justify leading-relaxed']),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->iconColor('gray')
                    ->visible(fn($record) => !empty($record->deskripsi)),

                // Fitur unggulan dengan highlight
                Infolists\Components\Section::make('Fitur Unggulan')
                    ->schema([
                        Infolists\Components\TextEntry::make('fitur_unggulan')
                            ->label('')
                            ->prose()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'text-justify leading-relaxed']),
                    ])
                    ->icon('heroicon-o-star')
                    ->iconColor('warning')
                    ->visible(fn($record) => !empty($record->fitur_unggulan)),

                // Informasi sistem dengan styling minimal
                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->visible(fn($record) => $record->trashed()),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('gray')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}