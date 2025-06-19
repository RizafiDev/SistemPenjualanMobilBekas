<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriResource\Pages;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $pluralLabel = 'Kategori';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Kategori::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('URL-friendly version dari nama kategori'),

                        Forms\Components\Select::make('ikon')
                            ->options([
                                'heroicon-o-tag' => 'Tag',
                                'heroicon-o-folder' => 'Folder',
                                'heroicon-o-rectangle-stack' => 'Stack',
                                'heroicon-o-squares-2x2' => 'Grid',
                                'heroicon-o-list-bullet' => 'List',
                                'heroicon-o-bookmark' => 'Bookmark',
                                'heroicon-o-star' => 'Star',
                                'heroicon-o-heart' => 'Heart',
                                'heroicon-o-fire' => 'Fire',
                                'heroicon-o-light-bulb' => 'Light Bulb',
                                'heroicon-o-gift' => 'Gift',
                                'heroicon-o-trophy' => 'Trophy',
                                'heroicon-o-shopping-bag' => 'Shopping Bag',
                                'heroicon-o-home' => 'Home',
                                'heroicon-o-building-storefront' => 'Store',
                                'heroicon-o-device-phone-mobile' => 'Mobile',
                                'heroicon-o-computer-desktop' => 'Desktop',
                                'heroicon-o-camera' => 'Camera',
                                'heroicon-o-musical-note' => 'Music',
                                'heroicon-o-film' => 'Film',
                                'heroicon-o-book-open' => 'Book',
                                'heroicon-o-academic-cap' => 'Education',
                                'heroicon-o-briefcase' => 'Business',
                                'heroicon-o-wrench-screwdriver' => 'Tools',
                                'heroicon-o-cpu-chip' => 'Technology',
                            ])
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Pilih ikon untuk kategori'),

                        Forms\Components\TextInput::make('urutan_tampil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Urutan tampil kategori (0 = otomatis di akhir)'),

                        Forms\Components\Toggle::make('unggulan')
                            ->default(false)
                            ->helperText('Tandai sebagai kategori unggulan'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Masukkan deskripsi kategori...'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('urutan_tampil')
                    ->label('#')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->width(60),

                Tables\Columns\IconColumn::make('ikon')
                    ->icon(fn(string $state): string => $state ?: 'heroicon-o-tag')
                    ->size('lg')
                    ->color('primary')
                    ->width(60),

                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->fontFamily('mono')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),

                Tables\Columns\IconColumn::make('unggulan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable()
                    ->alignCenter()
                    ->label('Unggulan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\TernaryFilter::make('unggulan')
                    ->label('Status Unggulan')
                    ->placeholder('Semua kategori')
                    ->trueLabel('Hanya unggulan')
                    ->falseLabel('Hanya biasa'),

                Tables\Filters\SelectFilter::make('ikon')
                    ->options([
                        'heroicon-o-tag' => 'Tag',
                        'heroicon-o-folder' => 'Folder',
                        'heroicon-o-star' => 'Star',
                        'heroicon-o-heart' => 'Heart',
                        'heroicon-o-shopping-bag' => 'Shopping Bag',
                        'heroicon-o-home' => 'Home',
                    ])
                    ->label('Ikon'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),

                    Tables\Actions\Action::make('toggleUnggulan')
                        ->label(fn(Kategori $record) => $record->unggulan ? 'Hapus dari Unggulan' : 'Jadikan Unggulan')
                        ->icon(fn(Kategori $record) => $record->unggulan ? 'heroicon-o-star' : 'heroicon-s-star')
                        ->color(fn(Kategori $record) => $record->unggulan ? 'warning' : 'gray')
                        ->action(function (Kategori $record) {
                            $record->update(['unggulan' => !$record->unggulan]);
                        })
                        ->successNotificationTitle('Status unggulan berhasil diubah'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\BulkAction::make('jadikanUnggulan')
                        ->label('Jadikan Unggulan')
                        ->icon('heroicon-s-star')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['unggulan' => true]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('hapusDariUnggulan')
                        ->label('Hapus dari Unggulan')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['unggulan' => false]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('urutan_tampil', 'asc')
            ->reorderable('urutan_tampil')
            ->emptyStateHeading('Belum ada data kategori')
            ->emptyStateDescription('Mulai dengan menambahkan kategori pertama Anda.')
            ->emptyStateIcon('heroicon-o-tag');
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
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'view' => Pages\ViewKategori::route('/{record}'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama', 'slug', 'deskripsi'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Deskripsi' => $record->deskripsi ? Str::limit($record->deskripsi, 50) : '-',
            'Status' => $record->unggulan ? 'Unggulan' : 'Biasa',
            'Urutan' => $record->urutan_tampil,
        ];
    }
}