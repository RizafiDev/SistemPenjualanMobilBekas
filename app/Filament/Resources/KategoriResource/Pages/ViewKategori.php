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
                // Header Section dengan Card-like appearance
                Infolists\Components\Section::make('Informasi Kategori')
                    ->description('Detail lengkap kategori yang dipilih')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\Split::make([
                            // Main content area
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('nama')
                                        ->label('Nama Kategori')
                                        ->weight('bold')
                                        ->size('xl')
                                        ->color('primary')
                                        ->icon('heroicon-o-tag'),

                                    Infolists\Components\TextEntry::make('slug')
                                        ->label('Slug URL')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->copyMessage('✓ Slug berhasil disalin ke clipboard')
                                        ->copyMessageDuration(2000)
                                        ->icon('heroicon-o-link')
                                        ->color('gray'),

                                    Infolists\Components\TextEntry::make('urutan_tampil')
                                        ->label('Urutan Tampil')
                                        ->numeric()
                                        ->badge()
                                        ->color('info')
                                        ->icon('heroicon-o-list-bullet')
                                        ->suffix(fn($state) => $state == 1 ? ' (Paling Atas)' : ''),

                                    Infolists\Components\Group::make([
                                        Infolists\Components\IconEntry::make('unggulan')
                                            ->label('Status Unggulan')
                                            ->boolean()
                                            ->trueIcon('heroicon-s-star')
                                            ->falseIcon('heroicon-o-star')
                                            ->trueColor('warning')
                                            ->falseColor('gray')
                                            ->size('lg'),

                                        Infolists\Components\TextEntry::make('unggulan')
                                            ->label('')
                                            ->formatStateUsing(fn($state) => $state ? 'Kategori Unggulan' : 'Kategori Biasa')
                                            ->color(fn($state) => $state ? 'warning' : 'gray')
                                            ->weight(fn($state) => $state ? 'semibold' : 'normal')
                                            ->size('sm'),
                                    ])
                                        ->columns(2)
                                        ->columnSpan(1),
                                ]),

                            // Icon showcase area
                            Infolists\Components\Group::make([
                                Infolists\Components\TextEntry::make('ikon')
                                    ->label('Preview Ikon')
                                    ->formatStateUsing(fn($state) => $state ?: 'heroicon-o-tag')
                                    ->color('primary')
                                    ->weight('semibold')
                                    ->size('sm'),

                                Infolists\Components\IconEntry::make('ikon')
                                    ->label('')
                                    ->icon(fn(string $state): string => $state ?: 'heroicon-o-tag')
                                    ->size('3xl')
                                    ->color('primary'),
                            ])
                                ->columns(1)
                                ->grow(false),
                        ])->from('lg'),
                    ])
                    ->compact(),

                // Description Section dengan rich text
                Infolists\Components\Section::make('Deskripsi Kategori')
                    ->description('Penjelasan detail tentang kategori ini')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('')
                            ->prose()
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Tidak ada deskripsi yang tersedia'),
                    ])
                    ->visible(fn($record) => !empty($record->deskripsi))
                    ->compact(),

                // Statistics atau informasi tambahan (jika ada relasi)
                Infolists\Components\Section::make('Statistik')
                    ->description('Ringkasan penggunaan kategori')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                // Contoh jika ada relasi dengan posts/articles
                                Infolists\Components\TextEntry::make('posts_count')
                                    ->label('Total Postingan')
                                    ->numeric()
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-document-duplicate')
                                    ->default(0),

                                Infolists\Components\TextEntry::make('views_count')
                                    ->label('Total Dilihat')
                                    ->numeric()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-eye')
                                    ->default(0),

                                Infolists\Components\TextEntry::make('popularity_score')
                                    ->label('Skor Popularitas')
                                    ->badge()
                                    ->color(fn($state) => match (true) {
                                        $state >= 80 => 'success',
                                        $state >= 60 => 'warning',
                                        default => 'danger'
                                    })
                                    ->icon('heroicon-o-fire')
                                    ->suffix('%')
                                    ->default(0),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(false), // Set true jika ada data statistik

                // System Information dengan lebih detail
                Infolists\Components\Section::make('Informasi Sistem')
                    ->description('Detail teknis dan riwayat perubahan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('created_at')
                                        ->label('Dibuat')
                                        ->dateTime('d M Y, H:i')
                                        ->icon('heroicon-o-plus-circle')
                                        ->color('success'),

                                    Infolists\Components\TextEntry::make('created_at')
                                        ->label('')
                                        ->since()
                                        ->color('gray')
                                        ->size('sm'),
                                ])
                                    ->columns(1),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('updated_at')
                                        ->label('Diperbarui')
                                        ->dateTime('d M Y, H:i')
                                        ->icon('heroicon-o-pencil-square')
                                        ->color('warning'),

                                    Infolists\Components\TextEntry::make('updated_at')
                                        ->label('')
                                        ->since()
                                        ->color('gray')
                                        ->size('sm'),
                                ])
                                    ->columns(1),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('deleted_at')
                                        ->label('Dihapus')
                                        ->dateTime('d M Y, H:i')
                                        ->icon('heroicon-o-trash')
                                        ->color('danger'),

                                    Infolists\Components\TextEntry::make('deleted_at')
                                        ->label('')
                                        ->since()
                                        ->color('gray')
                                        ->size('sm'),
                                ])
                                    ->columns(1)
                                    ->visible(fn($record) => $record->trashed()),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('id')
                                        ->label('ID Kategori')
                                        ->badge()
                                        ->color('gray')
                                        ->icon('heroicon-o-hashtag')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('table_name')
                                        ->label('Tabel')
                                        ->formatStateUsing(fn() => 'categories')
                                        ->fontFamily('mono')
                                        ->color('gray')
                                        ->size('sm'),
                                ])
                                    ->columns(1),
                            ]),

                        // Audit trail jika ada
                        Infolists\Components\RepeatableEntry::make('audits')
                            ->label('Riwayat Aktivity')
                            ->schema([
                                Infolists\Components\TextEntry::make('event')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'gray'
                                    }),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime('d M Y, H:i')
                                    ->since(),

                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Oleh')
                                    ->default('System'),
                            ])
                            ->columns(3)
                            ->visible(false), // Set true jika menggunakan audit
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact(),

                // Quick Actions (opsional)
                Infolists\Components\Section::make('Aksi Cepat')
                    ->description('Tindakan yang dapat dilakukan')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('edit')
                                ->label('Edit Kategori')
                                ->icon('heroicon-o-pencil-square')
                                ->color('warning')
                                ->url(fn($record) => route('filament.admin.resources.categories.edit', $record)),

                            Infolists\Components\Actions\Action::make('duplicate')
                                ->label('Duplikat')
                                ->icon('heroicon-o-document-duplicate')
                                ->color('info'),

                            Infolists\Components\Actions\Action::make('toggle_featured')
                                ->label(fn($record) => $record->unggulan ? 'Hapus dari Unggulan' : 'Jadikan Unggulan')
                                ->icon(fn($record) => $record->unggulan ? 'heroicon-o-star' : 'heroicon-s-star')
                                ->color(fn($record) => $record->unggulan ? 'gray' : 'warning'),
                        ])
                    ])
                    ->compact()
                    ->visible(false), // Set true jika ingin menampilkan quick actions
            ]);
    }
}