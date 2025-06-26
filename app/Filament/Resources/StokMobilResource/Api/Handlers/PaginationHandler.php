<?php
namespace App\Filament\Resources\StokMobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\StokMobilResource;
use App\Filament\Resources\StokMobilResource\Api\Transformers\StokMobilTransformer;

class PaginationHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = StokMobilResource::class;
    public static bool $public = true;

    /**
     * List of StokMobil
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        // ✅ For catalog, still filter only available items
        $query = $query->where('status', 'tersedia');

        // ✅ Load all necessary relationships including varian and service history
        $query = $query->with([
            'mobil.merek',
            'mobil.kategori',
            'mobil.fotoMobils',
            'varian.mobil',  // ✅ Include varian relation with mobil
            'riwayatServis'   // ✅ Include service history
        ]);

        $query = QueryBuilder::for($query)
            ->allowedFields($this->getAllowedFields() ?? [])
            ->allowedSorts([
                'id',
                'mobil_id',
                'varian_id',
                'warna',
                'tahun',
                'kilometer',
                'kondisi',
                'harga_beli',
                'harga_jual',
                'tanggal_masuk',
                'created_at',
                'updated_at',
                '-id',
                '-mobil_id',
                '-varian_id',
                '-tahun',
                '-kilometer',
                '-harga_beli',
                '-harga_jual',
                '-tanggal_masuk',
                '-created_at',
                '-updated_at'
            ])
            ->allowedFilters([
                'mobil_id',
                'varian_id',
                'warna',
                'tahun',
                'kilometer',
                'kondisi',
                'harga_beli',
                'harga_jual',
                'tanggal_masuk'
            ])
            ->allowedIncludes($this->getAllowedIncludes() ?? []);

        // Handle custom filtering
        if (request()->has('merek_id')) {
            $query->whereHas('mobil', function ($q) {
                $q->where('merek_id', request('merek_id'));
            });
        }

        if (request()->has('kategori_id')) {
            $query->whereHas('mobil', function ($q) {
                $q->where('kategori_id', request('kategori_id'));
            });
        }

        if (request()->has('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('mobil', function ($mq) use ($search) {
                    $mq->where('nama', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhereHas('merek', function ($mrq) use ($search) {
                            $mrq->where('nama', 'like', "%{$search}%");
                        });
                })
                    ->orWhere('warna', 'like', "%{$search}%");
            });
        }

        if (request()->has('min_harga_jual')) {
            $query->where('harga_jual', '>=', request('min_harga_jual'));
        }

        if (request()->has('max_harga_jual')) {
            $query->where('harga_jual', '<=', request('max_harga_jual'));
        }

        $result = $query->paginate(request()->query('per_page', 15))
            ->appends(request()->query());

        return StokMobilTransformer::collection($result);
    }
}
