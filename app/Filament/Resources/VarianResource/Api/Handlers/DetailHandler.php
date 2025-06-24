<?php

namespace App\Filament\Resources\VarianResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\VarianResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\VarianResource\Api\Transformers\VarianTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = VarianResource::class;


    /**
     * Show Varian
     *
     * @param Request $request
     * @return VarianTransformer
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

        return new VarianTransformer($query);
    }
}
