<?php

namespace App\Filament\Resources\PenggajianResource\Widgets;

use App\Models\Penggajian;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class TopEarnersWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 Earners Bulan Ini';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        $currentMonth = now()->format('Y-m');

        return $table
            ->query(
                Penggajian::query()
                    ->with(['karyawan'])
                    ->where('periode', $currentMonth)
                    ->where('status', Penggajian::STATUS_DIBAYAR)
                    ->orderBy('gaji_bersih', 'desc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex()
                    ->alignCenter(),

                TextColumn::make('karyawan.nama_lengkap')
                    ->label('Karyawan')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('karyawan.nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->money('IDR'),

                TextColumn::make('total_gaji')
                    ->label('Total Gaji')
                    ->money('IDR'),

                TextColumn::make('total_potongan')
                    ->label('Potongan')
                    ->money('IDR')
                    ->color('danger'),

                TextColumn::make('gaji_bersih')
                    ->label('Gaji Bersih')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('success'),
            ])
            ->paginated(false)
            ->defaultSort('gaji_bersih', 'desc');
    }
}