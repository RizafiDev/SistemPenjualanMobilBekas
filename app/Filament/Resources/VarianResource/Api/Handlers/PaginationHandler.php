<?php
namespace App\Filament\Resources\VarianResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\VarianResource;
use App\Filament\Resources\VarianResource\Api\Transformers\VarianTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = VarianResource::class;
    public static bool $public = true;

    /**
     * List of Varian
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
                'nama',
                'kode',
                'harga_otr',
                'transmisi',
                'jenis_bahan_bakar',
                'created_at',
                'updated_at',
                '-id',
                '-mobil_id',
                '-nama',
                '-kode',
                '-harga_otr',
                '-transmisi',
                '-jenis_bahan_bakar',
                '-created_at',
                '-updated_at'
            ])
            ->allowedFilters($this->getAllowedFilters() ?? [])
            ->allowedIncludes($this->getAllowedIncludes() ?? [])
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return VarianTransformer::collection($query);
    }
}
