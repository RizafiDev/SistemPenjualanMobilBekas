<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MerekSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $carBrands = [
            [
                'nama' => 'Toyota',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Toyota adalah merek mobil terbesar di Indonesia, dikenal dengan kendaraan andal seperti Avanza, Innova, dan Fortuner.',
                'tahun_berdiri' => 1937,
                'aktif' => true,
            ],
            [
                'nama' => 'Daihatsu',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Daihatsu menawarkan mobil LCGC seperti Sigra dan Ayla, serta SUV seperti Terios, populer di kalangan keluarga Indonesia.',
                'tahun_berdiri' => 1907,
                'aktif' => true,
            ],
            [
                'nama' => 'Honda',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Honda terkenal dengan model seperti Brio, HR-V, dan CR-V, serta inovasi hybrid seperti HR-V e:HEV.',
                'tahun_berdiri' => 1948,
                'aktif' => true,
            ],
            [
                'nama' => 'Suzuki',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Suzuki dikenal dengan MPV seperti Ertiga dan SUV seperti Fronx, menawarkan harga kompetitif dan fitur modern.',
                'tahun_berdiri' => 1909,
                'aktif' => true,
            ],
            [
                'nama' => 'Mitsubishi',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Mitsubishi populer dengan Xpander dan Pajero Sport, serta truk seperti L100 EV untuk kebutuhan komersial.',
                'tahun_berdiri' => 1970,
                'aktif' => true,
            ],
            [
                'nama' => 'Hyundai',
                'negara_asal' => 'Korea Selatan',
                'deskripsi' => 'Hyundai menawarkan mobil listrik seperti Ioniq 5 dan Kona EV, serta SUV seperti Santa Fe dan Palisade.',
                'tahun_berdiri' => 1967,
                'aktif' => true,
            ],
            [
                'nama' => 'Wuling',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Wuling dikenal dengan mobil listrik seperti Binguo EV dan Air ev, serta MPV seperti Cortez.',
                'tahun_berdiri' => 2002,
                'aktif' => true,
            ],
            [
                'nama' => 'BYD',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'BYD mendominasi pasar mobil listrik di Indonesia dengan model seperti M6, Seal, dan Atto 3.',
                'tahun_berdiri' => 1995,
                'aktif' => true,
            ],
            [
                'nama' => 'Chery',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Chery menawarkan SUV seperti Tiggo 8 dan mobil listrik seperti Omoda E5, dengan teknologi canggih.',
                'tahun_berdiri' => 1997,
                'aktif' => true,
            ],
            [
                'nama' => 'Neta',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Neta fokus pada mobil listrik seperti Neta V-II dan Neta X, menawarkan solusi ramah lingkungan.',
                'tahun_berdiri' => 2018,
                'aktif' => true,
            ],
            [
                'nama' => 'MG',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'MG (Morris Garages) menawarkan mobil listrik seperti MG 4EV dan ZS EV, serta SUV seperti ZS 2025.',
                'tahun_berdiri' => 1924,
                'aktif' => true,
            ],
            [
                'nama' => 'BMW',
                'negara_asal' => 'Jerman',
                'deskripsi' => 'BMW adalah merek premium dengan model seperti X3, iX1, dan i5, dikenal dengan desain elegan dan performa tinggi.',
                'tahun_berdiri' => 1916,
                'aktif' => true,
            ],
            [
                'nama' => 'Mercedes-Benz',
                'negara_asal' => 'Jerman',
                'deskripsi' => 'Mercedes-Benz menawarkan mobil mewah seperti G-Class Electric dan Maybach GLS-Class untuk segmen premium.',
                'tahun_berdiri' => 1926,
                'aktif' => true,
            ],
            [
                'nama' => 'Volvo',
                'negara_asal' => 'Swedia',
                'deskripsi' => 'Volvo fokus pada SUV premium seperti XC90 dan mobil listrik seperti EX30, dengan teknologi keselamatan tinggi.',
                'tahun_berdiri' => 1927,
                'aktif' => true,
            ],
            [
                'nama' => 'Citroen',
                'negara_asal' => 'Prancis',
                'deskripsi' => 'Citroen hadir dengan mobil listrik seperti E-C3 dan SUV seperti Basalt, menawarkan desain unik.',
                'tahun_berdiri' => 1919,
                'aktif' => true,
            ],
            [
                'nama' => 'Kia',
                'negara_asal' => 'Korea Selatan',
                'deskripsi' => 'Kia menawarkan mobil listrik seperti EV9 dan SUV seperti Seltos, dengan desain modern dan teknologi canggih.',
                'tahun_berdiri' => 1944,
                'aktif' => true,
            ],
            [
                'nama' => 'Isuzu',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Isuzu dikenal dengan kendaraan komersial seperti truk dan pick-up D-Max, tangguh untuk berbagai medan.',
                'tahun_berdiri' => 1916,
                'aktif' => true,
            ],
            [
                'nama' => 'Hino',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Hino fokus pada truk dan bus, seperti Ranger, untuk kebutuhan logistik dan transportasi.',
                'tahun_berdiri' => 1942,
                'aktif' => true,
            ],
            [
                'nama' => 'Mitsubishi Fuso',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Mitsubishi Fuso menyediakan truk seperti Canter untuk kebutuhan komersial di Indonesia.',
                'tahun_berdiri' => 1932,
                'aktif' => true,
            ],
            [
                'nama' => 'Nissan',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Nissan menawarkan SUV seperti Terra dan teknologi keselamatan seperti ProPilot 2.0.',
                'tahun_berdiri' => 1933,
                'aktif' => true,
            ],
            [
                'nama' => 'Mazda',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Mazda dikenal dengan desain KODO dan model seperti CX-5, menawarkan pengalaman berkendara premium.',
                'tahun_berdiri' => 1920,
                'aktif' => true,
            ],
            [
                'nama' => 'Lexus',
                'negara_asal' => 'Jepang',
                'deskripsi' => 'Lexus, merek mewah Toyota, menawarkan MPV seperti LM dan SUV seperti RX untuk segmen premium.',
                'tahun_berdiri' => 1989,
                'aktif' => true,
            ],
            [
                'nama' => 'Jeep',
                'negara_asal' => 'Amerika Serikat',
                'deskripsi' => 'Jeep menawarkan SUV off-road seperti Wrangler dan Cherokee, cocok untuk petualangan.',
                'tahun_berdiri' => 1941,
                'aktif' => true,
            ],
            [
                'nama' => 'Esemka',
                'negara_asal' => 'Indonesia',
                'deskripsi' => 'Esemka adalah merek lokal Indonesia dengan model seperti Bima, fokus pada kendaraan terjangkau.',
                'tahun_berdiri' => 2009,
                'aktif' => true,
            ],
            [
                'nama' => 'Xpeng',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Xpeng menghadirkan mobil listrik seperti G6, dirancang untuk kebutuhan pasar Indonesia.',
                'tahun_berdiri' => 2014,
                'aktif' => true,
            ],
            [
                'nama' => 'JAECOO',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'JAECOO, bagian dari Chery Group, menawarkan SUV premium seperti J8 AWD.',
                'tahun_berdiri' => 2023,
                'aktif' => true,
            ],
            [
                'nama' => 'Mini',
                'negara_asal' => 'Inggris',
                'deskripsi' => 'Mini menawarkan mobil listrik seperti Cooper EV dan Countryman EV, dengan desain ikonik.',
                'tahun_berdiri' => 1959,
                'aktif' => true,
            ],
            [
                'nama' => 'Maxfort',
                'negara_asal' => 'Indonesia',
                'deskripsi' => 'Maxfort, di bawah Banteng Mas Group, menawarkan kendaraan multiguna seperti TAC-ROV.',
                'tahun_berdiri' => 2024,
                'aktif' => true,
            ],
            [
                'nama' => 'Aion',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Aion, bagian dari GAC Group, menawarkan mobil listrik seperti Y Plus untuk pasar Indonesia.',
                'tahun_berdiri' => 2017,
                'aktif' => true,
            ],
            [
                'nama' => 'Hyptec',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Hyptec menghadirkan mobil listrik seperti HT, fokus pada teknologi ramah lingkungan.',
                'tahun_berdiri' => 2020,
                'aktif' => true,
            ],
            [
                'nama' => 'Seres',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'Seres menawarkan mobil listrik seperti E1, cocok untuk mobilitas perkotaan.',
                'tahun_berdiri' => 2016,
                'aktif' => true,
            ],
            [
                'nama' => 'DFSK',
                'negara_asal' => 'Tiongkok',
                'deskripsi' => 'DFSK menyediakan kendaraan seperti Gelora E untuk kebutuhan komersial dan listrik.',
                'tahun_berdiri' => 2007,
                'aktif' => true,
            ],
        ];

        foreach ($carBrands as $brand) {
            DB::table('mereks')->insert([
                'nama' => $brand['nama'],
                'slug' => Str::slug($brand['nama']),
                'logo' => null, // Tambahkan path logo jika tersedia
                'negara_asal' => $brand['negara_asal'],
                'deskripsi' => $brand['deskripsi'],
                'tahun_berdiri' => $brand['tahun_berdiri'],
                'aktif' => $brand['aktif'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}