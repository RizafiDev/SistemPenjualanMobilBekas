<?php
namespace App\Filament\Resources\PresensiResource\Widgets;

use App\Filament\Resources\PresensiResource;
use App\Models\Presensi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Colors\Color;

class LatestPresensiWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(PresensiResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable()
                    ->color(fn($record) => $record->menit_terlambat > 0 ? Color::Red : Color::Green),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Pulang')
                    ->time('H:i')
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
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn(Presensi $record): string => PresensiResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ]);
    }

    protected function getTableHeading(): string
    {
        return 'Presensi Terbaru';
    }
}
