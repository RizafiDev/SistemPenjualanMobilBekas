<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Get company information from config
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

    public function printInvoice(Penjualan $penjualan)
    {
        // Load relationships yang diperlukan
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

        // Return view untuk print
        return view('invoices.penjualan', $data);
    }

    public function previewInvoice(Penjualan $penjualan)
    {
        // Load relationships yang diperlukan
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
            'isPreview' => true,
        ];

        // Return view untuk preview
        return view('invoices.penjualan', $data);
    }

    public function downloadInvoice(Penjualan $penjualan)
    {
        // Load relationships yang diperlukan
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

        // Generate PDF
        $pdf = Pdf::loadView('invoices.penjualan', $data);
        $pdf->setPaper('A4', 'portrait');

        // Return PDF untuk download
        return $pdf->download("Invoice-{$penjualan->no_faktur}.pdf");
    }
}