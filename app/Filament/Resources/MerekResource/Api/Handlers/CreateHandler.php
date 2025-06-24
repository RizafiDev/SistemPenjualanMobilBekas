<?php
namespace App\Filament\Resources\MerekResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\MerekResource;
use App\Filament\Resources\MerekResource\Api\Requests\CreateMerekRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = MerekResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Merek
     *
     * @param CreateMerekRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateMerekRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}