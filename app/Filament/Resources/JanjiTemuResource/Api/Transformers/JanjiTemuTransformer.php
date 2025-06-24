<?php
namespace App\Filament\Resources\JanjiTemuResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\JanjiTemu;

/**
 * @property JanjiTemu $resource
 */
class JanjiTemuTransformer extends JsonResource
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
