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
                // Header Section - Ringkasan Cuti
                Infolists\Components\Section::make('Ringkasan Pengajuan Cuti')
                    ->schema([
                        Infolists\Components\Grid::make(12)
                            ->schema([
                                // Status utama
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('status')
                                        ->label('Status Pengajuan')
                                        ->formatStateUsing(fn(string $state): string => PengajuanCuti::getStatusOptions()[$state] ?? $state)
                                        ->badge()
                                        ->size('xl')
                                        ->color(fn(PengajuanCuti $record): string => $record->status_color),

                                    Infolists\Components\TextEntry::make('jenis')
                                        ->label('Jenis Cuti')
                                        ->formatStateUsing(fn(string $state): string => PengajuanCuti::getJenisOptions()[$state] ?? $state)
                                        ->badge()
                                        ->size('lg')
                                        ->color('info'),
                                ])
                                    ->columnSpan(4),

                                // Periode cuti
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('tanggal_mulai')
                                        ->label('Tanggal Mulai')
                                        ->date('d F Y')
                                        ->icon('heroicon-o-calendar')
                                        ->iconColor('success')
                                        ->weight('semibold'),

                                    Infolists\Components\TextEntry::make('tanggal_selesai')
                                        ->label('Tanggal Selesai')
                                        ->date('d F Y')
                                        ->icon('heroicon-o-calendar')
                                        ->iconColor('danger')
                                        ->weight('semibold'),

                                    Infolists\Components\TextEntry::make('jumlah_hari')
                                        ->label('Total Hari')
                                        ->suffix(' hari')
                                        ->icon('heroicon-o-clock')
                                        ->iconColor('warning')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->color('primary'),
                                ])
                                    ->columnSpan(4),

                                // Info tambahan
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('created_at')
                                        ->label('Tanggal Pengajuan')
                                        ->date('d F Y')
                                        ->icon('heroicon-o-document-plus')
                                        ->iconColor('info'),

                                    Infolists\Components\TextEntry::make('sisa_cuti')
                                        ->label('Sisa Cuti Tahunan')
                                        ->suffix(' hari')
                                        ->icon('heroicon-o-chart-bar')
                                        ->iconColor('primary')
                                        ->visible(fn(PengajuanCuti $record): bool => isset($record->sisa_cuti)),

                                    Infolists\Components\TextEntry::make('cuti_terpakai')
                                        ->label('Cuti Terpakai')
                                        ->suffix(' hari')
                                        ->icon('heroicon-o-minus-circle')
                                        ->iconColor('gray')
                                        ->visible(fn(PengajuanCuti $record): bool => isset($record->cuti_terpakai)),
                                ])
                                    ->columnSpan(4),
                            ]),
                    ])
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('primary'),

                // Informasi Karyawan
                Infolists\Components\Section::make('Informasi Pemohon')
                    ->schema([
                        Infolists\Components\Grid::make(12)
                            ->schema([
                                // Foto karyawan jika ada
                                Infolists\Components\ImageEntry::make('karyawan.foto')
                                    ->label('Foto')
                                    ->circular()
                                    ->height(120)
                                    ->width(120)
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->karyawan?->foto))
                                    ->columnSpan(2),

                                // Data pribadi
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('karyawan.nama_lengkap')
                                        ->label('Nama Lengkap')
                                        ->icon('heroicon-o-user')
                                        ->iconColor('primary')
                                        ->weight('bold')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('karyawan.nip')
                                        ->label('NIP')
                                        ->icon('heroicon-o-identification')
                                        ->iconColor('info')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('karyawan.email')
                                        ->label('Email')
                                        ->icon('heroicon-o-envelope')
                                        ->iconColor('warning')
                                        ->copyable()
                                        ->visible(fn(PengajuanCuti $record): bool => !empty($record->karyawan?->email)),
                                ])
                                    ->columnSpan(5),

                                // Data organisasi
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('karyawan.departemen')
                                        ->label('Departemen')
                                        ->icon('heroicon-o-building-office-2')
                                        ->iconColor('success')
                                        ->badge()
                                        ->color('success'),

                                    Infolists\Components\TextEntry::make('karyawan.jabatan')
                                        ->label('Jabatan')
                                        ->icon('heroicon-o-briefcase')
                                        ->iconColor('primary')
                                        ->badge()
                                        ->color('primary'),

                                    Infolists\Components\TextEntry::make('karyawan.atasan.nama_lengkap')
                                        ->label('Atasan Langsung')
                                        ->icon('heroicon-o-user-group')
                                        ->iconColor('info')
                                        ->visible(fn(PengajuanCuti $record): bool => !empty($record->karyawan?->atasan)),
                                ])
                                    ->columnSpan(5),
                            ]),
                    ])
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('info'),

                // Detail Pengajuan Cuti
                Infolists\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                // Alasan dan keterangan
                                Infolists\Components\TextEntry::make('alasan')
                                    ->label('Alasan Cuti')
                                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                    ->iconColor('primary')
                                    ->columnSpanFull()
                                    ->placeholder('Tidak ada alasan yang diberikan'),

                                Infolists\Components\TextEntry::make('alamat_selama_cuti')
                                    ->label('Alamat Selama Cuti')
                                    ->icon('heroicon-o-map-pin')
                                    ->iconColor('warning')
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->alamat_selama_cuti))
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('telepon_darurat')
                                    ->label('Telepon Darurat')
                                    ->icon('heroicon-o-phone')
                                    ->iconColor('danger')
                                    ->copyable()
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->telepon_darurat)),

                                Infolists\Components\TextEntry::make('pengganti_tugas')
                                    ->label('Pengganti Tugas')
                                    ->icon('heroicon-o-user-plus')
                                    ->iconColor('success')
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->pengganti_tugas)),
                            ]),

                        // Dokumen pendukung
                        Infolists\Components\Group::make([
                            Infolists\Components\ImageEntry::make('dokumen')
                                ->label('Dokumen Pendukung')
                                ->disk('public')
                                ->height(200)
                                ->width(300)
                                ->extraAttributes(['class' => 'rounded-lg shadow-md'])
                                ->visible(fn(PengajuanCuti $record): bool => !empty($record->dokumen)),
                        ])
                            ->columnSpanFull()
                            ->visible(fn(PengajuanCuti $record): bool => !empty($record->dokumen)),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->iconColor('warning'),

                // Status Persetujuan dengan Timeline
                Infolists\Components\Section::make('Status & Persetujuan')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                // Status current
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('status')
                                        ->label('Status Saat Ini')
                                        ->formatStateUsing(fn(string $state): string => PengajuanCuti::getStatusOptions()[$state] ?? $state)
                                        ->badge()
                                        ->size('xl')
                                        ->color(fn(PengajuanCuti $record): string => $record->status_color),

                                    Infolists\Components\TextEntry::make('progress_percentage')
                                        ->label('Progress')
                                        ->formatStateUsing(
                                            fn(PengajuanCuti $record): string =>
                                            match ($record->status) {
                                                PengajuanCuti::STATUS_MENUNGGU => '0%',
                                                PengajuanCuti::STATUS_DISETUJUI => '100%',
                                                PengajuanCuti::STATUS_DITOLAK => '0%',
                                                default => '0%'
                                            }
                                        )
                                        ->suffix(' selesai')
                                        ->icon('heroicon-o-chart-pie'),
                                ])
                                    ->columnSpan(1),

                                // Info persetujuan
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('disetujuiOleh.name')
                                        ->label('Disetujui/Ditolak Oleh')
                                        ->icon('heroicon-o-user')
                                        ->iconColor('success')
                                        ->visible(fn(PengajuanCuti $record): bool => !empty($record->disetujui_oleh)),

                                    Infolists\Components\TextEntry::make('tanggal_persetujuan')
                                        ->label('Tanggal Keputusan')
                                        ->dateTime('d F Y, H:i')
                                        ->icon('heroicon-o-calendar-days')
                                        ->iconColor('primary')
                                        ->visible(fn(PengajuanCuti $record): bool => !empty($record->tanggal_persetujuan)),

                                    Infolists\Components\TextEntry::make('level_persetujuan')
                                        ->label('Level Persetujuan')
                                        ->badge()
                                        ->color('info')
                                        ->visible(fn(PengajuanCuti $record): bool => !empty($record->level_persetujuan)),
                                ])
                                    ->columnSpan(1),

                                // Catatan atau alasan penolakan
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('catatan_persetujuan')
                                        ->label('Catatan Persetujuan')
                                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                        ->iconColor('info')
                                        ->visible(
                                            fn(PengajuanCuti $record): bool =>
                                            !empty($record->catatan_persetujuan) &&
                                            $record->status === PengajuanCuti::STATUS_DISETUJUI
                                        ),

                                    Infolists\Components\TextEntry::make('alasan_penolakan')
                                        ->label('Alasan Penolakan')
                                        ->icon('heroicon-o-x-circle')
                                        ->iconColor('danger')
                                        ->color('danger')
                                        ->visible(
                                            fn(PengajuanCuti $record): bool =>
                                            $record->status === PengajuanCuti::STATUS_DITOLAK &&
                                            !empty($record->alasan_penolakan)
                                        ),
                                ])
                                    ->columnSpan(1),
                            ]),

                        // Timeline persetujuan
                        Infolists\Components\TextEntry::make('timeline_persetujuan')
                            ->label('Timeline Persetujuan')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->visible(fn(PengajuanCuti $record): bool => !empty($record->timeline_persetujuan))
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success'),

                // Dampak Cuti & Informasi Tambahan
                Infolists\Components\Section::make('Dampak & Informasi Tambahan')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('hari_kerja_hilang')
                                    ->label('Hari Kerja Hilang')
                                    ->suffix(' hari')
                                    ->icon('heroicon-o-calendar-x')
                                    ->iconColor('danger')
                                    ->visible(fn(PengajuanCuti $record): bool => isset($record->hari_kerja_hilang)),

                                Infolists\Components\TextEntry::make('biaya_pengganti')
                                    ->label('Biaya Pengganti')
                                    ->formatStateUsing(fn(?string $state): string => $state ? 'Rp ' . number_format($state, 0, ',', '.') : 'Tidak ada')
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('warning')
                                    ->visible(fn(PengajuanCuti $record): bool => isset($record->biaya_pengganti)),

                                Infolists\Components\TextEntry::make('proyek_terdampak')
                                    ->label('Proyek Terdampak')
                                    ->badge()
                                    ->color('warning')
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->proyek_terdampak)),

                                Infolists\Components\TextEntry::make('tingkat_urgensi')
                                    ->label('Tingkat Urgensi')
                                    ->badge()
                                    ->color(
                                        fn(?string $state): string =>
                                        match ($state) {
                                            'tinggi' => 'danger',
                                            'sedang' => 'warning',
                                            'rendah' => 'success',
                                            default => 'gray'
                                        }
                                    )
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->tingkat_urgensi)),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->visible(
                        fn(PengajuanCuti $record): bool =>
                        isset($record->hari_kerja_hilang) ||
                        isset($record->biaya_pengganti) ||
                        !empty($record->proyek_terdampak) ||
                        !empty($record->tingkat_urgensi)
                    )
                    ->icon('heroicon-o-exclamation-triangle')
                    ->iconColor('warning'),

                // System Information
                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d F Y, H:i:s')
                                    ->icon('heroicon-o-plus-circle')
                                    ->iconColor('success'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Terakhir Diubah')
                                    ->dateTime('d F Y, H:i:s')
                                    ->icon('heroicon-o-pencil-square')
                                    ->iconColor('warning'),

                                Infolists\Components\TextEntry::make('nomor_referensi')
                                    ->label('No. Referensi')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->visible(fn(PengajuanCuti $record): bool => !empty($record->nomor_referensi)),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('gray'),


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