<?php

namespace App\Filament\Resources\StokMobilResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatServisRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatServis';

    protected static ?string $title = 'Riwayat Servis';

    protected static ?string $recordTitleAttribute = 'jenis_servis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Servis')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_servis')
                            ->required()
                            ->maxDate(now()),

                        Forms\Components\TextInput::make('jenis_servis')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('tempat_servis')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('biaya')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999999999),

                        Forms\Components\TextInput::make('kilometer_servis')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->label('Kilometer'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_bukti')
                            ->multiple()
                            ->image()
                            ->directory('servis/bukti')
                            ->maxSize(2048)
                            ->maxFiles(5)
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('sparepart')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\TextInput::make('kode')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_servis')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_servis')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_servis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tempat_servis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kilometer_servis')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('foto_bukti')
                    ->circular()
                    ->stacked()
                    ->limit(3),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Riwayat Servis')
                    ->modalWidth('5xl'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalWidth('5xl'),
                Tables\Actions\EditAction::make()
                    ->modalWidth('5xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_servis', 'desc')
            ->emptyStateHeading('Belum ada riwayat servis')
            ->emptyStateDescription('Mulai dengan menambahkan riwayat servis untuk mobil ini.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }
}