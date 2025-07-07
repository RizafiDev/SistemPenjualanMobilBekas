<?php

namespace App\Filament\Resources\HomepageResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\HomepageResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\HomepageResource\Api\Transformers\HomepageTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = HomepageResource::class;


    /**
     * Show Homepage
     *
     * @param Request $request
     * @return HomepageTransformer
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

        return new HomepageTransformer($query);
    }
}
