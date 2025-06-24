<?php
namespace App\Filament\Resources\KategoriResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\KategoriResource;
use App\Filament\Resources\KategoriResource\Api\Requests\CreateKategoriRequest;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = KategoriResource::class;
    public static bool $public = true;
    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Create Kategori
     *
     * @param CreateKategoriRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateKategoriRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}