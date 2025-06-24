<?php

namespace App\Filament\Resources\MobilResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\MobilResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\MobilResource\Api\Transformers\MobilTransformer;

class DetailHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = MobilResource::class;
    public static bool $public = true;


    /**
     * Show Mobil
     *
     * @param Request $request
     * @return MobilTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query)
            return static::sendNotFoundResponse();

        return new MobilTransformer($query);
    }
}
