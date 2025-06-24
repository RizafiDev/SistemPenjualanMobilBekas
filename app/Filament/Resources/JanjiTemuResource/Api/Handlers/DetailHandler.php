<?php

namespace App\Filament\Resources\JanjiTemuResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\JanjiTemuResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\JanjiTemuResource\Api\Transformers\JanjiTemuTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = JanjiTemuResource::class;


    /**
     * Show JanjiTemu
     *
     * @param Request $request
     * @return JanjiTemuTransformer
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

        return new JanjiTemuTransformer($query);
    }
}
