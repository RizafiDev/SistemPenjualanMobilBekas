<?php
namespace App\Filament\Resources\MerekResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\MerekResource;
use App\Filament\Resources\MerekResource\Api\Transformers\MerekTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = MerekResource::class;
    public static bool $public = true; // Public API, protected by middleware

    /**
     * List of Merek
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

        return MerekTransformer::collection($query);
    }
}



