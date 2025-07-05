<?php
namespace App\Filament\Resources\ArticleResource\Api\Handlers;

use App\Filament\Resources\ArticleResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ArticleResource\Api\Transformers\ArticleTransformer;

class DetailHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = ArticleResource::class;
    public static bool $public = true; // Public API, protected by middleware

    /**
     * Show Article
     *
     * @param Request $request
     * @return ArticleTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query)
            return static::sendNotFoundResponse();

        return new ArticleTransformer($query);
    }
}
