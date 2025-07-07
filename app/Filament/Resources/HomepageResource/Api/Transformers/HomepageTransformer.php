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
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');

        // Transform foto_homepage to include full URLs
        $fotoHomepageUrls = [];
        if (!empty($this->foto_homepage)) {
            $fotoHomepage = is_array($this->foto_homepage) ? $this->foto_homepage : json_decode($this->foto_homepage, true);
            if (is_array($fotoHomepage)) {
                foreach ($fotoHomepage as $foto) {
                    if ($foto) {
                        // If already a full URL, use as is
                        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
                            $fotoHomepageUrls[] = $foto;
                        } else {
                            // Add storage URL prefix
                            $fotoHomepageUrls[] = $baseUrl . '/storage/' . ltrim($foto, '/');
                        }
                    }
                }
            }
        }

        return [
            'id' => $this->id,
            'pelanggan_puas' => $this->pelanggan_puas,
            'rating_puas' => $this->rating_puas,
            'foto_homepage' => $fotoHomepageUrls,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
