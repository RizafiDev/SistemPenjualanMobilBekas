<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobilSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $mobils = [
            [
                'nama' => 'Toyota Avanza',
                'merek_id' => 1, // Toyota
                'kategori_id' => 2, // MPV
                'tahun_mulai' => 2003,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 7,
                'tipe_bodi' => 'mpv',
                'status' => 'aktif',
                'deskripsi' => 'Toyota Avanza adalah MPV keluarga terpopuler di Indonesia, dikenal karena keandalan dan harga terjangkau.',
                'fitur_unggulan' => 'Desain modern, hemat bahan bakar, interior luas, fitur keselamatan seperti ABS dan airbag.',
            ],
            [
                'nama' => 'Honda Brio',
                'merek_id' => 3, // Honda
                'kategori_id' => 4, // LCGC
                'tahun_mulai' => 2012,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'hatchback',
                'status' => 'aktif',
                'deskripsi' => 'Honda Brio adalah hatchback LCGC yang kompak, cocok untuk mobilitas perkotaan dengan desain sporty.',
                'fitur_unggulan' => 'Mesin i-VTEC, konsumsi bahan bakar efisien, touchscreen infotainment, desain stylish.',
            ],
            [
                'nama' => 'Hyundai Ioniq 5',
                'merek_id' => 6, // Hyundai
                'kategori_id' => 6, // Mobil Listrik
                'tahun_mulai' => 2022,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'Hyundai Ioniq 5 adalah SUV listrik premium dengan teknologi canggih dan jangkauan baterai jauh.',
                'fitur_unggulan' => 'Baterai jarak jauh, pengisian cepat, desain futuristik, fitur ADAS seperti lane assist.',
            ],
            [
                'nama' => 'Mitsubishi Xpander',
                'merek_id' => 5, // Mitsubishi
                'kategori_id' => 2, // MPV
                'tahun_mulai' => 2017,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 7,
                'tipe_bodi' => 'mpv',
                'status' => 'aktif',
                'deskripsi' => 'Mitsubishi Xpander adalah MPV serbaguna dengan desain tangguh, ideal untuk keluarga Indonesia.',
                'fitur_unggulan' => 'Ground clearance tinggi, interior fleksibel, fitur keselamatan modern, konsumsi bahan bakar efisien.',
            ],
            [
                'nama' => 'Wuling Air ev',
                'merek_id' => 7, // Wuling
                'kategori_id' => 6, // Mobil Listrik
                'tahun_mulai' => 2022,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 4,
                'tipe_bodi' => 'hatchback',
                'status' => 'aktif',
                'deskripsi' => 'Wuling Air ev adalah mobil listrik kompak, ideal untuk penggunaan perkotaan dengan harga terjangkau.',
                'fitur_unggulan' => 'Desain minimalis, baterai tahan lama, fitur parkir otomatis, ramah lingkungan.',
            ],
            [
                'nama' => 'Daihatsu Ayla',
                'merek_id' => 2, // Daihatsu
                'kategori_id' => 4, // LCGC
                'tahun_mulai' => 2013,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'hatchback',
                'status' => 'aktif',
                'deskripsi' => 'Daihatsu Ayla adalah mobil LCGC yang hemat dan praktis, cocok untuk penggunaan sehari-hari.',
                'fitur_unggulan' => 'Mesin 1.0L/1.2L, harga terjangkau, konsumsi bahan bakar efisien, desain kompak.',
            ],
            [
                'nama' => 'Toyota Fortuner',
                'merek_id' => 1, // Toyota
                'kategori_id' => 1, // SUV
                'tahun_mulai' => 2005,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 7,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'Toyota Fortuner adalah SUV tangguh dengan performa kuat, cocok untuk petualangan dan penggunaan keluarga.',
                'fitur_unggulan' => 'Mesin diesel bertenaga, 4x4 opsional, interior premium, fitur keselamatan lengkap.',
            ],
            [
                'nama' => 'BYD Atto 3',
                'merek_id' => 8, // BYD
                'kategori_id' => 6, // Mobil Listrik
                'tahun_mulai' => 2023,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'BYD Atto 3 adalah SUV listrik dengan teknologi canggih, menawarkan kenyamanan dan efisiensi.',
                'fitur_unggulan' => 'Blade Battery, jangkauan hingga 420 km, interior modern, sistem infotainment canggih.',
            ],
            [
                'nama' => 'Suzuki Ertiga',
                'merek_id' => 4, // Suzuki
                'kategori_id' => 2, // MPV
                'tahun_mulai' => 2012,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 7,
                'tipe_bodi' => 'mpv',
                'status' => 'aktif',
                'deskripsi' => 'Suzuki Ertiga adalah MPV keluarga yang ekonomis dengan desain modern dan ruang kabin luas.',
                'fitur_unggulan' => 'Mesin hybrid opsional, fitur keselamatan seperti ESP, desain stylish, hemat bahan bakar.',
            ],
            [
                'nama' => 'Honda Civic',
                'merek_id' => 3, // Honda
                'kategori_id' => 3, // Sedan
                'tahun_mulai' => 1972,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'sedan',
                'status' => 'aktif',
                'deskripsi' => 'Honda Civic adalah sedan sporty dengan performa tinggi, cocok untuk penggemar berkendara dinamis.',
                'fitur_unggulan' => 'Mesin turbo, Honda SENSING, desain aerodinamis, interior premium.',
            ],
            [
                'nama' => 'Isuzu D-Max',
                'merek_id' => 17, // Isuzu
                'kategori_id' => 7, // Pick-up
                'tahun_mulai' => 2002,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'pickup',
                'status' => 'aktif',
                'deskripsi' => 'Isuzu D-Max adalah pick-up tangguh, ideal untuk kebutuhan komersial dan medan berat.',
                'fitur_unggulan' => 'Mesin diesel kuat, kemampuan off-road, daya angkut besar, fitur keselamatan modern.',
            ],
            [
                'nama' => 'Chery Tiggo 8 Pro',
                'merek_id' => 9, // Chery
                'kategori_id' => 1, // SUV
                'tahun_mulai' => 2022,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 7,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'Chery Tiggo 8 Pro adalah SUV premium dengan teknologi canggih dan kenyamanan keluarga.',
                'fitur_unggulan' => 'Mesin turbo, panoramic sunroof, ADAS, interior mewah.',
            ],
            [
                'nama' => 'BMW X3',
                'merek_id' => 12, // BMW
                'kategori_id' => 1, // SUV
                'tahun_mulai' => 2003,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'BMW X3 adalah SUV premium dengan performa tinggi dan desain elegan.',
                'fitur_unggulan' => 'Mesin bertenaga, iDrive system, fitur keselamatan canggih, desain sporty.',
            ],
            [
                'nama' => 'Wuling Almaz',
                'merek_id' => 7, // Wuling
                'kategori_id' => 1, // SUV
                'tahun_mulai' => 2019,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 7,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'Wuling Almaz adalah SUV modern dengan fitur canggih dan harga kompetitif.',
                'fitur_unggulan' => 'WIND voice command, panoramic sunroof, fitur keselamatan lengkap, desain stylish.',
            ],
            [
                'nama' => 'Neta V-II',
                'merek_id' => 10, // Neta
                'kategori_id' => 6, // Mobil Listrik
                'tahun_mulai' => 2024,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'suv',
                'status' => 'aktif',
                'deskripsi' => 'Neta V-II adalah SUV listrik kompak dengan teknologi modern untuk mobilitas perkotaan.',
                'fitur_unggulan' => 'Baterai jarak jauh, layar infotainment besar, fitur ADAS, desain futuristik.',
            ],
            [
                'nama' => 'Toyota Corolla',
                'merek_id' => 1, // Toyota
                'kategori_id' => 3, // Sedan
                'tahun_mulai' => 1966,
                'tahun_akhir' => null,
                'kapasitas_penumpang' => 5,
                'tipe_bodi' => 'sedan',
                'status' => 'aktif',
                'deskripsi' => 'Toyota Corolla adalah sedan legendaris dengan keandalan tinggi dan desain modern.',
                'fitur_unggulan' => 'Mesin hybrid opsional, Toyota Safety Sense, interior nyaman, hemat bahan bakar.',
            ],
        ];

        foreach ($mobils as $mobil) {
            DB::table('mobils')->insert([
                'nama' => $mobil['nama'],
                'slug' => Str::slug($mobil['nama']),
                'merek_id' => $mobil['merek_id'],
                'kategori_id' => $mobil['kategori_id'],
                'tahun_mulai' => $mobil['tahun_mulai'],
                'tahun_akhir' => $mobil['tahun_akhir'],
                'kapasitas_penumpang' => $mobil['kapasitas_penumpang'],
                'tipe_bodi' => $mobil['tipe_bodi'],
                'status' => $mobil['status'],
                'deskripsi' => $mobil['deskripsi'],
                'fitur_unggulan' => $mobil['fitur_unggulan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}