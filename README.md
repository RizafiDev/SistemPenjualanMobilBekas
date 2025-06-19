# Car Dealer Management System

Sistem manajemen dealer mobil bekas dengan fitur manajemen karyawan dan absensi.

## Fitur Utama

### Admin Panel (Filament)
- **Master Data Mobil**
  - Manajemen Merek
  - Manajemen Kategori
  - Manajemen Model Mobil
  - Manajemen Stok Mobil

- **Manajemen Pelanggan**
  - Data Pelanggan
  - Riwayat Pembelian

- **Manajemen Penjualan**
  - Transaksi Penjualan
  - Pembayaran
  - Laporan Penjualan

- **Manajemen Karyawan**
  - Data Karyawan
  - Penggajian
  - Laporan Presensi

### Sistem Absensi Karyawan (Blade)
- **Login Terpisah** untuk karyawan
- **Absensi dengan Foto** dan lokasi GPS
- **Validasi Radius** kantor
- **Riwayat Absensi** personal
- **Dashboard** status harian

## Instalasi

1. Clone repository
\`\`\`bash
git clone <repository-url>
cd car-dealer-system
\`\`\`

2. Install dependencies
\`\`\`bash
composer install
npm install
\`\`\`

3. Setup environment
\`\`\`bash
cp .env.example .env
php artisan key:generate
\`\`\`

4. Setup database
\`\`\`bash
php artisan migrate
php artisan db:seed
\`\`\`

5. Create admin user
\`\`\`bash
php artisan make:filament-user
\`\`\`

6. Start development server
\`\`\`bash
php artisan serve
npm run dev
\`\`\`

## Akses Sistem

### Admin Panel (Filament)
- URL: `http://localhost:8000/admin`
- Login dengan user yang dibuat via `make:filament-user`

### Absensi Karyawan
- URL: `http://localhost:8000/absensi/login`
- Default login: NIP `001`, Password `123456`

## Struktur Database

Sistem menggunakan 18 tabel utama:
- **mereks** - Data merek mobil
- **kategoris** - Kategori mobil
- **mobils** - Model mobil
- **stok_mobils** - Stok mobil tersedia
- **pelanggan** - Data pelanggan
- **karyawans** - Data karyawan
- **presensis** - Data absensi
- **pengaturan_kantor** - Setting lokasi kantor
- Dan tabel lainnya untuk penjualan, pembayaran, dll.

## Teknologi

- **Laravel 12** - Framework PHP
- **Filament 3** - Admin panel
- **Tailwind CSS** - Styling
- **MySQL** - Database
- **JavaScript** - Frontend interactivity
- **HTML5 Geolocation & Camera API** - Absensi features

## Konfigurasi Absensi

1. Set lokasi kantor di tabel `pengaturan_kantor`
2. Atur radius, jam kerja, dan toleransi keterlambatan
3. Karyawan login dengan NIP dan password terpisah dari admin

## Kontribusi

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

MIT License
