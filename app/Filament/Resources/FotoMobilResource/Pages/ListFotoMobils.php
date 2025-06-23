<?php

namespace App\Filament\Resources\FotoMobilResource\Pages;

use App\Filament\Resources\FotoMobilResource;
use App\Models\FotoMobil;
use App\Models\Mobil;
use App\Traits\BulkUploadTrait;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Filament\Resources\FotoMobilResource\Widgets\FotoMobilStatsWidget;

class ListFotoMobils extends ListRecords
{
    use BulkUploadTrait;

    protected static string $resource = FotoMobilResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
            FotoMobilStatsWidget::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            // Action untuk bulk upload
            Actions\Action::make('bulk_upload')
                ->label('Upload Multiple Foto')
                ->icon('heroicon-o-photo')
                ->color('success')
                ->form([
                    Forms\Components\Section::make('Upload Multiple Foto')
                        ->schema([
                            Forms\Components\Select::make('mobil_id')
                                ->label('Mobil')
                                ->options(Mobil::pluck('nama', 'id'))
                                ->searchable()
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\FileUpload::make('files')
                                ->label('File Media (Multiple)')
                                ->directory('mobil-media')
                                ->acceptedFileTypes([
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                    'image/webp',
                                    'video/mp4',
                                    'video/mov',
                                    'video/avi',
                                    'application/pdf'
                                ])
                                ->maxSize(50 * 1024)
                                ->multiple()
                                ->maxFiles(30)
                                ->image()
                                ->imageEditor()
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio(null)
                                ->imageResizeTargetWidth('1920')
                                ->imageResizeTargetHeight('1080')
                                ->required()
                                ->columnSpanFull()
                                ->helperText('Pilih hingga 30 file sekaligus'),

                            Forms\Components\Select::make('jenis_media')
                                ->label('Jenis Media (untuk semua file)')
                                ->options([
                                    'gambar' => 'Gambar',
                                    'video' => 'Video',
                                    'brosur' => 'Brosur',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set) => $set('jenis_gambar', null)),

                            Forms\Components\Select::make('jenis_gambar')
                                ->label('Jenis Gambar (untuk semua file)')
                                ->options([
                                    'eksterior' => 'Eksterior',
                                    'interior' => 'Interior',
                                    'fitur' => 'Fitur',
                                    'thumbnail' => 'Thumbnail',
                                    'galeri' => 'Galeri',
                                ])
                                ->visible(fn(callable $get) => $get('jenis_media') === 'gambar')
                                ->nullable(),

                            Forms\Components\TextInput::make('urutan_tampil_start')
                                ->label('Urutan Tampil Mulai Dari')
                                ->numeric()
                                ->default(function () {
                                    return FotoMobil::max('urutan_tampil') + 1;
                                })
                                ->minValue(0)
                                ->maxValue(999)
                                ->step(1)
                                ->helperText('File akan diurutkan secara berurutan mulai dari angka ini'),

                            Forms\Components\TextInput::make('teks_alternatif_base')
                                ->label('Base Teks Alternatif')
                                ->maxLength(200)
                                ->helperText('Akan ditambahkan nomor urut untuk setiap file (contoh: "Foto Mobil 1", "Foto Mobil 2")'),

                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan (untuk semua file)')
                                ->maxLength(500)
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                ->action(function (array $data) {
                    // Validate files first
                    $validation = $this->validateBulkFiles($data['files'] ?? []);

                    if (!empty($validation['invalid_files'])) {
                        $invalidCount = count($validation['invalid_files']);
                        $invalidFileNames = collect($validation['invalid_files'])
                            ->pluck('file')
                            ->take(3)
                            ->map(fn($file) => basename($file))
                            ->join(', ');

                        Notification::make()
                            ->title('File Tidak Valid!')
                            ->body("Ditemukan {$invalidCount} file tidak valid: {$invalidFileNames}")
                            ->warning()
                            ->send();
                    }

                    // Process only valid files
                    if (!empty($validation['valid_files'])) {
                        $data['files'] = $validation['valid_files'];
                        $result = $this->processBulkUpload($data);

                        // Additional logging for debugging
                        if (!$result['success']) {
                            \Log::error('Bulk upload failed', $result);
                        }
                    } else {
                        Notification::make()
                            ->title('Tidak Ada File Valid!')
                            ->body('Semua file yang dipilih tidak valid untuk diupload.')
                            ->danger()
                            ->send();
                    }
                })
                ->modalHeading('Upload Multiple Foto Mobil')
                ->modalSubmitActionLabel('Upload Semua')
                ->modalWidth('4xl'),

            // Action create biasa untuk single upload
            Actions\CreateAction::make()
                ->label('Tambah Foto Tunggal')
                ->icon('heroicon-o-plus'),

            // Action untuk cleanup orphaned files
            Actions\Action::make('cleanup_files')
                ->label('Cleanup Files')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Cleanup Orphaned Files')
                ->modalDescription('Apakah Anda yakin ingin menghapus file-file yang tidak terpakai? Proses ini tidak dapat dibatalkan.')
                ->action(function () {
                    $cleanedCount = $this->cleanupOrphanedFiles();

                    if ($cleanedCount > 0) {
                        Notification::make()
                            ->title('Cleanup Berhasil!')
                            ->body("Berhasil menghapus {$cleanedCount} file yang tidak terpakai.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Tidak Ada File Yang Dihapus')
                            ->body('Tidak ditemukan file yang tidak terpakai.')
                            ->info()
                            ->send();
                    }
                })
                ->hidden(fn() => !auth()->user()->can('cleanup_files')), // Hide if user doesn't have permission

            // Action untuk reorder files
            Actions\Action::make('reorder_files')
                ->label('Reorder Files')
                ->icon('heroicon-o-bars-3')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('mobil_id')
                        ->label('Pilih Mobil')
                        ->options(Mobil::pluck('nama', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('jenis_media')
                        ->label('Jenis Media')
                        ->options([
                            'gambar' => 'Gambar',
                            'video' => 'Video',
                            'brosur' => 'Brosur',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    FotoMobil::reorderItems($data['mobil_id'], $data['jenis_media']);

                    Notification::make()
                        ->title('Reorder Berhasil!')
                        ->body('Urutan tampil telah diperbarui.')
                        ->success()
                        ->send();
                })
                ->modalHeading('Reorder Files')
                ->modalSubmitActionLabel('Reorder'),
        ];
    }

    /**
     * Additional table actions
     */
    protected function getTableActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn(FotoMobil $record): string => \Storage::url($record->path_file))
                ->openUrlInNewTab()
                ->visible(fn(FotoMobil $record): bool => $record->is_image || $record->is_video),

            Actions\Action::make('download')
                ->label('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (FotoMobil $record) {
                    return response()->download(
                        storage_path('app/public/' . $record->path_file),
                        $record->file_name
                    );
                }),
        ];
    }

}