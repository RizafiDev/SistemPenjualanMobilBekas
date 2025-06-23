<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerekResource\Pages;
use App\Models\Merek;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class MerekResource extends Resource
{
    protected static ?string $model = Merek::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Merek';

    protected static ?string $pluralLabel = 'Merek';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'nama';
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama', 'slug', 'negara_asal'];
    }

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
                            ->unique(Merek::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('URL-friendly version dari nama merek'),

                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('logos/mereks')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Maksimal 2MB, format: JPG, PNG, GIF'),

                        Forms\Components\Select::make('negara_asal')
                            ->options([
                                'indonesia' => 'Indonesia',
                                'jepang' => 'Jepang',
                                'korea_selatan' => 'Korea Selatan',
                                'china' => 'China',
                                'amerika_serikat' => 'Amerika Serikat',
                                'jerman' => 'Jerman',
                                'inggris' => 'Inggris',
                                'prancis' => 'Prancis',
                                'italia' => 'Italia',
                                'belanda' => 'Belanda',
                                'swedia' => 'Swedia',
                                'swiss' => 'Swiss',
                                'lainnya' => 'Lainnya',
                            ])
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('tahun_berdiri')
                            ->numeric()
                            ->minValue(1800)
                            ->maxValue(date('Y'))
                            ->rules(['digits:4'])
                            ->placeholder('Contoh: 1995'),

                        Forms\Components\Toggle::make('aktif')
                            ->default(true)
                            ->helperText('Status aktif/tidak aktif merek'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Masukkan deskripsi singkat tentang merek ini...'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder-logo.png')),

                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->fontFamily('mono')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('negara_asal')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tahun_berdiri')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('aktif')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

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

                Tables\Filters\SelectFilter::make('aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->label('Status'),

                Tables\Filters\SelectFilter::make('negara_asal')
                    ->options([
                        'indonesia' => 'Indonesia',
                        'jepang' => 'Jepang',
                        'korea_selatan' => 'Korea Selatan',
                        'china' => 'China',
                        'amerika_serikat' => 'Amerika Serikat',
                        'jerman' => 'Jerman',
                        'lainnya' => 'Lainnya',
                    ])
                    ->label('Negara Asal'),

                Tables\Filters\Filter::make('tahun_berdiri')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tahun')
                            ->displayFormat('Y')
                            ->format('Y'),
                        Forms\Components\DatePicker::make('sampai_tahun')
                            ->displayFormat('Y')
                            ->format('Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tahun'],
                                fn(Builder $query, $date): Builder => $query->where('tahun_berdiri', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tahun'],
                                fn(Builder $query, $date): Builder => $query->where('tahun_berdiri', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\BulkAction::make('toggleAktif')
                        ->label('Toggle Status Aktif')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['aktif' => !$record->aktif]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada data merek')
            ->emptyStateDescription('Mulai dengan menambahkan merek pertama Anda.')
            ->emptyStateIcon('heroicon-o-building-office');
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
            'index' => Pages\ListMereks::route('/'),
            'create' => Pages\CreateMerek::route('/create'),
            'view' => Pages\ViewMerek::route('/{record}'),
            'edit' => Pages\EditMerek::route('/{record}/edit'),
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

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Negara Asal' => $record->negara_asal ? ucwords(str_replace('_', ' ', $record->negara_asal)) : '-',
            'Tahun Berdiri' => $record->tahun_berdiri ?? '-',
            'Status' => $record->aktif ? 'Aktif' : 'Tidak Aktif',
        ];
    }
}