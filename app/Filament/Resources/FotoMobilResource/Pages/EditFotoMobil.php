<?php

namespace App\Filament\Resources\FotoMobilResource\Pages;

use App\Filament\Resources\FotoMobilResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;

class EditFotoMobil extends EditRecord
{
    protected static string $resource = FotoMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    // Override form untuk edit - menggunakan single upload
    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\Select::make('mobil_id')
                            ->label('Mobil')
                            ->relationship('mobil', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        // Single file upload untuk edit
                        Forms\Components\FileUpload::make('path_file')
                            ->label('File Media')
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
                            ->maxSize(50 * 1024) // 50MB
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('jenis_media')
                            ->label('Jenis Media')
                            ->options([
                                'gambar' => 'Gambar',
                                'video' => 'Video',
                                'brosur' => 'Brosur',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('jenis_gambar', null)),

                        Forms\Components\Select::make('jenis_gambar')
                            ->label('Jenis Gambar')
                            ->options([
                                'eksterior' => 'Eksterior',
                                'interior' => 'Interior',
                                'fitur' => 'Fitur',
                                'thumbnail' => 'Thumbnail',
                                'galeri' => 'Galeri',
                            ])
                            ->visible(fn(callable $get) => $get('jenis_media') === 'gambar')
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pengaturan Tampilan')
                    ->schema([
                        Forms\Components\TextInput::make('urutan_tampil')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(999)
                            ->step(1),

                        Forms\Components\TextInput::make('teks_alternatif')
                            ->label('Teks Alternatif (Alt Text)')
                            ->maxLength(255)
                            ->helperText('Deskripsi singkat untuk accessibility'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}