<?php

namespace App\Filament\Resources\StokMobilResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\StokMobilResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\StokMobilResource\Api\Transformers\StokMobilTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = StokMobilResource::class;


    /**
     * Show StokMobil
     *
     * @param Request $request
     * @return StokMobilTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new StokMobilTransformer($query);
    }
}
