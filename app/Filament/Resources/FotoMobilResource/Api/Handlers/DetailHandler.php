<?php

namespace App\Filament\Resources\FotoMobilResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\FotoMobilResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\FotoMobilResource\Api\Transformers\FotoMobilTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = FotoMobilResource::class;


    /**
     * Show FotoMobil
     *
     * @param Request $request
     * @return FotoMobilTransformer
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

        return new FotoMobilTransformer($query);
    }
}



