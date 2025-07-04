<?php

namespace App\Filament\Resources\StokMobilResource\Api\Handlers;

use App\Filament\Resources\StokMobilResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\StokMobilResource\Api\Transformers\StokMobilTransformer;

class DetailHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = StokMobilResource::class;
    public static bool $public = true; // Public API, protected by middleware

    /**
     * Show StokMobil
     *
     * @param Request $request
     * @return StokMobilTransformer|\Illuminate\Http\JsonResponse
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
                // ✅ Removed status filter - allow all stock items to be viewable
                ->with([
                    'mobil.merek',
                    'mobil.kategori',
                    'mobil.fotoMobils', // ✅ Add mobil photos as fallback
                    'varian.mobil',     // ✅ Include full varian data with mobil
                    'riwayatServis'     // ✅ Include service history
                ])
        )
            ->first();

        if (!$query)
            return static::sendNotFoundResponse();

        return new StokMobilTransformer($query);
    }
}



