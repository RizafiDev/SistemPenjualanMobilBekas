<?php
namespace App\Filament\Resources\RiwayatServisResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\RiwayatServis;

/**
 * @property RiwayatServis $resource
 */
class RiwayatServisTransformer extends JsonResource
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
