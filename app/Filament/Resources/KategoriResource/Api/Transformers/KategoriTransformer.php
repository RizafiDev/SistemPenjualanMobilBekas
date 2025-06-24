<?php
namespace App\Filament\Resources\KategoriResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Kategori;

/**
 * @property Kategori $resource
 */
class KategoriTransformer extends JsonResource
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
