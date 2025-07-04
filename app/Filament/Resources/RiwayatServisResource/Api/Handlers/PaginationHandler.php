<?php
namespace App\Filament\Resources\RiwayatServisResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\RiwayatServisResource;
use App\Filament\Resources\RiwayatServisResource\Api\Transformers\RiwayatServisTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = RiwayatServisResource::class;
    public static bool $public = true; // Public API, protected by middleware

    /**
     * List of RiwayatServis
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
                'stok_mobil_id',
                'tanggal_servis',
                'jenis_servis',
                'tempat_servis',
                'biaya',
                'kilometer_servis',
                'created_at',
                'updated_at',
                '-id',
                '-stok_mobil_id',
                '-tanggal_servis',
                '-jenis_servis',
                '-biaya',
                '-kilometer_servis',
                '-created_at',
                '-updated_at'
            ])
            ->allowedFilters($this->getAllowedFilters() ?? [])
            ->allowedIncludes($this->getAllowedIncludes() ?? [])
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return RiwayatServisTransformer::collection($query);
    }
}



