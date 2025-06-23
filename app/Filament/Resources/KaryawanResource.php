<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Karyawan';

    protected static ?string $pluralModelLabel = 'Karyawan';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 1;

    public static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'nip',
            'nik',
            'nama_lengkap',
            'email',
            'no_telepon',
            'jabatan',
            'departemen',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Personal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nip')
                                    ->label('NIP')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->dehydrated(fn($state): bool => filled($state))
                                    ->maxLength(255),

                                TextInput::make('no_telepon')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->maxLength(255),

                                Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->required(),

                                DatePicker::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->required()
                                    ->native(false),
                            ]),

                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('foto')
                            ->label('Foto')
                            ->image()
                            ->directory('karyawan-foto')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('jabatan')
                                    ->label('Jabatan')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('departemen')
                                    ->label('Departemen')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->step(0.01),

                                DatePicker::make('tanggal_masuk')
                                    ->label('Tanggal Masuk')
                                    ->required()
                                    ->native(false),

                                Select::make('status')
                                    ->label('Status Karyawan')
                                    ->options([
                                        'tetap' => 'Tetap',
                                        'kontrak' => 'Kontrak',
                                        'magang' => 'Magang',
                                    ])
                                    ->default('tetap')
                                    ->required(),

                                Select::make('aktif')
                                    ->label('Status Aktif')
                                    ->options([
                                        'aktif' => 'Aktif',
                                        'nonaktif' => 'Non-Aktif',
                                    ])
                                    ->default('aktif')
                                    ->required(),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Data Tambahan')
                    ->schema([
                        KeyValue::make('data_tambahan')
                            ->label('Data Tambahan')
                            ->keyLabel('Kunci')
                            ->valueLabel('Nilai')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('departemen')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'tetap' => 'success',
                        'kontrak' => 'warning',
                        'magang' => 'info',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                TextColumn::make('aktif')
                    ->label('Status Aktif')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    }),

                TextColumn::make('tanggal_masuk')
                    ->label('Tgl. Masuk')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('departemen')
                    ->label('Departemen')
                    ->options(function () {
                        return Karyawan::distinct('departemen')
                            ->pluck('departemen', 'departemen')
                            ->toArray();
                    }),

                SelectFilter::make('status')
                    ->label('Status Karyawan')
                    ->options([
                        'tetap' => 'Tetap',
                        'kontrak' => 'Kontrak',
                        'magang' => 'Magang',
                    ]),

                SelectFilter::make('aktif')
                    ->label('Status Aktif')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    ]),

                Filter::make('tanggal_masuk')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_masuk', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_masuk', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'view' => Pages\ViewKaryawan::route('/{record}'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}