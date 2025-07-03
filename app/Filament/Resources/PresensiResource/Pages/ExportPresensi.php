<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use App\Exports\PresensiAdvancedExport;
use App\Exports\PresensiExport;
use App\Models\Presensi;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExportPresensi extends Page
{
    protected static string $resource = PresensiResource::class;

    protected static string $view = 'filament.resources.presensi-resource.pages.export-presensi';

    protected static ?string $title = 'Export Rekap Presensi';

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'format' => 'xlsx',
            'include_photos' => false,
            'include_location' => true,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📅 Periode Export')
                    ->description('Tentukan rentang tanggal data yang akan diekspor')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->default(now()->startOfMonth())
                                    ->maxDate(now()),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Tanggal Akhir')
                                    ->required()
                                    ->default(now()->endOfMonth())
                                    ->maxDate(now())
                                    ->afterOrEqual('start_date'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('quick_period')
                                    ->label('Periode Cepat')
                                    ->placeholder('Pilih periode')
                                    ->options([
                                        'today' => 'Hari Ini',
                                        'yesterday' => 'Kemarin',
                                        'this_week' => 'Minggu Ini',
                                        'last_week' => 'Minggu Lalu',
                                        'this_month' => 'Bulan Ini',
                                        'last_month' => 'Bulan Lalu',
                                        'this_year' => 'Tahun Ini',
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        match ($state) {
                                            'today' => [
                                                $set('start_date', now()->toDateString()),
                                                $set('end_date', now()->toDateString()),
                                            ],
                                            'yesterday' => [
                                                $set('start_date', now()->subDay()->toDateString()),
                                                $set('end_date', now()->subDay()->toDateString()),
                                            ],
                                            'this_week' => [
                                                $set('start_date', now()->startOfWeek()->toDateString()),
                                                $set('end_date', now()->endOfWeek()->toDateString()),
                                            ],
                                            'last_week' => [
                                                $set('start_date', now()->subWeek()->startOfWeek()->toDateString()),
                                                $set('end_date', now()->subWeek()->endOfWeek()->toDateString()),
                                            ],
                                            'this_month' => [
                                                $set('start_date', now()->startOfMonth()->toDateString()),
                                                $set('end_date', now()->endOfMonth()->toDateString()),
                                            ],
                                            'last_month' => [
                                                $set('start_date', now()->subMonth()->startOfMonth()->toDateString()),
                                                $set('end_date', now()->subMonth()->endOfMonth()->toDateString()),
                                            ],
                                            'this_year' => [
                                                $set('start_date', now()->startOfYear()->toDateString()),
                                                $set('end_date', now()->endOfYear()->toDateString()),
                                            ],
                                            default => null,
                                        };
                                    }),
                            ]),
                    ]),

                Forms\Components\Section::make('🔍 Filter Data')
                    ->description('Filter data yang akan disertakan dalam export')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('karyawan_ids')
                                    ->label('Pilih Karyawan')
                                    ->relationship('karyawan', 'nama_lengkap')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Semua Karyawan')
                                    ->helperText('Kosongkan untuk export semua karyawan'),

                                Forms\Components\Select::make('status_filter')
                                    ->label('Status Presensi')
                                    ->options(Presensi::getStatusOptions())
                                    ->multiple()
                                    ->placeholder('Semua Status')
                                    ->helperText('Kosongkan untuk export semua status'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('departemen_filter')
                                    ->label('Departemen')
                                    ->options(function () {
                                        return Karyawan::whereNotNull('departemen')
                                            ->distinct()
                                            ->pluck('departemen', 'departemen')
                                            ->toArray();
                                    })
                                    ->multiple()
                                    ->placeholder('Semua Departemen'),

                                Forms\Components\Select::make('jabatan_filter')
                                    ->label('Jabatan')
                                    ->options(function () {
                                        return Karyawan::whereNotNull('jabatan')
                                            ->distinct()
                                            ->pluck('jabatan', 'jabatan')
                                            ->toArray();
                                    })
                                    ->multiple()
                                    ->placeholder('Semua Jabatan'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('⚙️ Pengaturan Export')
                    ->description('Kustomisasi format dan konten file export')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('format')
                                    ->label('Format File')
                                    ->options([
                                        'xlsx' => 'Excel (.xlsx)',
                                        'csv' => 'CSV (.csv)',
                                        'pdf' => 'PDF (.pdf) - Coming Soon',
                                    ])
                                    ->default('xlsx')
                                    ->required(),

                                Forms\Components\Toggle::make('include_location')
                                    ->label('Sertakan Data Lokasi GPS')
                                    ->default(true)
                                    ->helperText('Koordinat latitude & longitude'),

                                Forms\Components\Toggle::make('include_photos')
                                    ->label('Sertakan Link Foto')
                                    ->default(false)
                                    ->helperText('URL foto check-in/out'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('group_by_employee')
                                    ->label('Kelompokkan Per Karyawan')
                                    ->default(false)
                                    ->helperText('Buat sheet terpisah untuk setiap karyawan'),

                                Forms\Components\Toggle::make('include_summary')
                                    ->label('Sertakan Ringkasan')
                                    ->default(true)
                                    ->helperText('Statistik kehadiran di sheet terpisah'),
                            ]),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Sekarang')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->size('lg')
                ->action('export'),

            Actions\Action::make('preview')
                ->label('Preview Data')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->action('preview'),
        ];
    }



    public function preview()
    {
        $data = $this->form->getState();

        // Hitung jumlah data yang akan diekspor
        $query = Presensi::query()->with(['karyawan']);

        if (!empty($data['start_date'])) {
            $query->whereDate('tanggal', '>=', $data['start_date']);
        }

        if (!empty($data['end_date'])) {
            $query->whereDate('tanggal', '<=', $data['end_date']);
        }

        if (!empty($data['karyawan_ids'])) {
            $query->whereIn('karyawan_id', $data['karyawan_ids']);
        }

        if (!empty($data['status_filter'])) {
            $query->whereIn('status', $data['status_filter']);
        }

        $totalRecords = $query->count();
        $dateRange = Carbon::parse($data['start_date'])->format('d M Y') . ' - ' .
            Carbon::parse($data['end_date'])->format('d M Y');

        Notification::make()
            ->title('Preview Data Export')
            ->body("Periode: {$dateRange}\nTotal record: {$totalRecords} data presensi")
            ->info()
            ->persistent()
            ->send();
    }
}
