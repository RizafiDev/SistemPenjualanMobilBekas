<?php
namespace App\Filament\Resources\VarianResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Varian;

/**
 * @property Varian $resource
 */
class VarianTransformer extends JsonResource
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
