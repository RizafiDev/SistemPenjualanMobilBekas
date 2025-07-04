<?php

namespace App\Filament\Resources\KategoriResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\KategoriResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\KategoriResource\Api\Transformers\KategoriTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = KategoriResource::class;


    /**
     * Show Kategori
     *
     * @param Request $request
     * @return KategoriTransformer
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

        return new KategoriTransformer($query);
    }
}



