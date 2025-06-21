<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function printInvoice(Penjualan $penjualan)
    {
        // Load relationships yang diperlukan
        // Menggunakan nested relationship untuk mengakses merek melalui mobil
        $penjualan->load([
            'stokMobil.mobil.merek',  // Mengakses merek melalui mobil
            'stokMobil.varian',       // Mengakses varian
            'pelanggan',
            'karyawan'
        ]);

        // Data untuk invoice
        $data = [
            'penjualan' => $penjualan,
            'company' => [
                'name' => config('app.name', 'Dealer Mobil XYZ'),
                'address' => 'Jl. Contoh No. 123, Surakarta',
                'phone' => '(0271) 123-4567',
                'email' => 'info@dealermobil.com',
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('invoices.penjualan', $data);
        $pdf->setPaper('A4', 'portrait');

        // Return PDF untuk download atau view
        return $pdf->stream("Invoice-{$penjualan->no_faktur}.pdf");
    }

    public function downloadInvoice(Penjualan $penjualan)
    {
        // Load relationships yang diperlukan
        $penjualan->load([
            'stokMobil.mobil.merek',  // Mengakses merek melalui mobil
            'stokMobil.varian',       // Mengakses varian
            'pelanggan',
            'karyawan'
        ]);

        $data = [
            'penjualan' => $penjualan,
            'company' => [
                'name' => config('app.name', 'Dealer Mobil XYZ'),
                'address' => 'Jl. Contoh No. 123, Surakarta',
                'phone' => '(0271) 123-4567',
                'email' => 'info@dealermobil.com',
            ]
        ];

        $pdf = Pdf::loadView('invoices.penjualan', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Invoice-{$penjualan->no_faktur}.pdf");
    }
}