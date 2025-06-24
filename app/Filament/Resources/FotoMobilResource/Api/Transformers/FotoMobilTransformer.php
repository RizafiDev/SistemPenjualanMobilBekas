<?php
namespace App\Filament\Resources\FotoMobilResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FotoMobil;

/**
 * @property FotoMobil $resource
 */
class FotoMobilTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
