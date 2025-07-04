<?php

namespace App\Filament\Resources\MerekResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\MerekResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\MerekResource\Api\Transformers\MerekTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = MerekResource::class;


    /**
     * Show Merek
     *
     * @param Request $request
     * @return MerekTransformer
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

        return new MerekTransformer($query);
    }
}



