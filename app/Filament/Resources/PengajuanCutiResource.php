<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanCutiResource\Pages;
use App\Models\PengajuanCuti;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Notifications\Notification;

class PengajuanCutiResource extends Resource
{
    protected static ?string $model = PengajuanCuti::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Pengajuan Cuti';

    protected static ?string $modelLabel = 'Pengajuan Cuti';

    protected static ?string $pluralModelLabel = 'Pengajuan Cuti';

    protected static ?string $navigationGroup = 'Manajemen Presensi';

    protected static ?int $navigationSort = 2;

    public static ?string $recordTitleAttribute = 'karyawan.nama_lengkap';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'karyawan.nama_lengkap',
            'karyawan.nip',
            'jenis',
            'status',
            'alasan',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Karyawan')
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Karyawan')
                            ->relationship('karyawan', 'nama_lengkap')
                            ->searchable(['nama_lengkap', 'nip'])
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(Karyawan $record): string => "{$record->nama_lengkap} ({$record->nip})")
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Cuti')
                    ->schema([
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Cuti')
                            ->options(PengajuanCuti::getJenisOptions())
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                self::calculateDays($state, $get('tanggal_selesai'), $set)),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                self::calculateDays($get('tanggal_mulai'), $state, $set))
                            ->afterOrEqual('tanggal_mulai'),

                        Forms\Components\TextInput::make('jumlah_hari')
                            ->label('Jumlah Hari')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(false),

                        Forms\Components\Textarea::make('alasan')
                            ->label('Alasan Cuti')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('dokumen')
                            ->label('Dokumen Pendukung')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(2048)
                            ->directory('pengajuan-cuti')
                            ->columnSpanFull()
                            ->helperText('Upload dokumen pendukung (PDF/Gambar, max 2MB)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(PengajuanCuti::getStatusOptions())
                            ->default(PengajuanCuti::STATUS_MENUNGGU)
                            ->required()
                            ->native(false)
                            ->live()
                            ->disabled(fn($operation) => $operation === 'create'),

                        Forms\Components\Select::make('disetujui_oleh')
                            ->label('Disetujui Oleh')
                            ->relationship('disetujuiOleh', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn(callable $get) => $get('status') !== PengajuanCuti::STATUS_MENUNGGU),

                        Forms\Components\DateTimePicker::make('tanggal_persetujuan')
                            ->label('Tanggal Persetujuan')
                            ->native(false)
                            ->visible(fn(callable $get) => $get('status') !== PengajuanCuti::STATUS_MENUNGGU),

                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->rows(3)
                            ->visible(fn(callable $get) => $get('status') === PengajuanCuti::STATUS_DITOLAK)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($operation) => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('karyawan.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('jenis')
                    ->label('Jenis Cuti')
                    ->formatStateUsing(fn(string $state): string => PengajuanCuti::getJenisOptions()[$state] ?? $state)
                    ->colors([
                        'primary' => PengajuanCuti::JENIS_TAHUNAN,
                        'warning' => PengajuanCuti::JENIS_SAKIT,
                        'danger' => PengajuanCuti::JENIS_DARURAT,
                        'secondary' => PengajuanCuti::JENIS_LAINNYA,
                    ]),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_hari')
                    ->label('Jumlah Hari')
                    ->numeric()
                    ->suffix(' hari')
                    ->alignCenter(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => PengajuanCuti::getStatusOptions()[$state] ?? $state)
                    ->colors([
                        'warning' => PengajuanCuti::STATUS_MENUNGGU,
                        'success' => PengajuanCuti::STATUS_DISETUJUI,
                        'danger' => PengajuanCuti::STATUS_DITOLAK,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tanggal_persetujuan')
                    ->label('Tanggal Diproses')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(PengajuanCuti::getStatusOptions()),

                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis Cuti')
                    ->options(PengajuanCuti::getJenisOptions()),

                Tables\Filters\SelectFilter::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama_lengkap')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('tanggal_mulai')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                TableAction::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(PengajuanCuti $record): bool => $record->status === PengajuanCuti::STATUS_MENUNGGU)
                    ->action(function (PengajuanCuti $record): void {
                        $record->approve(auth()->id());

                        Notification::make()
                            ->title('Pengajuan cuti berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                TableAction::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(PengajuanCuti $record): bool => $record->status === PengajuanCuti::STATUS_MENUNGGU)
                    ->form([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PengajuanCuti $record, array $data): void {
                        $record->reject($data['alasan_penolakan'], auth()->id());

                        Notification::make()
                            ->title('Pengajuan cuti berhasil ditolak')
                            ->success()
                            ->send();
                    }),

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

                    Tables\Actions\BulkAction::make('approve_bulk')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === PengajuanCuti::STATUS_MENUNGGU) {
                                    $record->approve(auth()->id());
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} pengajuan cuti berhasil disetujui")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
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
                        Infolists\Components\TextEntry::make('karyawan.departemen')
                            ->label('Departemen'),
                        Infolists\Components\TextEntry::make('karyawan.jabatan')
                            ->label('Jabatan'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Cuti')
                    ->schema([
                        Infolists\Components\TextEntry::make('jenis')
                            ->label('Jenis Cuti')
                            ->formatStateUsing(fn(string $state): string => PengajuanCuti::getJenisOptions()[$state] ?? $state)
                            ->badge(),
                        Infolists\Components\TextEntry::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->date('d F Y'),
                        Infolists\Components\TextEntry::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->date('d F Y'),
                        Infolists\Components\TextEntry::make('jumlah_hari')
                            ->label('Jumlah Hari')
                            ->suffix(' hari'),
                        Infolists\Components\TextEntry::make('alasan')
                            ->label('Alasan Cuti')
                            ->columnSpanFull(),
                        Infolists\Components\ImageEntry::make('dokumen')
                            ->label('Dokumen Pendukung')
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->dokumen),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn(string $state): string => PengajuanCuti::getStatusOptions()[$state] ?? $state)
                            ->badge()
                            ->color(fn(PengajuanCuti $record): string => $record->status_color),
                        Infolists\Components\TextEntry::make('disetujuiOleh.name')
                            ->label('Disetujui Oleh')
                            ->visible(fn($record) => $record->disetujui_oleh),
                        Infolists\Components\TextEntry::make('tanggal_persetujuan')
                            ->label('Tanggal Persetujuan')
                            ->dateTime('d F Y H:i')
                            ->visible(fn($record) => $record->tanggal_persetujuan),
                        Infolists\Components\TextEntry::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->visible(fn($record) => $record->status === PengajuanCuti::STATUS_DITOLAK)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListPengajuanCutis::route('/'),
            'create' => Pages\CreatePengajuanCuti::route('/create'),
            'view' => Pages\ViewPengajuanCuti::route('/{record}'),
            'edit' => Pages\EditPengajuanCuti::route('/{record}/edit'),
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
        return static::getModel()::where('status', PengajuanCuti::STATUS_MENUNGGU)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    protected static function calculateDays($startDate, $endDate, callable $set): void
    {
        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $days = $start->diffInDays($end) + 1;
            $set('jumlah_hari', $days);
        }
    }

}