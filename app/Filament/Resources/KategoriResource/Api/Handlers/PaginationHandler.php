<?php
namespace App\Filament\Resources\KategoriResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\KategoriResource;
use App\Filament\Resources\KategoriResource\Api\Transformers\KategoriTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = KategoriResource::class;
    public static bool $public = true;

    /**
     * List of Kategori
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
            ->allowedFields($this->getAllowedFields() ?? [])
            ->allowedSorts([
                'id',
                'nama',  // ✅ Fixed - using correct column name
                'created_at',
                'updated_at',
                '-id',
                '-nama', // ✅ Fixed - using correct column name
                '-created_at',
                '-updated_at'
            ])
            ->allowedFilters($this->getAllowedFilters() ?? [])
            ->allowedIncludes($this->getAllowedIncludes() ?? [])
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return KategoriTransformer::collection($query);
    }
}
