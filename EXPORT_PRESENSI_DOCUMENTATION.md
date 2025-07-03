# Fitur Export Rekap Presensi

## Deskripsi

Fitur export rekap presensi memungkinkan pengguna untuk mengekspor data presensi karyawan dalam format Excel (.xlsx) atau CSV dengan berbagai pilihan filter dan periode.

## Fitur Utama

### 1. Export Cepat (Quick Export)

-   **Lokasi**: Header table presensi
-   **Fitur**:
    -   Export rekap dengan dialog pilihan periode
    -   Filter karyawan dan status
    -   Export bulan ini langsung

### 2. Export Lanjutan (Advanced Export)

-   **Lokasi**: Menu "Export Lanjutan" → Halaman terpisah
-   **Fitur**:
    -   Pilihan periode dengan quick select
    -   Filter berdasarkan karyawan, status, departemen, jabatan
    -   Multiple sheets (Data + Ringkasan)
    -   Pengaturan format dan konten

### 3. Export Individual

-   **Lokasi**: Action pada setiap row di table
-   **Fitur**: Export data presensi per karyawan dengan pilihan periode

## Format Export

### Sheet 1: Data Presensi Detail

Kolom yang disertakan:

-   No urut
-   NIP
-   Nama Karyawan
-   Jabatan
-   Departemen
-   Tanggal
-   Hari
-   Jam Masuk
-   Jam Pulang
-   Durasi Kerja (Jam:Menit)
-   Jam Kerja Efektif (Jam)
-   Status Presensi
-   Terlambat (Menit)
-   Keterangan Terlambat
-   Lokasi Masuk (GPS)
-   Lokasi Pulang (GPS)
-   Keterangan
-   Dicatat Pada

### Sheet 2: Ringkasan Presensi (Optional)

Kolom yang disertakan:

-   NIP
-   Nama Karyawan
-   Jabatan
-   Departemen
-   Total Presensi
-   Hadir
-   Terlambat
-   Tidak Hadir
-   Sakit
-   Izin
-   Cuti
-   Total Kehadiran
-   Persentase Kehadiran
-   Total Jam Kerja
-   Rata-rata Terlambat

## Filter yang Tersedia

### 1. Filter Periode

-   Tanggal mulai dan akhir
-   Quick select: Hari Ini, Kemarin, Minggu Ini, Minggu Lalu, Bulan Ini, Bulan Lalu, Tahun Ini

### 2. Filter Karyawan

-   Pilih karyawan spesifik (multiple selection)
-   Semua karyawan (default)

### 3. Filter Status

-   Multiple selection status presensi
-   Hadir, Terlambat, Tidak Hadir, Sakit, Izin, Cuti, Libur

### 4. Filter Departemen & Jabatan

-   Filter berdasarkan departemen
-   Filter berdasarkan jabatan

## Cara Penggunaan

### Export Cepat

1. Buka halaman daftar presensi
2. Klik tombol "Export Rekap Presensi" di header
3. Pilih periode dan filter yang diinginkan
4. Klik "Export" untuk mengunduh file

### Export Lanjutan

1. Buka halaman daftar presensi
2. Klik tombol "Export Lanjutan"
3. Atur semua pengaturan di halaman export:
    - Pilih periode
    - Atur filter data
    - Konfigurasikan pengaturan export
4. Klik "Preview Data" untuk melihat ringkasan
5. Klik "Export Sekarang" untuk mengunduh

### Export Individual

1. Di daftar presensi, klik icon "Export" pada row karyawan
2. Pilih periode untuk karyawan tersebut
3. Klik "Export" untuk mengunduh

## Format File

### Nama File

Format: `rekap_presensi_[periode]_[timestamp].xlsx`
Contoh: `rekap_presensi_01-01-2025_sampai_31-01-2025_2025-01-15_14-30-25.xlsx`

### Styling Excel

-   Header dengan background biru dan teks putih
-   Border pada semua cell
-   Kolom numeric di-center
-   Row total dengan background gelap
-   Auto-fit untuk tinggi row

## Performa dan Optimasi

### Tips untuk Performa Optimal

1. Hindari export periode yang terlalu lama (> 6 bulan)
2. Gunakan filter untuk membatasi data yang diekspor
3. Untuk data besar, gunakan export bertahap per departemen
4. Preview data terlebih dahulu untuk estimasi ukuran file

### Batasan

-   Maksimal 100,000 record per export
-   File size maksimal ~50MB untuk performa optimal
-   Timeout 5 menit untuk proses export

## Troubleshooting

### Error Memory Limit

-   Kurangi periode export
-   Gunakan filter yang lebih spesifik
-   Export per departemen/karyawan

### File Kosong

-   Periksa filter yang diterapkan
-   Pastikan ada data dalam periode yang dipilih
-   Cek permission karyawan

### Gagal Download

-   Clear browser cache
-   Coba dengan browser lain
-   Periksa koneksi internet

## Dependencies

-   `maatwebsite/excel` ^3.1
-   `phpoffice/phpspreadsheet` (auto-installed)
-   Laravel 10+
-   PHP 8.1+
