<?php
namespace App\Filament\Resources\RiwayatServisResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\RiwayatServisResource;
use App\Filament\Resources\RiwayatServisResource\Api\Requests\CreateRiwayatServisRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = RiwayatServisResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create RiwayatServis
     *
     * @param CreateRiwayatServisRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateRiwayatServisRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}


