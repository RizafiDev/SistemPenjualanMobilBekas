<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'nama' => 'SUV',
                'deskripsi' => 'Sport Utility Vehicle, kendaraan tangguh dengan ground clearance tinggi, cocok untuk berbagai medan dan gaya hidup aktif.',
                'urutan_tampil' => 1,
                'unggulan' => true,
            ],
            [
                'nama' => 'MPV',
                'deskripsi' => 'Multi-Purpose Vehicle, mobil keluarga dengan kapasitas besar, ideal untuk perjalanan bersama.',
                'urutan_tampil' => 2,
                'unggulan' => true,
            ],
            [
                'nama' => 'Sedan',
                'deskripsi' => 'Mobil dengan desain elegan dan kompartemen terpisah untuk penumpang dan bagasi, cocok untuk penggunaan perkotaan.',
                'urutan_tampil' => 3,
                'unggulan' => false,
            ],
            [
                'nama' => 'LCGC',
                'deskripsi' => 'Low Cost Green Car, mobil hemat bahan bakar dan ramah lingkungan dengan harga terjangkau.',
                'urutan_tampil' => 4,
                'unggulan' => true,
            ],
            [
                'nama' => 'Hatchback',
                'deskripsi' => 'Mobil kompak dengan pintu belakang yang terintegrasi, praktis untuk penggunaan sehari-hari di kota.',
                'urutan_tampil' => 5,
                'unggulan' => false,
            ],
            [
                'nama' => 'Mobil Listrik',
                'deskripsi' => 'Kendaraan bertenaga listrik, ramah lingkungan dengan teknologi canggih, seperti BEV dan PHEV.',
                'urutan_tampil' => 6,
                'unggulan' => true,
            ],
            [
                'nama' => 'Pick-up',
                'deskripsi' => 'Kendaraan dengan bak terbuka, cocok untuk kebutuhan komersial dan transportasi barang.',
                'urutan_tampil' => 7,
                'unggulan' => false,
            ],
            [
                'nama' => 'Van',
                'deskripsi' => 'Kendaraan dengan ruang kargo besar, digunakan untuk transportasi penumpang atau barang.',
                'urutan_tampil' => 8,
                'unggulan' => false,
            ],
            [
                'nama' => 'Crossover',
                'deskripsi' => 'Kombinasi antara SUV dan hatchback, menawarkan gaya modern dan efisiensi bahan bakar.',
                'urutan_tampil' => 9,
                'unggulan' => false,
            ],
            [
                'nama' => 'Mobil Sport',
                'deskripsi' => 'Kendaraan dengan performa tinggi dan desain aerodinamis, dirancang untuk pengalaman berkendara dinamis.',
                'urutan_tampil' => 10,
                'unggulan' => false,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('kategoris')->insert([
                'nama' => $category['nama'],
                'slug' => Str::slug($category['nama']),
                'deskripsi' => $category['deskripsi'],
                'ikon' => null, // Tambahkan path ikon atau class jika tersedia
                'urutan_tampil' => $category['urutan_tampil'],
                'unggulan' => $category['unggulan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}