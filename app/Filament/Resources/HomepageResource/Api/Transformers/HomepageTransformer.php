<?php
namespace App\Filament\Resources\HomepageResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Homepage;

/**
 * @property Homepage $resource
 */
class HomepageTransformer extends JsonResource
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
