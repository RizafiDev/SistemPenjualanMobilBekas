<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokMobilSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $stokMobils = [
            [
                'mobil_id' => 1, // Toyota Avanza
                'varian_id' => 1, // Avanza 1.5 G CVT
                'warna' => 'Putih Mutiara',
                'no_rangka' => 'MHKM1BA3XNJ123456',
                'no_mesin' => '2NRVE123456',
                'tahun' => 2024,
                'kilometer' => 15000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_beli' => 260000000.00,
                'harga_jual' => 280000000.00,
                'tanggal_masuk' => Carbon::create(2025, 1, 10),
                'tanggal_keluar' => null,
                'lokasi' => 'Jakarta',
                'catatan' => 'Mobil bekas dalam kondisi baik, servis rutin di dealer resmi.',
                'kelengkapan' => json_encode(['Kunci Serep', 'Buku Servis', 'Toolkit', 'Dongkrak']),
                'riwayat_perbaikan' => json_encode([
                    ['tanggal' => '2024-06-15', 'deskripsi' => 'Ganti oli mesin dan filter', 'biaya' => 500000],
                    ['tanggal' => '2024-12-10', 'deskripsi' => 'Tune-up dan cek rem', 'biaya' => 750000],
                ]),
                'dokumen' => json_encode(['BPKB', 'STNK', 'Faktur', 'Sertifikat Garansi']),
                'foto_kondisi' => json_encode([
                    'eksterior_depan' => 'storage/fotos/avanza_123456_depan.jpg',
                    'eksterior_belakang' => 'storage/fotos/avanza_123456_belakang.jpg',
                    'interior' => 'storage/fotos/avanza_123456_interior.jpg',
                ]),
                'kondisi_fitur' => json_encode([
                    'ABS' => 'baik',
                    'Airbag Pengemudi' => 'baik',
                    'AC' => 'baik',
                    'Touchscreen Head Unit' => 'baik',
                ]),
                'aktif' => true,
            ],
            [
                'mobil_id' => 2, // Honda Brio
                'varian_id' => 2, // Brio RS CVT
                'warna' => 'Hitam Metalik',
                'no_rangka' => 'MHRGE8H5XPJ654321',
                'no_mesin' => 'L12B123456',
                'tahun' => 2023,
                'kilometer' => 20000,
                'kondisi' => 'sangat_baik',
                'status' => 'terjual',
                'harga_beli' => 230000000.00,
                'harga_jual' => 245000000.00,
                'tanggal_masuk' => Carbon::create(2024, 11, 20),
                'tanggal_keluar' => Carbon::create(2025, 2, 15),
                'lokasi' => 'Bandung',
                'catatan' => 'Mobil dalam kondisi prima, digunakan pribadi, bebas kecelakaan.',
                'kelengkapan' => json_encode(['Kunci Serep', 'Buku Servis', 'Toolkit']),
                'riwayat_perbaikan' => json_encode([
                    ['tanggal' => '2024-08-10', 'deskripsi' => 'Servis rutin dan ganti filter udara', 'biaya' => 400000],
                ]),
                'dokumen' => json_encode(['BPKB', 'STNK', 'Faktur']),
                'foto_kondisi' => json_encode([
                    'eksterior_depan' => 'storage/fotos/brio_654321_depan.jpg',
                    'eksterior_samping' => 'storage/fotos/brio_654321_samping.jpg',
                    'interior' => 'storage/fotos/brio_654321_interior.jpg',
                ]),
                'kondisi_fitur' => json_encode([
                    'ABS' => 'baik',
                    'Airbag Pengemudi' => 'baik',
                    'AC' => 'baik',
                    'Touchscreen Head Unit' => 'baik',
                ]),
                'aktif' => true,
            ],
            [
                'mobil_id' => 3, // Hyundai Ioniq 5
                'varian_id' => 3, // Ioniq 5 Signature Long Range
                'warna' => 'Gravity Gold Matte',
                'no_rangka' => 'KMHC851HFNU789123',
                'no_mesin' => 'EM123456789',
                'tahun' => 2024,
                'kilometer' => 5000,
                'kondisi' => 'sangat_baik',
                'status' => 'tersedia',
                'harga_beli' => 850000000.00,
                'harga_jual' => 890000000.00,
                'tanggal_masuk' => Carbon::create(2025, 3, 1),
                'tanggal_keluar' => null,
                'lokasi' => 'Surabaya',
                'catatan' => 'Mobil listrik premium, hampir baru, digunakan untuk test drive.',
                'kelengkapan' => json_encode(['Kunci Serep', 'Buku Servis', 'Kabel Pengisian', 'Toolkit']),
                'riwayat_perbaikan' => json_encode([]),
                'dokumen' => json_encode(['BPKB', 'STNK', 'Faktur', 'Sertifikat Garansi']),
                'foto_kondisi' => json_encode([
                    'eksterior_depan' => 'storage/fotos/ioniq5_789123_depan.jpg',
                    'eksterior_belakang' => 'storage/fotos/ioniq5_789123_belakang.jpg',
                    'interior' => 'storage/fotos/ioniq5_789123_interior.jpg',
                ]),
                'kondisi_fitur' => json_encode([
                    'ABS' => 'baik',
                    'Airbag Pengemudi' => 'baik',
                    'AC Dual Zone' => 'baik',
                    'Touchscreen Head Unit' => 'baik',
                    'Cruise Control' => 'baik',
                ]),
                'aktif' => true,
            ],
            [
                'mobil_id' => 4, // Mitsubishi Xpander
                'varian_id' => 4, // Xpander Ultimate CVT
                'warna' => 'Silver Metalik',
                'no_rangka' => 'MHFNX45EXMJ456789',
                'no_mesin' => '4A91X123456',
                'tahun' => 2023,
                'kilometer' => 25000,
                'kondisi' => 'baik',
                'status' => 'booking',
                'harga_beli' => 300000000.00,
                'harga_jual' => 320000000.00,
                'tanggal_masuk' => Carbon::create(2024, 12, 5),
                'tanggal_keluar' => null,
                'lokasi' => 'Jakarta',
                'catatan' => 'Mobil dalam kondisi baik, sedang dipesan pelanggan, menunggu pembayaran.',
                'kelengkapan' => json_encode(['Kunci Serep', 'Buku Servis', 'Toolkit']),
                'riwayat_perbaikan' => json_encode([
                    ['tanggal' => '2024-07-20', 'deskripsi' => 'Ganti oli dan cek suspensi', 'biaya' => 600000],
                ]),
                'dokumen' => json_encode(['BPKB', 'STNK', 'Faktur']),
                'foto_kondisi' => json_encode([
                    'eksterior_depan' => 'storage/fotos/xpander_456789_depan.jpg',
                    'eksterior_samping' => 'storage/fotos/xpander_456789_samping.jpg',
                    'interior' => 'storage/fotos/xpander_456789_interior.jpg',
                ]),
                'kondisi_fitur' => json_encode([
                    'ABS' => 'baik',
                    'Airbag Pengemudi' => 'baik',
                    'AC' => 'baik',
                    'Touchscreen Head Unit' => 'baik',
                ]),
                'aktif' => true,
            ],
            [
                'mobil_id' => 5, // Wuling Air ev
                'varian_id' => 5, // Air ev Long Range
                'warna' => 'Putih',
                'no_rangka' => 'LZWADAGA1NJ987654',
                'no_mesin' => 'EV123456789',
                'tahun' => 2024,
                'kilometer' => 8000,
                'kondisi' => 'sangat_baik',
                'status' => 'tersedia',
                'harga_beli' => 280000000.00,
                'harga_jual' => 305000000.00,
                'tanggal_masuk' => Carbon::create(2025, 2, 10),
                'tanggal_keluar' => null,
                'lokasi' => 'Medan',
                'catatan' => 'Mobil listrik dalam kondisi hampir baru, ideal untuk penggunaan kota.',
                'kelengkapan' => json_encode(['Kunci Serep', 'Buku Servis', 'Kabel Pengisian']),
                'riwayat_perbaikan' => json_encode([]),
                'dokumen' => json_encode(['BPKB', 'STNK', 'Faktur']),
                'foto_kondisi' => json_encode([
                    'eksterior_depan' => 'storage/fotos/airev_987654_depan.jpg',
                    'eksterior_belakang' => 'storage/fotos/airev_987654_belakang.jpg',
                    'interior' => 'storage/fotos/airev_987654_interior.jpg',
                ]),
                'kondisi_fitur' => json_encode([
                    'ABS' => 'baik',
                    'Airbag Pengemudi' => 'baik',
                    'AC' => 'baik',
                    'Touchscreen Head Unit' => 'baik',
                ]),
                'aktif' => true,
            ],
        ];

        foreach ($stokMobils as $stok) {
            DB::table('stok_mobils')->insert([
                'mobil_id' => $stok['mobil_id'],
                'varian_id' => $stok['varian_id'],
                'warna' => $stok['warna'],
                'no_rangka' => $stok['no_rangka'],
                'no_mesin' => $stok['no_mesin'],
                'tahun' => $stok['tahun'],
                'kilometer' => $stok['kilometer'],
                'kondisi' => $stok['kondisi'],
                'status' => $stok['status'],
                'harga_beli' => $stok['harga_beli'],
                'harga_jual' => $stok['harga_jual'],
                'tanggal_masuk' => $stok['tanggal_masuk'],
                'tanggal_keluar' => $stok['tanggal_keluar'],
                'lokasi' => $stok['lokasi'],
                'catatan' => $stok['catatan'],
                'kelengkapan' => $stok['kelengkapan'],
                'riwayat_perbaikan' => $stok['riwayat_perbaikan'],
                'dokumen' => $stok['dokumen'],
                'foto_kondisi' => $stok['foto_kondisi'],
                'kondisi_fitur' => $stok['kondisi_fitur'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}