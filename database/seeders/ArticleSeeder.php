<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Tips Memilih Mobil Bekas yang Berkualitas',
                'slug' => 'tips-memilih-mobil-bekas-berkualitas',
                'content' => '<p>Membeli mobil bekas memerlukan ketelitian khusus. Berikut adalah tips yang perlu diperhatikan:</p>
                            <h3>1. Cek Kondisi Mesin</h3>
                            <p>Pastikan mesin dalam kondisi baik dan tidak ada kebocoran oli.</p>
                            <h3>2. Periksa Surat-surat</h3>
                            <p>Pastikan STNK, BPKB, dan faktur lengkap dan sesuai.</p>
                            <h3>3. Test Drive</h3>
                            <p>Lakukan test drive untuk merasakan performa mobil secara langsung.</p>',
                'excerpt' => 'Panduan lengkap memilih mobil bekas berkualitas dengan tips praktis dari para ahli.',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'meta_title' => 'Tips Memilih Mobil Bekas Berkualitas - Panduan Lengkap',
                'meta_description' => 'Panduan lengkap memilih mobil bekas berkualitas. Tips praktis dari para ahli untuk mendapatkan mobil bekas terbaik.',
            ],
            [
                'title' => 'Cara Merawat Mobil Bekas Agar Awet',
                'slug' => 'cara-merawat-mobil-bekas-agar-awet',
                'content' => '<p>Merawat mobil bekas memerlukan perhatian ekstra. Berikut tips perawatan yang efektif:</p>
                            <h3>Perawatan Rutin</h3>
                            <ul>
                                <li>Ganti oli secara berkala setiap 5000-7500 km</li>
                                <li>Periksa tekanan ban setiap minggu</li>
                                <li>Cuci mobil minimal 2 minggu sekali</li>
                            </ul>
                            <h3>Perawatan Berkala</h3>
                            <ul>
                                <li>Service besar setiap 40.000 km</li>
                                <li>Ganti kampas rem sesuai kebutuhan</li>
                                <li>Periksa sistem kelistrikan</li>
                            </ul>',
                'excerpt' => 'Tips dan trik merawat mobil bekas agar tetap prima dan tahan lama.',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'meta_title' => 'Cara Merawat Mobil Bekas Agar Awet dan Tahan Lama',
                'meta_description' => 'Tips merawat mobil bekas agar tetap prima. Panduan perawatan rutin dan berkala untuk mobil bekas.',
            ],
            [
                'title' => 'Harga Mobil Bekas Terbaru 2024',
                'slug' => 'harga-mobil-bekas-terbaru-2024',
                'content' => '<p>Berikut adalah daftar harga mobil bekas terbaru untuk tahun 2024:</p>
                            <h3>Kategori Hatchback</h3>
                            <ul>
                                <li>Toyota Agya (2018-2020): Rp 85-120 juta</li>
                                <li>Honda Brio (2017-2019): Rp 90-130 juta</li>
                                <li>Suzuki Ignis (2018-2020): Rp 95-135 juta</li>
                            </ul>
                            <h3>Kategori Sedan</h3>
                            <ul>
                                <li>Toyota Vios (2017-2019): Rp 150-200 juta</li>
                                <li>Honda City (2018-2020): Rp 170-220 juta</li>
                                <li>Nissan Almera (2019-2021): Rp 160-210 juta</li>
                            </ul>',
                'excerpt' => 'Daftar harga mobil bekas terbaru 2024 untuk berbagai kategori dan merek.',
                'status' => 'draft',
                'published_at' => null,
                'meta_title' => 'Harga Mobil Bekas Terbaru 2024 - Daftar Lengkap',
                'meta_description' => 'Daftar harga mobil bekas terbaru 2024 untuk semua kategori. Update harga terkini mobil bekas.',
            ],
            [
                'title' => 'Panduan Lengkap Proses Jual Beli Mobil Bekas',
                'slug' => 'panduan-lengkap-proses-jual-beli-mobil-bekas',
                'content' => '<p>Proses jual beli mobil bekas memerlukan persiapan yang matang. Berikut panduannya:</p>
                            <h3>Persiapan Dokumen</h3>
                            <p>Siapkan semua dokumen yang diperlukan seperti STNK, BPKB, faktur, dan KTP.</p>
                            <h3>Proses Negosiasi</h3>
                            <p>Lakukan negosiasi dengan bijak dan berdasarkan kondisi mobil yang sebenarnya.</p>
                            <h3>Proses Balik Nama</h3>
                            <p>Pastikan proses balik nama dilakukan dengan benar dan sesuai prosedur.</p>',
                'excerpt' => 'Panduan lengkap proses jual beli mobil bekas dari A sampai Z.',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
                'meta_title' => 'Panduan Lengkap Jual Beli Mobil Bekas',
                'meta_description' => 'Panduan lengkap proses jual beli mobil bekas. Tips dan trik untuk transaksi yang aman dan menguntungkan.',
            ],
            [
                'title' => 'Mobil Bekas Terpopuler di Indonesia',
                'slug' => 'mobil-bekas-terpopuler-di-indonesia',
                'content' => '<p>Berikut adalah daftar mobil bekas yang paling populer di Indonesia:</p>
                            <h3>Top 5 Mobil Bekas Terpopuler</h3>
                            <ol>
                                <li><strong>Toyota Avanza</strong> - MPV keluarga dengan nilai jual tinggi</li>
                                <li><strong>Honda Jazz</strong> - Hatchback premium dengan performa baik</li>
                                <li><strong>Suzuki Ertiga</strong> - MPV kompak dengan harga terjangkau</li>
                                <li><strong>Toyota Innova</strong> - MPV tangguh untuk keluarga besar</li>
                                <li><strong>Honda CR-V</strong> - SUV premium dengan teknologi canggih</li>
                            </ol>',
                'excerpt' => 'Daftar mobil bekas terpopuler di Indonesia berdasarkan tingkat penjualan.',
                'status' => 'archived',
                'published_at' => Carbon::now()->subDays(30),
                'meta_title' => 'Mobil Bekas Terpopuler di Indonesia 2024',
                'meta_description' => 'Daftar mobil bekas terpopuler di Indonesia. Pilihan terbaik mobil bekas dengan nilai jual tinggi.',
            ],
            [
                'title' => 'Inspeksi Mobil Bekas: Checklist Lengkap',
                'slug' => 'inspeksi-mobil-bekas-checklist-lengkap',
                'content' => '<p>Sebelum membeli mobil bekas, lakukan inspeksi menyeluruh dengan checklist ini:</p>
                            <h3>Eksterior</h3>
                            <ul>
                                <li>Cek kondisi cat dan body</li>
                                <li>Periksa karat dan bekas perbaikan</li>
                                <li>Pastikan semua lampu berfungsi</li>
                            </ul>
                            <h3>Interior</h3>
                            <ul>
                                <li>Cek kondisi jok dan dashboard</li>
                                <li>Test semua fitur elektronik</li>
                                <li>Periksa AC dan audio system</li>
                            </ul>
                            <h3>Mesin</h3>
                            <ul>
                                <li>Dengarkan suara mesin</li>
                                <li>Cek oli dan cairan lainnya</li>
                                <li>Periksa sistem kelistrikan</li>
                            </ul>',
                'excerpt' => 'Checklist lengkap untuk inspeksi mobil bekas sebelum membeli.',
                'status' => 'draft',
                'published_at' => null,
                'meta_title' => 'Checklist Inspeksi Mobil Bekas Lengkap',
                'meta_description' => 'Checklist lengkap inspeksi mobil bekas. Panduan detail untuk memeriksa kondisi mobil bekas sebelum beli.',
            ],
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        // Create some soft deleted articles
        $deletedArticles = Article::create([
            'title' => 'Artikel yang Dihapus',
            'slug' => 'artikel-yang-dihapus',
            'content' => '<p>Ini adalah contoh artikel yang telah dihapus (soft delete).</p>',
            'excerpt' => 'Contoh artikel yang dihapus.',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $deletedArticles->delete();
    }
}
