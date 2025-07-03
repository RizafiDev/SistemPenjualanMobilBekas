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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Placeholder;
use App\Models\PengajuanCuti;
use App\Models\Presensi;

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
                                Select::make('jabatan')
                                    ->label('Jabatan')
                                    ->required()
                                    ->options([
                                        'sales_executive' => 'Sales Executive',
                                        'sales_supervisor' => 'Sales Supervisor',
                                        'sales_manager' => 'Sales Manager',
                                        'service_advisor' => 'Service Advisor',
                                        'teknisi' => 'Teknisi',
                                        'kepala_teknisi' => 'Kepala Teknisi',
                                        'service_manager' => 'Service Manager',
                                        'spare_part_staff' => 'Staff Spare Part',
                                        'spare_part_supervisor' => 'Supervisor Spare Part',
                                        'finance_staff' => 'Staff Finance',
                                        'finance_manager' => 'Finance Manager',
                                        'admin' => 'Admin',
                                        'receptionist' => 'Receptionist',
                                        'security' => 'Security',
                                        'driver' => 'Driver',
                                        'cleaning_service' => 'Cleaning Service',
                                        'general_manager' => 'General Manager',
                                        'deputy_manager' => 'Deputy Manager',
                                        'marketing_staff' => 'Marketing Staff',
                                        'marketing_manager' => 'Marketing Manager',
                                        'body_repair_staff' => 'Staff Body Repair',
                                        'quality_control' => 'Quality Control',
                                        'inventory_staff' => 'Staff Inventory',
                                        'customer_service' => 'Customer Service',
                                        'cashier' => 'Kasir',
                                    ])
                                    ->searchable(),

                                Select::make('departemen')
                                    ->label('Departemen')
                                    ->required()
                                    ->options([
                                        'sales' => 'Sales',
                                        'service' => 'Service',
                                        'spare_part' => 'Spare Part',
                                        'finance' => 'Finance',
                                        'admin' => 'Admin',
                                        'marketing' => 'Marketing',
                                        'body_repair' => 'Body Repair',
                                        'general_affairs' => 'General Affairs',
                                        'security' => 'Security',
                                        'management' => 'Management',
                                        'customer_service' => 'Customer Service',
                                        'inventory' => 'Inventory',
                                        'quality_assurance' => 'Quality Assurance',
                                        'it_support' => 'IT Support',
                                        'human_resources' => 'Human Resources',
                                    ])
                                    ->searchable(),

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('👤 Informasi Personal')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\ImageEntry::make('foto')
                                    ->label('Foto')
                                    ->circular()
                                    ->size(150)
                                    ->defaultImageUrl(url('/images/default-avatar.png')),

                                Components\Grid::make(2)
                                    ->schema([
                                        Components\TextEntry::make('nip')
                                            ->label('NIP')
                                            ->copyable()
                                            ->badge()
                                            ->color('primary'),

                                        Components\TextEntry::make('nik')
                                            ->label('NIK')
                                            ->copyable()
                                            ->badge()
                                            ->color('gray'),

                                        Components\TextEntry::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->weight('bold')
                                            ->size('lg'),

                                        Components\TextEntry::make('email')
                                            ->label('Email')
                                            ->copyable()
                                            ->icon('heroicon-m-envelope'),

                                        Components\TextEntry::make('no_telepon')
                                            ->label('No. Telepon')
                                            ->copyable()
                                            ->icon('heroicon-m-phone'),

                                        Components\TextEntry::make('jenis_kelamin')
                                            ->label('Jenis Kelamin')
                                            ->badge()
                                            ->color(fn($state) => $state === 'L' ? 'blue' : 'pink')
                                            ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                                    ])
                                    ->columnSpan(2),
                            ]),

                        Components\TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->icon('heroicon-m-cake'),

                        Components\TextEntry::make('alamat')
                            ->label('Alamat')
                            ->icon('heroicon-m-map-pin')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('💼 Informasi Pekerjaan')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('jabatan')
                                    ->label('Jabatan')
                                    ->badge()
                                    ->color('success'),

                                Components\TextEntry::make('departemen')
                                    ->label('Departemen')
                                    ->badge()
                                    ->color('info'),

                                Components\TextEntry::make('status')
                                    ->label('Status Karyawan')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'tetap' => 'success',
                                        'kontrak' => 'warning',
                                        'magang' => 'info',
                                        default => 'gray'
                                    }),

                                Components\TextEntry::make('aktif')
                                    ->label('Status Aktif')
                                    ->badge()
                                    ->color(fn($state) => $state === 'aktif' ? 'success' : 'danger')
                                    ->formatStateUsing(fn($state) => $state === 'aktif' ? 'Aktif' : 'Non-Aktif'),

                                Components\TextEntry::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->money('IDR')
                                    ->color('warning'),

                                Components\TextEntry::make('tanggal_masuk')
                                    ->label('Tanggal Masuk')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-calendar'),
                            ]),
                    ]),

                Components\Section::make('📊 Ringkasan Absensi')
                    ->description('Statistik absensi karyawan dalam periode tertentu')
                    ->schema([
                        Placeholder::make('ringkasan_absensi')
                            ->label('')
                            ->content(function ($record) {
                                $bulanIni = \Carbon\Carbon::now();
                                $startOfMonth = $bulanIni->copy()->startOfMonth();
                                $today = \Carbon\Carbon::today();
                                $endOfMonth = $bulanIni->copy()->endOfMonth();
                                $endDate = $today->lt($endOfMonth) ? $today : $endOfMonth;

                                // Ambil semua data presensi untuk bulan ini
                                $presensiRecords = Presensi::where('karyawan_id', $record->id)
                                    ->whereBetween('tanggal', [$startOfMonth, $endDate])
                                    ->get();

                                $stats = [
                                    'hadir' => 0,
                                    'terlambat' => 0,
                                    'tidak_hadir' => 0,
                                    'sakit' => 0,
                                    'izin' => 0,
                                    'cuti' => 0,
                                    'libur' => 0,
                                ];

                                // Hitung dari record yang ada
                                foreach ($presensiRecords as $p) {
                                    if (array_key_exists($p->status, $stats)) {
                                        $stats[$p->status]++;
                                    }
                                }

                                // Hitung hari kerja efektif dan tidak hadir yang sebenarnya
                                $currentDate = $startOfMonth->copy();
                                $presensiByDate = $presensiRecords->keyBy(function ($item) {
                                    return $item->tanggal->format('Y-m-d');
                                });

                                $totalHariKerjaEfektif = 0;
                                $tidakHadirSebenarnya = 0;

                                while ($currentDate->lte($endDate)) {
                                    if (!$currentDate->isWeekend()) { // Hari kerja (Senin-Jumat)
                                        $totalHariKerjaEfektif++;

                                        $dateStr = $currentDate->format('Y-m-d');
                                        if (!$presensiByDate->has($dateStr)) {
                                            // Tidak ada record presensi untuk hari kerja ini = tidak hadir
                                            $tidakHadirSebenarnya++;
                                        }
                                    }
                                    $currentDate->addDay();
                                }

                                // Gabungkan hadir dan terlambat untuk total kehadiran
                                $totalHadir = $stats['hadir'] + $stats['terlambat'];

                                $cuti = PengajuanCuti::where('karyawan_id', $record->id)
                                    ->where('status', PengajuanCuti::STATUS_DISETUJUI)
                                    ->where(function ($query) use ($startOfMonth, $endDate) {
                                        $query->whereBetween('tanggal_mulai', [$startOfMonth, $endDate])
                                            ->orWhere(function ($q) use ($startOfMonth, $endDate) {
                                                $q->where('tanggal_mulai', '<=', $startOfMonth)
                                                    ->where('tanggal_selesai', '>=', $startOfMonth);
                                            });
                                    })
                                    ->sum('jumlah_hari');

                                return new \Illuminate\Support\HtmlString("
                                    <div class='grid grid-cols-5 gap-4'>
                                        <div class='text-center p-3 bg-green-50 rounded-lg'>
                                            <div class='text-2xl font-bold text-green-600'>{$totalHadir}</div>
                                            <div class='text-sm text-green-500'>Hadir Bulan Ini</div>
                                        </div>
                                        <div class='text-center p-3 bg-yellow-50 rounded-lg'>
                                            <div class='text-2xl font-bold text-yellow-600'>{$stats['sakit']}</div>
                                            <div class='text-sm text-yellow-500'>Sakit Bulan Ini</div>
                                        </div>
                                        <div class='text-center p-3 bg-gray-50 rounded-lg'>
                                            <div class='text-2xl font-bold text-gray-600'>{$stats['izin']}</div>
                                            <div class='text-sm text-gray-500'>Izin Bulan Ini</div>
                                        </div>
                                        <div class='text-center p-3 bg-blue-50 rounded-lg'>
                                            <div class='text-2xl font-bold text-blue-600'>{$cuti}</div>
                                            <div class='text-sm text-blue-500'>Hari Cuti Bulan Ini</div>
                                        </div>
                                        <div class='text-center p-3 bg-red-50 rounded-lg'>
                                            <div class='text-2xl font-bold text-red-600'>{$tidakHadirSebenarnya}</div>
                                            <div class='text-sm text-red-500'>Tidak Hadir Bulan Ini</div>
                                        </div>
                                    </div>
                                ");
                            }),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}