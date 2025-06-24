<?php
namespace App\Filament\Resources\JanjiTemuResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\JanjiTemuResource;
use App\Filament\Resources\JanjiTemuResource\Api\Transformers\JanjiTemuTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = JanjiTemuResource::class;
    public static bool $public = true;

    /**
     * List of JanjiTemu
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
            ->allowedFields($this->getAllowedFields() ?? [])
            ->allowedSorts([
                'id',
                'nama_pelanggan',
                'email_pelanggan',
                'stok_mobil_id',
                'karyawan_id',
                'waktu_mulai',
                'waktu_selesai',
                'jenis',
                'status',
                'lokasi',
                'metode',
                'tanggal_request',
                'tanggal_konfirmasi',
                'created_at',
                'updated_at',
                '-id',
                '-nama_pelanggan',
                '-waktu_mulai',
                '-waktu_selesai',
                '-tanggal_request',
                '-tanggal_konfirmasi',
                '-created_at',
                '-updated_at'
            ])
            ->allowedFilters($this->getAllowedFilters() ?? [])
            ->allowedIncludes($this->getAllowedIncludes() ?? [])
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return JanjiTemuTransformer::collection($query);
    }
}
