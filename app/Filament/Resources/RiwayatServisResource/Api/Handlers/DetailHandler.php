<?php

namespace App\Filament\Resources\RiwayatServisResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\RiwayatServisResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\RiwayatServisResource\Api\Transformers\RiwayatServisTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = RiwayatServisResource::class;


    /**
     * Show RiwayatServis
     *
     * @param Request $request
     * @return RiwayatServisTransformer
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

        return new RiwayatServisTransformer($query);
    }
}
