<?php
namespace App\Filament\Resources\StokMobilResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Filament\Resources\MobilResource\Api\Transformers\MobilTransformer;
use App\Filament\Resources\VarianResource\Api\Transformers\VarianTransformer;
use App\Filament\Resources\RiwayatServisResource\Api\Transformers\RiwayatServisTransformer;

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
        // Format foto kondisi URLs
        $fotoKondisiUrls = [];
        if (!empty($this->foto_kondisi)) {
            $fotoKondisi = is_array($this->foto_kondisi) ? $this->foto_kondisi : json_decode($this->foto_kondisi, true);
            if (is_array($fotoKondisi)) {
                foreach ($fotoKondisi as $foto) {
                    if (!empty($foto)) {
                        // Ensure proper storage URL format
                        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
                            $fotoKondisiUrls[] = $foto;
                        } elseif (str_starts_with($foto, 'storage/')) {
                            $fotoKondisiUrls[] = url($foto);
                        } else {
                            $fotoKondisiUrls[] = url('storage/' . $foto);
                        }
                    }
                }
            }
        }

        return [
            'id' => $this->id,
            'mobil_id' => $this->mobil_id,
            'varian_id' => $this->varian_id,
            'warna' => $this->warna,
            'no_rangka' => $this->no_rangka,
            'no_mesin' => $this->no_mesin,
            'no_polisi' => $this->no_polisi, // ✅ Added no_polisi field
            'tahun' => $this->tahun,
            'kilometer' => $this->kilometer,
            'kondisi' => $this->kondisi,
            'status' => $this->status,
            'harga_beli' => $this->harga_beli,
            'harga_jual' => $this->harga_jual,
            'tanggal_masuk' => $this->tanggal_masuk,
            'tanggal_keluar' => $this->tanggal_keluar,
            'lokasi' => $this->lokasi,
            'catatan' => $this->catatan,
            'kelengkapan' => $this->kelengkapan,
            'riwayat_perbaikan' => $this->riwayat_perbaikan,
            'dokumen' => $this->dokumen,
            'foto_kondisi' => $this->foto_kondisi,
            'foto_kondisi_urls' => $fotoKondisiUrls, // ✅ Formatted URLs for frontend
            'kondisi_fitur' => $this->kondisi_fitur,
            'aktif' => $this->aktif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Frontend display helpers
            'foto_url' => $fotoKondisiUrls[0] ?? null, // ✅ Primary photo for catalog
            'nama_lengkap' => $this->nama_lengkap,
            // Relations with full data
            'mobil' => new MobilTransformer($this->whenLoaded('mobil')),
            'varian' => new VarianTransformer($this->whenLoaded('varian')),
            'riwayat_servis' => RiwayatServisTransformer::collection($this->whenLoaded('riwayatServis')),
        ];
    }
}
