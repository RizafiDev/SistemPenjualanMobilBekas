<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static ?int $navigationSort = 3;

    public static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'nama_lengkap',
            'nik',
            'no_telepon',
            'email',
            'pekerjaan',
            'perusahaan',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pribadi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK')
                                    ->unique(ignoreRecord: true)
                                    ->length(16)
                                    ->numeric()
                                    ->placeholder('1234567890123456'),

                                Forms\Components\Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->options(Pelanggan::JENIS_KELAMIN)
                                    ->required(),

                                Forms\Components\DatePicker::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->maxDate(now())
                                    ->displayFormat('d/m/Y'),
                            ]),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Kontak')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('no_telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->placeholder('08123456789'),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('customer@example.com'),
                            ]),
                    ]),

                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('pekerjaan')
                                    ->label('Pekerjaan')
                                    ->maxLength(255)
                                    ->placeholder('Karyawan Swasta'),

                                Forms\Components\TextInput::make('perusahaan')
                                    ->label('Nama Perusahaan')
                                    ->maxLength(255)
                                    ->placeholder('PT. Contoh Indonesia'),
                            ]),
                    ]),

                Section::make('Data Tambahan')
                    ->schema([
                        Forms\Components\KeyValue::make('data_tambahan')
                            ->label('Data Tambahan')
                            ->keyLabel('Kunci')
                            ->valueLabel('Nilai')
                            ->addActionLabel('Tambah Data')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->placeholder('Belum diisi'),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'L' => 'blue',
                        'P' => 'pink',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => $state),

                Tables\Columns\TextColumn::make('umur')
                    ->label('Umur')
                    ->state(function (Pelanggan $record): string {
                        return $record->umur ? $record->umur . ' tahun' : '-';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('tanggal_lahir', $direction === 'asc' ? 'desc' : 'asc');
                    }),

                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Belum diisi')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Belum diisi')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pekerjaan')
                    ->label('Pekerjaan')
                    ->searchable()
                    ->placeholder('Belum diisi')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Kelengkapan')
                    ->state(function (Pelanggan $record): string {
                        return $record->getCompletionPercentage() . '%';
                    })
                    ->badge()
                    ->color(fn(Pelanggan $record): string => match (true) {
                        $record->getCompletionPercentage() >= 80 => 'success',
                        $record->getCompletionPercentage() >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Custom sorting logic for completion percentage
                        return $query->selectRaw('
                            pelanggans.*,
                            (
                                CASE WHEN nama_lengkap IS NOT NULL AND nama_lengkap != "" THEN 1 ELSE 0 END +
                                CASE WHEN nik IS NOT NULL AND nik != "" THEN 1 ELSE 0 END +
                                CASE WHEN no_telepon IS NOT NULL AND no_telepon != "" THEN 1 ELSE 0 END +
                                CASE WHEN email IS NOT NULL AND email != "" THEN 1 ELSE 0 END +
                                CASE WHEN alamat IS NOT NULL AND alamat != "" THEN 1 ELSE 0 END +
                                CASE WHEN tanggal_lahir IS NOT NULL THEN 1 ELSE 0 END +
                                CASE WHEN jenis_kelamin IS NOT NULL AND jenis_kelamin != "" THEN 1 ELSE 0 END +
                                CASE WHEN pekerjaan IS NOT NULL AND pekerjaan != "" THEN 1 ELSE 0 END
                            ) * 100 / 8 as completion_percentage
                        ')->orderBy('completion_percentage', $direction);
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(Pelanggan::JENIS_KELAMIN),

                Filter::make('has_complete_contact')
                    ->label('Kontak Lengkap')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('no_telepon')->whereNotNull('email'))
                    ->toggle(),

                Filter::make('has_complete_identity')
                    ->label('Identitas Lengkap')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('nik')->whereNotNull('tanggal_lahir')->whereNotNull('alamat'))
                    ->toggle(),

                Filter::make('age_range')
                    ->form([
                        Forms\Components\TextInput::make('min_age')
                            ->label('Umur Minimum')
                            ->numeric()
                            ->placeholder('18'),
                        Forms\Components\TextInput::make('max_age')
                            ->label('Umur Maksimum')
                            ->numeric()
                            ->placeholder('65'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_age'] ?? null,
                                fn(Builder $query, $minAge): Builder => $query->whereDate('tanggal_lahir', '<=', now()->subYears($minAge))
                            )
                            ->when(
                                $data['max_age'] ?? null,
                                fn(Builder $query, $maxAge): Builder => $query->whereDate('tanggal_lahir', '>=', now()->subYears($maxAge))
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_age'] ?? null) {
                            $indicators[] = 'Min umur: ' . $data['min_age'];
                        }
                        if ($data['max_age'] ?? null) {
                            $indicators[] = 'Max umur: ' . $data['max_age'];
                        }
                        return $indicators;
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
            ->emptyStateHeading('Belum ada data pelanggan')
            ->emptyStateDescription('Mulai dengan menambahkan pelanggan pertama.')
            ->emptyStateIcon('heroicon-o-users')
            ->defaultSort('created_at', 'desc')
            ->striped();
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
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'view' => Pages\ViewPelanggan::route('/{record}'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'primary';
    }
}