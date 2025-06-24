<?php
namespace App\Filament\Resources\MobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\MobilResource;
use App\Filament\Resources\MobilResource\Api\Transformers\MobilTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = MobilResource::class;
    public static bool $public = true;

    /**
     * List of Mobil
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
                'nama',
                'created_at',
                'updated_at',
                'tahun_mulai',
                'tahun_akhir',
                '-id',           // newest first (id desc)
                '-nama',         // name desc
                '-created_at',   // newest first (created_at desc)  
                '-updated_at',   // recently updated first
                '-tahun_mulai',  // newest year first
                '-tahun_akhir'   // newest year first
            ])
            ->allowedFilters($this->getAllowedFilters() ?? [])
            ->allowedIncludes($this->getAllowedIncludes() ?? [])
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return MobilTransformer::collection($query);
    }
}
