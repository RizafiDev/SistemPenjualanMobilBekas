<?php
// File: app/Http/Controllers/PenjualanController.php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    /**
     * Print invoice penjualan
     */
    public function print(Penjualan $penjualan)
    {
        // Load relasi yang diperlukan
        $penjualan->load([
            'stokMobil.mobil.merek',
            'stokMobil.varian',
            'pelanggan',
            'karyawan',
            'pembayarans'
        ]);

        // Data untuk invoice
        $data = [
            'penjualan' => $penjualan,
            'company' => $this->getCompanyInfo(),
            'printDate' => now()->format('d/m/Y H:i:s'),
        ];

        return view('print.invoice', $data);
    }

    /**
     * Preview invoice sebelum print
     */
    public function preview(Penjualan $penjualan)
    {
        $penjualan->load([
            'stokMobil.mobil.merek',
            'stokMobil.varian',
            'pelanggan',
            'karyawan',
            'pembayarans'
        ]);

        $data = [
            'penjualan' => $penjualan,
            'company' => $this->getCompanyInfo(),
            'printDate' => now()->format('d/m/Y H:i:s'),
            'isPreview' => true,
        ];

        return view('print.invoice', $data);
    }

    /**
     * Download invoice sebagai PDF
     */
    public function downloadPdf(Penjualan $penjualan)
    {
        $penjualan->load([
            'stokMobil.mobil.merek',
            'stokMobil.varian',
            'pelanggan',
            'karyawan',
            'pembayarans'
        ]);

        $data = [
            'penjualan' => $penjualan,
            'company' => $this->getCompanyInfo(),
            'printDate' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('print.invoice-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'invoice-' . $penjualan->no_faktur . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get company information
     */
    private function getCompanyInfo(): array
    {
        return [
            'name' => config('app.company_name', 'AUTO DEALER SYSTEM'),
            'address' => config('app.company_address', 'Jl. Raya No. 123, Kota, Provinsi'),
            'phone' => config('app.company_phone', '(021) 1234-5678'),
            'email' => config('app.company_email', 'info@autodealer.com'),
            'website' => config('app.company_website', 'www.autodealer.com'),
            'logo' => config('app.company_logo', null),
        ];
    }

    /**
     * Helper untuk format currency
     */
    public function formatCurrency($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Helper untuk format tanggal Indonesia
     */
    public function formatTanggalIndonesia($date): string
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $tanggal = date('d', strtotime($date));
        $bulanNama = $bulan[date('n', strtotime($date))];
        $tahun = date('Y', strtotime($date));

        return "$tanggal $bulanNama $tahun";
    }
}