<?php

namespace App\Filament\Resources\FotoMobilResource\Pages;

use App\Filament\Resources\FotoMobilResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewFotoMobil extends ViewRecord
{
    protected static string $resource = FotoMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Foto: ' . $this->record->mobil->nama_mobil ?? 'Foto Mobil';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Preview Media')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\ImageEntry::make('path_file')
                                ->label('')
                                ->size(400)
                                ->extraAttributes(['class' => 'rounded-lg shadow-lg'])
                                ->visible(fn($record) => $record->jenis_media === 'gambar'),

                            Infolists\Components\TextEntry::make('path_file')
                                ->label('File Video/Brosur')
                                ->formatStateUsing(fn($state) => basename($state))
                                ->url(fn($record) => asset('storage/' . $record->path_file))
                                ->openUrlInNewTab()
                                ->icon(fn($record) => match ($record->jenis_media) {
                                    'video' => 'heroicon-o-video-camera',
                                    'brosur' => 'heroicon-o-document-text',
                                    default => 'heroicon-o-document'
                                })
                                ->visible(fn($record) => $record->jenis_media !== 'gambar'),
                        ])->from('lg'),
                    ]),

                Infolists\Components\Section::make('Informasi Media')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('mobil.nama_mobil')
                                    ->label('Mobil')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->icon('heroicon-o-truck'),

                                Infolists\Components\TextEntry::make('jenis_media')
                                    ->label('Jenis Media')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'gambar' => 'success',
                                        'video' => 'warning',
                                        'brosur' => 'info',
                                        default => 'gray'
                                    })
                                    ->icon(fn($state) => match ($state) {
                                        'gambar' => 'heroicon-o-photo',
                                        'video' => 'heroicon-o-video-camera',
                                        'brosur' => 'heroicon-o-document-text',
                                        default => 'heroicon-o-document'
                                    }),

                                Infolists\Components\TextEntry::make('jenis_gambar')
                                    ->label('Jenis Gambar')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'thumbnail' => 'primary',
                                        'eksterior' => 'success',
                                        'interior' => 'warning',
                                        'fitur' => 'info',
                                        'galeri' => 'gray',
                                        default => 'secondary'
                                    })
                                    ->visible(fn($record) => $record->jenis_media === 'gambar' && !empty($record->jenis_gambar))
                                    ->placeholder('—'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('urutan_tampil')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-list-bullet'),

                                Infolists\Components\TextEntry::make('file_name')
                                    ->label('Nama File')
                                    ->fontFamily('mono')
                                    ->copyable()
                                    ->copyMessage('Nama file berhasil disalin')
                                    ->icon('heroicon-o-document-duplicate'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Teks & Keterangan')
                    ->schema([
                        Infolists\Components\TextEntry::make('teks_alternatif')
                            ->label('Teks Alternatif (Alt Text)')
                            ->prose()
                            ->placeholder('Tidak ada teks alternatif')
                            ->icon('heroicon-o-language'),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->prose()
                            ->placeholder('Tidak ada keterangan')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis'),
                    ])
                    ->visible(fn($record) => !empty($record->teks_alternatif) || !empty($record->keterangan))
                    ->columns(1),

                Infolists\Components\Section::make('Informasi File')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('file_url')
                                    ->label('URL File')
                                    ->url(fn($record) => $record->file_url)
                                    ->openUrlInNewTab()
                                    ->copyable()
                                    ->copyMessage('URL berhasil disalin')
                                    ->limit(50)
                                    ->tooltip(fn($record) => $record->file_url)
                                    ->icon('heroicon-o-link'),

                                Infolists\Components\TextEntry::make('path_file')
                                    ->label('Path File')
                                    ->fontFamily('mono')
                                    ->copyable()
                                    ->copyMessage('Path berhasil disalin')
                                    ->limit(40)
                                    ->tooltip(fn($record) => $record->path_file)
                                    ->icon('heroicon-o-folder'),

                                Infolists\Components\TextEntry::make('file_size')
                                    ->label('Ukuran File')
                                    ->formatStateUsing(function ($record) {
                                        $filePath = storage_path('app/public/' . $record->path_file);
                                        if (file_exists($filePath)) {
                                            $bytes = filesize($filePath);
                                            $units = ['B', 'KB', 'MB', 'GB'];
                                            $bytes = max($bytes, 0);
                                            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                                            $pow = min($pow, count($units) - 1);
                                            $bytes /= (1 << (10 * $pow));
                                            return round($bytes, 2) . ' ' . $units[$pow];
                                        }
                                        return 'Unknown';
                                    })
                                    ->icon('heroicon-o-scale'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-plus-circle'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-pencil-square'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->dateTime('d M Y H:i')
                                    ->visible(fn($record) => $record->trashed())
                                    ->icon('heroicon-o-trash')
                                    ->color('danger'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}