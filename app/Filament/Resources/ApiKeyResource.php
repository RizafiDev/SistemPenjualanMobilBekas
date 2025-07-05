<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiKeyResource\Pages;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?string $pluralLabel = 'API Keys';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Key Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name/Description')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mobile App API Key'),

                        Forms\Components\TagsInput::make('permissions')
                            ->label('Permissions')
                            ->placeholder('Leave empty for full access')
                            ->helperText('Specify resource names (e.g., mobil, varian, kategori) or use * for all'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->nullable()
                            ->helperText('Leave empty for permanent key'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive keys cannot be used'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Key'),
                Tables\Columns\BadgeColumn::make('is_active')
                    ->label('Status')
                    ->getStateUsing(function (ApiKey $record) {
                        if (!$record->is_active)
                            return 'Inactive';
                        if ($record->isExpired())
                            return 'Expired';
                        return 'Active';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'warning',
                        'Expired' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('permissions')
                    ->label('Permissions')
                    ->badge()
                    ->getStateUsing(function (ApiKey $record) {
                        if (empty($record->permissions)) {
                            return ['Full Access'];
                        }
                        return $record->permissions;
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),

                Tables\Filters\TernaryFilter::make('expires_at')
                    ->label('Expiration')
                    ->placeholder('All keys')
                    ->trueLabel('Expired keys')
                    ->falseLabel('Active keys')
                    ->queries(
                        true: fn(Builder $query) => $query->where('expires_at', '<', now()),
                        false: fn(Builder $query) => $query->where(function ($query) {
                            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('revoke')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ApiKey $record) {
                        $record->update(['is_active' => false]);
                    })
                    ->visible(fn(ApiKey $record) => $record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('API Key Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('is_active')
                            ->label('Status')
                            ->badge()
                            ->getStateUsing(function (ApiKey $record) {
                                if (!$record->is_active)
                                    return 'Inactive';
                                if ($record->isExpired())
                                    return 'Expired';
                                return 'Active';
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'Active' => 'success',
                                'Inactive' => 'warning',
                                'Expired' => 'danger',
                            }),
                        TextEntry::make('permissions')
                            ->label('Permissions')
                            ->badge()
                            ->getStateUsing(function (ApiKey $record) {
                                if (empty($record->permissions)) {
                                    return ['Full Access'];
                                }
                                return $record->permissions;
                            }),
                    ])
                    ->columns(3),

                Section::make('Usage Statistics')
                    ->schema([
                        TextEntry::make('last_used_at')
                            ->label('Last Used')
                            ->dateTime()
                            ->placeholder('Never'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('expires_at')
                            ->label('Expires')
                            ->dateTime()
                            ->placeholder('Never'),
                    ])
                    ->columns(3),
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
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'view' => Pages\ViewApiKey::route('/{record}'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
