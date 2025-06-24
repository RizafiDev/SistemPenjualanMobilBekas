<?php
namespace App\Filament\Resources\MobilResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Mobil;

/**
 * @property Mobil $resource
 */
class MobilTransformer extends JsonResource
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
