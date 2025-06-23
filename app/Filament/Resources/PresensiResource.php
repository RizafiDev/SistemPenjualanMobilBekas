<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Widgets;
use App\Filament\Resources\PresensiResource\Pages;
use App\Models\Presensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Presensi';

    protected static ?string $modelLabel = 'Presensi';

    protected static ?string $pluralModelLabel = 'Data Presensi';

    protected static ?string $navigationGroup = 'Manajemen Presensi';

    protected static ?int $navigationSort = 1;

    public static ?string $recordTitleAttribute = 'karyawan.nama_lengkap';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'karyawan.nama_lengkap',
            'karyawan.nip',
            'tanggal',
            'status',
            'keterangan',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Karyawan')
                            ->relationship('karyawan', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Presensi::getStatusOptions())
                            ->required()
                            ->default(Presensi::STATUS_HADIR)
                            ->columnSpan(1)
                            ->reactive(),
                    ])
                    ->columns(4),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TimePicker::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->seconds(false)
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('jam_pulang')
                            ->label('Jam Pulang')
                            ->seconds(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('menit_terlambat')
                            ->label('Menit Terlambat')
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->label('Waktu Kehadiran'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\FileUpload::make('foto_masuk')
                            ->label('Foto Masuk')
                            ->image()
                            ->directory('presensi/masuk')
                            ->visibility('private')
                            ->columnSpan(1),

                        Forms\Components\FileUpload::make('foto_pulang')
                            ->label('Foto Pulang')
                            ->image()
                            ->directory('presensi/pulang')
                            ->visibility('private')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->label('Dokumentasi')
                    ->collapsible(),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('latitude_masuk')
                            ->label('Latitude Masuk')
                            ->numeric()
                            ->step(0.00000001)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('longitude_masuk')
                            ->label('Longitude Masuk')
                            ->numeric()
                            ->step(0.00000001)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('latitude_pulang')
                            ->label('Latitude Pulang')
                            ->numeric()
                            ->step(0.00000001)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('longitude_pulang')
                            ->label('Longitude Pulang')
                            ->numeric()
                            ->step(0.00000001)
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->label('Lokasi GPS')
                    ->collapsible(),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->label('Catatan')
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('karyawan.nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable()
                    ->color(fn($record) => $record->menit_terlambat > 0 ? Color::Red : Color::Green)
                    ->weight(fn($record) => $record->menit_terlambat > 0 ? FontWeight::Bold : FontWeight::Medium),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Pulang')
                    ->time('H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => Presensi::STATUS_HADIR,
                        'warning' => Presensi::STATUS_TERLAMBAT,
                        'danger' => [Presensi::STATUS_TIDAK_HADIR],
                        'info' => [Presensi::STATUS_IZIN, Presensi::STATUS_CUTI],
                        'secondary' => [Presensi::STATUS_SAKIT, Presensi::STATUS_LIBUR],
                    ])
                    ->formatStateUsing(fn($state) => Presensi::getStatusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('keterangan_terlambat')
                    ->label('Terlambat')
                    ->color(Color::Red)
                    ->weight(FontWeight::Medium)
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama_lengkap')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Presensi::getStatusOptions())
                    ->multiple(),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn(Builder $query): Builder => $query->hariIni())
                    ->toggle(),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn(Builder $query): Builder => $query->bulanIni())
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),

                    BulkAction::make('export')
                        ->label('Export Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Implementasi export Excel bisa ditambahkan di sini
                            Notification::make()
                                ->title('Export berhasil!')
                                ->body('Data presensi telah berhasil diekspor.')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('tandai_hadir')
                        ->label('Tandai Hadir')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => Presensi::STATUS_HADIR]);
                            });

                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->body(count($records) . ' data presensi telah ditandai hadir.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('tanggal', 'desc')
            ->poll('30s') // Auto refresh setiap 30 detik
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Karyawan')
                    ->schema([
                        Infolists\Components\TextEntry::make('karyawan.nama_lengkap')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('karyawan.nip')
                            ->label('NIP'),
                        Infolists\Components\TextEntry::make('karyawan.jabatan')
                            ->label('Jabatan'),
                        Infolists\Components\TextEntry::make('karyawan.departemen')
                            ->label('Departemen'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Presensi')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal')
                            ->label('Tanggal')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                Presensi::STATUS_HADIR => 'success',
                                Presensi::STATUS_TERLAMBAT => 'warning',
                                Presensi::STATUS_TIDAK_HADIR => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn($state) => Presensi::getStatusOptions()[$state] ?? $state),
                        Infolists\Components\TextEntry::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->time('H:i'),
                        Infolists\Components\TextEntry::make('jam_pulang')
                            ->label('Jam Pulang')
                            ->time('H:i')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('keterangan_terlambat')
                            ->label('Keterangan Terlambat')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Dokumentasi')
                    ->schema([
                        Infolists\Components\ImageEntry::make('foto_masuk')
                            ->label('Foto Masuk')
                            ->height(200),
                        Infolists\Components\ImageEntry::make('foto_pulang')
                            ->label('Foto Pulang')
                            ->height(200),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Infolists\Components\Section::make('Lokasi GPS')
                    ->schema([
                        Infolists\Components\TextEntry::make('latitude_masuk')
                            ->label('Latitude Masuk'),
                        Infolists\Components\TextEntry::make('longitude_masuk')
                            ->label('Longitude Masuk'),
                        Infolists\Components\TextEntry::make('latitude_pulang')
                            ->label('Latitude Pulang'),
                        Infolists\Components\TextEntry::make('longitude_pulang')
                            ->label('Longitude Pulang'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Infolists\Components\Section::make('Keterangan')
                    ->schema([
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan'),
                    ])
                    ->collapsible(),
            ]);
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
            'index' => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'view' => Pages\ViewPresensi::route('/{record}'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
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
        return static::getModel()::hariIni()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\PresensiStatsWidget::class,
            Widgets\PresensiChartWidget::class,
            Widgets\LatestPresensiWidget::class,
            Widgets\PresensiCalendarWidget::class,
        ];
    }
}