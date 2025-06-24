<?php
namespace App\Filament\Resources\StokMobilResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\StokMobil;

/**
 * @property StokMobil $resource
 */
class StokMobilTransformer extends JsonResource
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
