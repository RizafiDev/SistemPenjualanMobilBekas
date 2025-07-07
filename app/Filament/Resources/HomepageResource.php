<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageResource\Pages;
use App\Filament\Resources\HomepageResource\RelationManagers;
use App\Models\Homepage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomepageResource extends Resource
{
    protected static ?string $model = Homepage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Content Management';

    // Tambahkan method untuk disable create button
    public static function canCreate(): bool
    {
        return Homepage::count() === 0;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pelanggan_puas')
                    ->label('Pelanggan Puas')
                    ->required()
                    ->maxLength(255)
                ,

                Forms\Components\TextInput::make('rating_puas')
                    ->label('Rating Puas')
                    ->required()
                    ->maxLength(255)
                    ->prefix('%'),

                Forms\Components\FileUpload::make('foto_homepage')
                    ->label('Foto Homepage')
                    ->multiple()
                    ->image()
                    ->directory('homepage')
                    ->preserveFilenames()
                    ->enableReordering()
                    ->enableDownload()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan_puas'),
                Tables\Columns\TextColumn::make('rating_puas'),
                Tables\Columns\ImageColumn::make('foto_homepage'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListHomepages::route('/'),
            'create' => Pages\CreateHomepage::route('/create'),
            'edit' => Pages\EditHomepage::route('/{record}/edit'),
        ];
    }
}
