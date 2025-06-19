<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FotoMobilResource\Pages;
use App\Models\FotoMobil;
use App\Models\Mobil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FotoMobilResource extends Resource
{
    protected static ?string $model = FotoMobil::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Foto Mobil';

    protected static ?string $pluralModelLabel = 'Foto Mobil';

    protected static ?string $modelLabel = 'Foto Mobil';

    protected static ?string $navigationGroup = 'Manajemen Mobil';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
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

                        // Multiple file upload
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
                            ->maxSize(50 * 1024) // 50MB per file
                            ->multiple() // Enable multiple file upload
                            ->maxFiles(20) // Maximum 20 files
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Anda dapat memilih hingga 20 file sekaligus'),

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
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pengaturan Tampilan (untuk semua file)')
                    ->schema([
                        Forms\Components\TextInput::make('urutan_tampil_start')
                            ->label('Urutan Tampil Mulai Dari')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(999)
                            ->step(1)
                            ->helperText('File akan diurutkan secara berurutan mulai dari angka ini'),

                        Forms\Components\TextInput::make('teks_alternatif')
                            ->label('Teks Alternatif Base (Alt Text)')
                            ->maxLength(255)
                            ->helperText('Akan ditambahkan nomor urut untuk setiap file'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan (untuk semua file)')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path_file')
                    ->label('Preview')
                    ->size(60)
                    ->square()
                    ->defaultImageUrl(asset('images/no-image.png'))
                    ->extraAttributes(['class' => 'object-cover']),

                Tables\Columns\TextColumn::make('mobil.nama')
                    ->label('Mobil')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\BadgeColumn::make('jenis_media')
                    ->label('Jenis Media')
                    ->colors([
                        'success' => 'gambar',
                        'warning' => 'video',
                        'info' => 'brosur',
                    ])
                    ->icons([
                        'heroicon-o-photo' => 'gambar',
                        'heroicon-o-video-camera' => 'video',
                        'heroicon-o-document-text' => 'brosur',
                    ]),

                Tables\Columns\BadgeColumn::make('jenis_gambar')
                    ->label('Jenis Gambar')
                    ->colors([
                        'primary' => 'thumbnail',
                        'success' => 'eksterior',
                        'warning' => 'interior',
                        'info' => 'fitur',
                        'gray' => 'galeri',
                    ])
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('urutan_tampil')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('teks_alternatif')
                    ->label('Alt Text')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->getStateUsing(function ($record) {
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
                        return '—';
                    })
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('file_extension')
                    ->label('Format')
                    ->getStateUsing(fn($record) => strtoupper(pathinfo($record->path_file, PATHINFO_EXTENSION)))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mobil')
                    ->relationship('mobil', 'nama')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('jenis_media')
                    ->label('Jenis Media')
                    ->options([
                        'gambar' => 'Gambar',
                        'video' => 'Video',
                        'brosur' => 'Brosur',
                    ]),

                Tables\Filters\SelectFilter::make('jenis_gambar')
                    ->label('Jenis Gambar')
                    ->options([
                        'eksterior' => 'Eksterior',
                        'interior' => 'Interior',
                        'fitur' => 'Fitur',
                        'thumbnail' => 'Thumbnail',
                        'galeri' => 'Galeri',
                    ]),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('urutan_tampil', 'asc')
            ->reorderable('urutan_tampil')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFotoMobils::route('/'),
            'create' => Pages\CreateFotoMobil::route('/create'),
            'view' => Pages\ViewFotoMobil::route('/{record}'),
            'edit' => Pages\EditFotoMobil::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}