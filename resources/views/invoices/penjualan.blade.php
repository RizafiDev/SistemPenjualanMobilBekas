<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $penjualan->no_faktur }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .company-info h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .company-info p {
            margin: 2px 0;
            color: #666;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .invoice-info p {
            margin: 2px 0;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .customer-info,
        .sales-info {
            width: 48%;
        }

        .customer-info h3,
        .sales-info h3 {
            color: #007bff;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .customer-info p,
        .sales-info p {
            margin: 3px 0;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: left;
        }

        .product-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .product-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .product-table .text-right {
            text-align: right;
        }

        .product-table .text-center {
            text-align: center;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .totals .total-label {
            font-weight: bold;
            text-align: right;
            width: 60%;
        }

        .totals .total-amount {
            text-align: right;
            width: 40%;
        }

        .totals .grand-total {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .payment-info h3 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .payment-details {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .payment-details>div {
            width: 48%;
            margin-bottom: 10px;
        }

        .trade-in-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        .trade-in-section h3 {
            color: #856404;
            margin-bottom: 10px;
        }

        .trade-in-item {
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .notes h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #6c757d;
            color: white;
        }

        .status-booking {
            background-color: #ffc107;
            color: #212529;
        }

        .status-lunas {
            background-color: #28a745;
            color: white;
        }

        .status-kredit {
            background-color: #17a2b8;
            color: white;
        }

        .status-batal {
            background-color: #dc3545;
            color: white;
        }

        @media print {
            .invoice-container {
                padding: 10px;
            }

            body {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $company['name'] }}</h1>
                <p>{{ $company['address'] }}</p>
                <p>Telepon: {{ $company['phone'] }}</p>
                <p>Email: {{ $company['email'] }}</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>No. Faktur:</strong> {{ $penjualan->no_faktur }}</p>
                <p><strong>Tanggal:</strong> {{ $penjualan->tanggal_penjualan->format('d F Y') }}</p>
                <p><strong>Status:</strong>
                    <span class="status-badge status-{{ $penjualan->status }}">
                        {{ $penjualan->status_label }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Customer and Sales Info -->
        <div class="invoice-details">
            <div class="customer-info">
                <h3>INFORMASI PELANGGAN</h3>
                <p><strong>Nama:</strong> {{ $penjualan->pelanggan->nama_lengkap }}</p>
                @if($penjualan->pelanggan->nik)
                    <p><strong>NIK:</strong> {{ $penjualan->pelanggan->nik }}</p>
                @endif
                @if($penjualan->pelanggan->no_telepon)
                    <p><strong>Telepon:</strong> {{ $penjualan->pelanggan->no_telepon }}</p>
                @endif
                @if($penjualan->pelanggan->email)
                    <p><strong>Email:</strong> {{ $penjualan->pelanggan->email }}</p>
                @endif
                @if($penjualan->pelanggan->alamat)
                    <p><strong>Alamat:</strong> {{ $penjualan->pelanggan->alamat }}</p>
                @endif
            </div>

            <div class="sales-info">
                <h3>INFORMASI SALES</h3>
                @if($penjualan->karyawan)
                    <p><strong>Nama Sales:</strong> {{ $penjualan->karyawan->nama_lengkap }}</p>
                    @if($penjualan->karyawan->no_telepon)
                        <p><strong>Telepon:</strong> {{ $penjualan->karyawan->no_telepon }}</p>
                    @endif
                @else
                    <p>Tidak ada sales yang ditugaskan</p>
                @endif
            </div>
        </div>

        <!-- Product Table -->
        <table class="product-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $penjualan->stokMobil->nama_lengkap }}</strong><br>
                        <small>
                            No. Rangka: {{ $penjualan->stokMobil->no_rangka }}<br>
                            No. Mesin: {{ $penjualan->stokMobil->no_mesin }}<br>
                            Tahun: {{ $penjualan->stokMobil->tahun }}<br>
                            Warna: {{ $penjualan->stokMobil->warna }}
                        </small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($penjualan->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($penjualan->harga_jual, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-amount">Rp {{ number_format($penjualan->harga_jual, 0, ',', '.') }}</td>
                </tr>
                @if($penjualan->diskon > 0)
                    <tr>
                        <td class="total-label">Diskon:</td>
                        <td class="total-amount">- Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($penjualan->ppn > 0)
                    <tr>
                        <td class="total-label">PPN (11%):</td>
                        <td class="total-amount">Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($penjualan->biaya_tambahan > 0)
                    <tr>
                        <td class="total-label">Biaya Tambahan:</td>
                        <td class="total-amount">Rp {{ number_format($penjualan->biaya_tambahan, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td class="total-label">TOTAL:</td>
                    <td class="total-amount">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <h3>INFORMASI PEMBAYARAN</h3>
            <div class="payment-details">
                <div>
                    <strong>Metode Pembayaran:</strong> {{ $penjualan->metode_pembayaran_label }}
                </div>

                @if(in_array($penjualan->metode_pembayaran, ['leasing', 'kredit']))
                    @if($penjualan->leasing_bank)
                        <div>
                            <strong>Bank/Leasing:</strong> {{ $penjualan->leasing_bank_label }}
                        </div>
                    @endif
                    @if($penjualan->tenor_bulan)
                        <div>
                            <strong>Tenor:</strong> {{ $penjualan->tenor_bulan }} bulan
                        </div>
                    @endif
                    @if($penjualan->uang_muka)
                        <div>
                            <strong>Uang Muka:</strong> Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}
                        </div>
                    @endif
                    @if($penjualan->cicilan_bulanan)
                        <div>
                            <strong>Cicilan Bulanan:</strong> Rp {{ number_format($penjualan->cicilan_bulanan, 0, ',', '.') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Trade In Section -->
        @if($penjualan->metode_pembayaran === 'trade_in' && !empty($penjualan->trade_in))
            <div class="trade-in-section">
                <h3>INFORMASI TRADE IN</h3>
                @foreach($penjualan->trade_in as $tradeIn)
                    <div class="trade-in-item">
                        <p><strong>{{ $tradeIn['merk'] ?? '' }} {{ $tradeIn['model'] ?? '' }}
                                ({{ $tradeIn['tahun'] ?? '' }})</strong></p>
                        @if(!empty($tradeIn['no_rangka']))
                            <p>No. Rangka: {{ $tradeIn['no_rangka'] }}</p>
                        @endif
                        <p>Kondisi: {{ $tradeIn['kondisi'] ?? '' }}</p>
                        <p>Nilai Trade In: Rp {{ number_format($tradeIn['nilai_trade_in'] ?? 0, 0, ',', '.') }}</p>
                        @if(!empty($tradeIn['catatan_trade_in']))
                            <p>Catatan: {{ $tradeIn['catatan_trade_in'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Notes -->
        @if($penjualan->catatan)
            <div class="notes">
                <h3>CATATAN</h3>
                <p>{{ $penjualan->catatan }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda. Dokumen ini dibuat otomatis oleh sistem.</p>
            <p>{{ $company['name'] }} - {{ $company['address'] }}</p>
        </div>
    </div>
</body>

</html>