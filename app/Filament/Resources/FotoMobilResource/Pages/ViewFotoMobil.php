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
                // Hero Section - Preview Media dengan styling yang lebih menarik
                Infolists\Components\Section::make('📸 Preview Media')
                    ->description('Tampilan media yang diunggah')
                    ->schema([
                        Infolists\Components\Split::make([
                            // Image Preview dengan hover effects
                            Infolists\Components\ImageEntry::make('path_file')
                                ->label('')
                                ->size(450)
                                ->extraAttributes([
                                    'class' => 'rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 border-4 border-white',
                                    'style' => 'filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));'
                                ])
                                ->visible(fn($record) => $record->jenis_media === 'gambar'),

                            // Non-image media dengan card styling
                            Infolists\Components\Group::make([
                                Infolists\Components\TextEntry::make('path_file')
                                    ->label('📁 File Media')
                                    ->formatStateUsing(function ($state, $record) {
                                        $fileName = basename($state);
                                        $extension = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));
                                        return "📄 {$fileName}";
                                    })
                                    ->url(fn($record) => asset('storage/' . $record->path_file))
                                    ->openUrlInNewTab()
                                    ->badge()
                                    ->color(fn($record) => match ($record->jenis_media) {
                                        'video' => 'danger',
                                        'brosur' => 'warning',
                                        default => 'info'
                                    })
                                    ->icon(fn($record) => match ($record->jenis_media) {
                                        'video' => 'heroicon-o-play-circle',
                                        'brosur' => 'heroicon-o-document-text',
                                        default => 'heroicon-o-document'
                                    })
                                    ->size('lg'),
                            ])
                                ->extraAttributes(['class' => 'bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border'])
                                ->visible(fn($record) => $record->jenis_media !== 'gambar'),
                        ])->from('lg'),
                    ])
                    ->headerActions([
                        // Action untuk download
                        \Filament\Infolists\Components\Actions\Action::make('download')
                            ->label('Download')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->url(fn($record) => asset('storage/' . $record->path_file))
                            ->openUrlInNewTab(),
                    ])
                    ->extraAttributes(['class' => 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500']),

                // Informasi Media dengan card design
                Infolists\Components\Section::make('ℹ️ Informasi Media')
                    ->description('Detail lengkap tentang media')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('mobil.nama_mobil')
                                    ->label('🚗 Nama Mobil')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('primary')
                                    ->badge()
                                    ->icon('heroicon-o-truck')
                                    ->extraAttributes(['class' => 'text-center']),

                                Infolists\Components\TextEntry::make('jenis_media')
                                    ->label('📋 Jenis Media')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn($state) => match ($state) {
                                        'gambar' => 'success',
                                        'video' => 'danger',
                                        'brosur' => 'warning',
                                        default => 'gray'
                                    })
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'gambar' => '🖼️ Gambar',
                                        'video' => '🎥 Video',
                                        'brosur' => '📄 Brosur',
                                        default => '📁 ' . ucfirst($state)
                                    })
                                    ->icon(fn($state) => match ($state) {
                                        'gambar' => 'heroicon-o-photo',
                                        'video' => 'heroicon-o-video-camera',
                                        'brosur' => 'heroicon-o-document-text',
                                        default => 'heroicon-o-document'
                                    }),

                                Infolists\Components\TextEntry::make('jenis_gambar')
                                    ->label('🎨 Kategori Gambar')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn($state) => match ($state) {
                                        'thumbnail' => 'primary',
                                        'eksterior' => 'success',
                                        'interior' => 'warning',
                                        'fitur' => 'info',
                                        'galeri' => 'gray',
                                        default => 'secondary'
                                    })
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'thumbnail' => '🏷️ Thumbnail',
                                        'eksterior' => '🌅 Eksterior',
                                        'interior' => '🏠 Interior',
                                        'fitur' => '⚡ Fitur',
                                        'galeri' => '🖼️ Galeri',
                                        default => ucfirst($state)
                                    })
                                    ->visible(fn($record) => $record->jenis_media === 'gambar' && !empty($record->jenis_gambar))
                                    ->placeholder('—'),
                            ]),

                        Infolists\Components\Fieldset::make('📊 Detail Tampilan')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('urutan_tampil')
                                            ->label('🔢 Urutan Tampil')
                                            ->numeric()
                                            ->badge()
                                            ->color('info')
                                            ->size('lg')
                                            ->formatStateUsing(fn($state) => "#{$state}")
                                            ->icon('heroicon-o-list-bullet'),

                                        Infolists\Components\TextEntry::make('file_name')
                                            ->label('📝 Nama File')
                                            ->fontFamily('mono')
                                            ->copyable()
                                            ->copyMessage('✅ Nama file berhasil disalin!')
                                            ->copyMessageDuration(2000)
                                            ->badge()
                                            ->color('gray')
                                            ->icon('heroicon-o-document-duplicate')
                                            ->limit(30)
                                            ->tooltip(fn($record) => $record->file_name),
                                    ]),
                            ])
                    ])
                    ->extraAttributes(['class' => 'bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500']),

                // Teks & Keterangan dengan rich text styling
                Infolists\Components\Section::make('📝 Teks & Keterangan')
                    ->description('Informasi tambahan dan deskripsi')
                    ->schema([
                        Infolists\Components\Grid::make(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('teks_alternatif')
                                    ->label('🔤 Teks Alternatif (Alt Text)')
                                    ->prose()
                                    ->placeholder('💭 Tidak ada teks alternatif')
                                    ->icon('heroicon-o-language')
                                    ->extraAttributes(['class' => 'bg-yellow-50 p-4 rounded-lg border border-yellow-200'])
                                    ->visible(fn($record) => !empty($record->teks_alternatif)),

                                Infolists\Components\TextEntry::make('keterangan')
                                    ->label('💬 Keterangan')
                                    ->prose()
                                    ->placeholder('📝 Tidak ada keterangan')
                                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                    ->extraAttributes(['class' => 'bg-blue-50 p-4 rounded-lg border border-blue-200'])
                                    ->visible(fn($record) => !empty($record->keterangan)),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->teks_alternatif) || !empty($record->keterangan))
                    ->extraAttributes(['class' => 'bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500']),

                // Informasi File dengan advanced styling
                Infolists\Components\Section::make('💾 Informasi File')
                    ->description('Detail teknis file media')
                    ->schema([
                        Infolists\Components\Grid::make(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('file_url')
                                    ->label('🌐 URL File')
                                    ->url(fn($record) => $record->file_url)
                                    ->openUrlInNewTab()
                                    ->copyable()
                                    ->copyMessage('🔗 URL berhasil disalin!')
                                    ->limit(60)
                                    ->tooltip(fn($record) => $record->file_url)
                                    ->icon('heroicon-o-link')
                                    ->badge()
                                    ->color('info')
                                    ->extraAttributes(['class' => 'font-mono text-sm']),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('path_file')
                                    ->label('📂 Path File')
                                    ->fontFamily('mono')
                                    ->copyable()
                                    ->copyMessage('📁 Path berhasil disalin!')
                                    ->limit(45)
                                    ->tooltip(fn($record) => $record->path_file)
                                    ->icon('heroicon-o-folder')
                                    ->badge()
                                    ->color('gray')
                                    ->size('sm'),

                                Infolists\Components\TextEntry::make('file_size')
                                    ->label('📏 Ukuran File')
                                    ->formatStateUsing(function ($record) {
                                        $filePath = storage_path('app/public/' . $record->path_file);
                                        if (file_exists($filePath)) {
                                            $bytes = filesize($filePath);
                                            $units = ['B', 'KB', 'MB', 'GB'];
                                            $bytes = max($bytes, 0);
                                            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                                            $pow = min($pow, count($units) - 1);
                                            $bytes /= (1 << (10 * $pow));
                                            return '💾 ' . round($bytes, 2) . ' ' . $units[$pow];
                                        }
                                        return '❓ Unknown';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $filePath = storage_path('app/public/' . $record->path_file);
                                        if (file_exists($filePath)) {
                                            $bytes = filesize($filePath);
                                            $mb = $bytes / (1024 * 1024);
                                            if ($mb > 10)
                                                return 'danger';
                                            if ($mb > 5)
                                                return 'warning';
                                            return 'success';
                                        }
                                        return 'gray';
                                    })
                                    ->icon('heroicon-o-scale'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->extraAttributes(['class' => 'bg-gradient-to-r from-orange-50 to-red-50 border-l-4 border-orange-500']),

                // Informasi Sistem dengan timeline design
                Infolists\Components\Section::make('🕐 Informasi Sistem')
                    ->description('Riwayat aktivitas sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('➕ Dibuat')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-plus-circle')
                                    ->badge()
                                    ->color('success')
                                    ->extraAttributes(['class' => 'text-center']),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('✏️ Diperbarui')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-pencil-square')
                                    ->badge()
                                    ->color('warning')
                                    ->extraAttributes(['class' => 'text-center']),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('🗑️ Dihapus')
                                    ->dateTime('d M Y H:i')
                                    ->visible(fn($record) => $record->trashed())
                                    ->icon('heroicon-o-trash')
                                    ->badge()
                                    ->color('danger')
                                    ->extraAttributes(['class' => 'text-center']),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->extraAttributes(['class' => 'bg-gradient-to-r from-gray-50 to-slate-50 border-l-4 border-gray-400']),
            ])
            ->columns(1); // Ensure single column layout for better mobile experience
    }
}