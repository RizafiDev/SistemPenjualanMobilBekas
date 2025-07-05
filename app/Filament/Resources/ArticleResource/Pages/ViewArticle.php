<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;

class ViewArticle extends ViewRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Article Details')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'draft' => 'gray',
                                'published' => 'success',
                                'archived' => 'warning',
                            }),

                        TextEntry::make('published_at')
                            ->label('Published Date')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Featured Image')
                    ->schema([
                        ImageEntry::make('featured_image')
                            ->label('')
                            ->disk('public')
                            ->size(400)
                            ->hiddenLabel(),
                    ])
                    ->visible(fn($record) => $record->featured_image),

                Section::make('Content')
                    ->schema([
                        TextEntry::make('excerpt')
                            ->label('Excerpt')
                            ->placeholder('No excerpt provided'),

                        TextEntry::make('content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO Information')
                    ->schema([
                        TextEntry::make('meta_title')
                            ->label('Meta Title')
                            ->placeholder('No meta title set'),

                        TextEntry::make('meta_description')
                            ->label('Meta Description')
                            ->placeholder('No meta description set'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime(),

                        TextEntry::make('deleted_at')
                            ->label('Deleted')
                            ->dateTime()
                            ->visible(fn($record) => $record->deleted_at),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
