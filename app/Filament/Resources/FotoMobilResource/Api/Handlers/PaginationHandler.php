<?php
namespace App\Filament\Resources\FotoMobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\FotoMobilResource;
use App\Filament\Resources\FotoMobilResource\Api\Transformers\FotoMobilTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = FotoMobilResource::class;
    public static bool $public = true; // Public API, protected by middleware



    /**
     * List of FotoMobil
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
                'mobil_id',
                'path_file',
                'jenis_media',
                'jenis_gambar',
                'urutan_tampil',
                'created_at',
                'updated_at',
                '-id',
                '-mobil_id',
                '-urutan_tampil',
                '-created_at',
                '-updated_at'
            ])
            ->allowedFilters($this->getAllowedFilters() ?? [])
            ->allowedIncludes($this->getAllowedIncludes() ?? [])
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return FotoMobilTransformer::collection($query);
    }
}



