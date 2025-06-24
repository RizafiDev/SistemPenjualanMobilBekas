<?php
namespace App\Filament\Resources\MerekResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Merek;

/**
 * @property Merek $resource
 */
class MerekTransformer extends JsonResource
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
