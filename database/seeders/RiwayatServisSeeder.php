<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatServisSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $riwayatServis = [
            [
                'stok_mobil_id' => 1, // Toyota Avanza
                'tanggal_servis' => Carbon::create(2024, 6, 15),
                'jenis_servis' => 'Servis Rutin',
                'tempat_servis' => 'Bengkel Resmi Toyota Astra',
                'deskripsi' => 'Ganti oli mesin, filter oli, dan cek sistem rem.',
                'biaya' => 500000.00,
                'kilometer_servis' => 12000,
                'foto_bukti' => json_encode([
                    'bukti_servis' => 'storage/fotos/servis_avanza_123456_20240615.jpg',
                    'nota' => 'storage/fotos/nota_avanza_123456_20240615.jpg',
                ]),
                'sparepart' => json_encode([
                    ['nama' => 'Oli Mesin Toyota 10W-30', 'jumlah' => 4, 'harga' => 100000],
                    ['nama' => 'Filter Oli', 'jumlah' => 1, 'harga' => 50000],
                ]),
                'aktif' => true,
            ],
            [
                'stok_mobil_id' => 1, // Toyota Avanza
                'tanggal_servis' => Carbon::create(2024, 12, 10),
                'jenis_servis' => 'Tune-up',
                'tempat_servis' => 'Bengkel Resmi Toyota Astra',
                'deskripsi' => 'Tune-up mesin, cek sistem kelistrikan, dan perbaikan rem.',
                'biaya' => 750000.00,
                'kilometer_servis' => 14500,
                'foto_bukti' => json_encode([
                    'bukti_servis' => 'storage/fotos/servis_avanza_123456_20241210.jpg',
                ]),
                'sparepart' => json_encode([
                    ['nama' => 'Busi', 'jumlah' => 4, 'harga' => 80000],
                    ['nama' => 'Filter Udara', 'jumlah' => 1, 'harga' => 100000],
                ]),
                'aktif' => true,
            ],
            [
                'stok_mobil_id' => 2, // Honda Brio
                'tanggal_servis' => Carbon::create(2024, 8, 10),
                'jenis_servis' => 'Servis Rutin',
                'tempat_servis' => 'Bengkel Resmi Honda',
                'deskripsi' => 'Ganti oli mesin, filter udara, dan cek suspensi.',
                'biaya' => 400000.00,
                'kilometer_servis' => 18000,
                'foto_bukti' => json_encode([
                    'bukti_servis' => 'storage/fotos/servis_brio_654321_20240810.jpg',
                    'nota' => 'storage/fotos/nota_brio_654321_20240810.jpg',
                ]),
                'sparepart' => json_encode([
                    ['nama' => 'Oli Mesin Honda 0W-20', 'jumlah' => 3, 'harga' => 90000],
                    ['nama' => 'Filter Udara', 'jumlah' => 1, 'harga' => 70000],
                ]),
                'aktif' => true,
            ],
            [
                'stok_mobil_id' => 3, // Hyundai Ioniq 5
                'tanggal_servis' => Carbon::create(2024, 10, 5),
                'jenis_servis' => 'Pengecekan Baterai',
                'tempat_servis' => 'Bengkel Resmi Hyundai',
                'deskripsi' => 'Pengecekan sistem baterai dan update software.',
                'biaya' => 300000.00,
                'kilometer_servis' => 4500,
                'foto_bukti' => json_encode([
                    'bukti_servis' => 'storage/fotos/servis_ioniq5_789123_20241005.jpg',
                ]),
                'sparepart' => json_encode([]),
                'aktif' => true,
            ],
            [
                'stok_mobil_id' => 4, // Mitsubishi Xpander
                'tanggal_servis' => Carbon::create(2024, 7, 20),
                'jenis_servis' => 'Servis Rutin',
                'tempat_servis' => 'Bengkel Resmi Mitsubishi',
                'deskripsi' => 'Ganti oli mesin, cek suspensi, dan perbaikan AC.',
                'biaya' => 600000.00,
                'kilometer_servis' => 22000,
                'foto_bukti' => json_encode([
                    'bukti_servis' => 'storage/fotos/servis_xpander_456789_20240720.jpg',
                    'nota' => 'storage/fotos/nota_xpander_456789_20240720.jpg',
                ]),
                'sparepart' => json_encode([
                    ['nama' => 'Oli Mesin Mitsubishi 5W-30', 'jumlah' => 4, 'harga' => 110000],
                    ['nama' => 'Filter AC', 'jumlah' => 1, 'harga' => 80000],
                ]),
                'aktif' => true,
            ],
            [
                'stok_mobil_id' => 5, // Wuling Air ev
                'tanggal_servis' => Carbon::create(2024, 11, 15),
                'jenis_servis' => 'Pengecekan Sistem Listrik',
                'tempat_servis' => 'Bengkel Resmi Wuling',
                'deskripsi' => 'Pengecekan baterai dan sistem kelistrikan, update software.',
                'biaya' => 250000.00,
                'kilometer_servis' => 7000,
                'foto_bukti' => json_encode([
                    'bukti_servis' => 'storage/fotos/servis_airev_987654_20241115.jpg',
                ]),
                'sparepart' => json_encode([]),
                'aktif' => true,
            ],
        ];

        foreach ($riwayatServis as $servis) {
            DB::table('riwayat_servis')->insert([
                'stok_mobil_id' => $servis['stok_mobil_id'],
                'tanggal_servis' => $servis['tanggal_servis'],
                'jenis_servis' => $servis['jenis_servis'],
                'tempat_servis' => $servis['tempat_servis'],
                'deskripsi' => $servis['deskripsi'],
                'biaya' => $servis['biaya'],
                'kilometer_servis' => $servis['kilometer_servis'],
                'foto_bukti' => $servis['foto_bukti'],
                'sparepart' => $servis['sparepart'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}