<?php
namespace App\Filament\Resources\JanjiTemuResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\JanjiTemuResource;
use App\Filament\Resources\JanjiTemuResource\Api\Requests\CreateJanjiTemuRequest;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = JanjiTemuResource::class;
    public static bool $public = true; // Public API, protected by middleware

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Create JanjiTemu
     *
     * @param CreateJanjiTemuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateJanjiTemuRequest $request)
    {
        $model = new (static::getModel());

        $data = $request->all();

        // Set default values for auto-generated fields
        $data['status'] = 'pending';
        $data['tanggal_request'] = now();

        // If no specific stock is selected, this is a general consultation
        if (empty($data['stok_mobil_id'])) {
            $data['stok_mobil_id'] = null;
        }

        $model->fill($data);
        $model->save();

        return static::sendSuccessResponse($model, "Janji temu berhasil dibuat");
    }
}


