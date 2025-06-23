<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $penjualan->no_faktur }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans text-gray-800 text-sm leading-relaxed bg-white">
    <button
        class="fixed top-5 right-5 bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-blue-700 transition z-50 print:hidden"
        onclick="window.print()">🖨️ Print Invoice</button>

    <div class="max-w-3xl mx-auto p-5 bg-white">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8 pb-5 border-b-4 border-blue-600">
            <div>
                <h1 class="text-2xl font-bold text-blue-600 mb-2">{{ $company['name'] }}</h1>
                <p class="text-gray-600 mb-1">{{ $company['address'] }}</p>
                <p class="text-gray-600 mb-1">Telp: {{ $company['phone'] }}</p>
                <p class="text-gray-600 mb-1">Email: {{ $company['email'] }}</p>
                @if($company['website'])
                    <p class="text-gray-600">{{ $company['website'] }}</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-blue-600 mb-3">INVOICE</div>
                <div class="text-lg font-bold text-gray-800">#{{ $penjualan->no_faktur }}</div>
                <p class="text-xs text-gray-600 mt-1">Tanggal:
                    {{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y') }}</p>
                <p class="text-xs text-gray-600">Dicetak: {{ $printDate }}</p>
            </div>
        </div>

        <!-- Customer & Sales Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 my-8 p-5 bg-gray-50 rounded-lg">
            <div>
                <h3 class="text-sm font-bold text-blue-600 mb-3 pb-2 border-b border-gray-200">👤 INFORMASI PELANGGAN
                </h3>
                <p class="font-bold">{{ $penjualan->pelanggan->nama_lengkap }}</p>
                @if($penjualan->pelanggan->nik)
                    <p>NIK: {{ $penjualan->pelanggan->nik }}</p>
                @endif
                @if($penjualan->pelanggan->no_telepon)
                    <p>Telp: {{ $penjualan->pelanggan->no_telepon }}</p>
                @endif
                @if($penjualan->pelanggan->email)
                    <p>Email: {{ $penjualan->pelanggan->email }}</p>
                @endif
                @if($penjualan->pelanggan->alamat)
                    <p>Alamat: {{ $penjualan->pelanggan->alamat }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-bold text-blue-600 mb-3 pb-2 border-b border-gray-200">👨‍💼 SALES EXECUTIVE
                </h3>
                @if($penjualan->karyawan)
                    <p class="font-bold">{{ $penjualan->karyawan->nama_lengkap }}</p>
                    @if($penjualan->karyawan->no_telepon)
                        <p>Telp: {{ $penjualan->karyawan->no_telepon }}</p>
                    @endif
                @else
                    <p class="italic text-gray-600">Tidak ada sales</p>
                @endif
                <div class="mt-4">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase
                        @if($penjualan->status === 'lunas') bg-green-100 text-green-800
                        @elseif($penjualan->status === 'booking') bg-yellow-100 text-yellow-800
                        @elseif($penjualan->status === 'kredit') bg-blue-100 text-blue-800
                        @elseif($penjualan->status === 'draft') bg-gray-100 text-gray-600
                        @elseif($penjualan->status === 'batal') bg-red-100 text-red-800
                        @endif">
                        {{ \App\Models\Penjualan::STATUS[$penjualan->status] ?? $penjualan->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Vehicle Information -->
        <div class="my-8 p-5 bg-blue-50 border-l-4 border-blue-600 rounded-r-lg">
            <h3 class="text-base font-bold text-blue-600 mb-4">🚗 INFORMASI KENDARAAN</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex justify-between py-2 border-b border-dotted border-gray-300 last:border-none">
                    <span class="font-bold text-gray-600">Mobil:</span>
                    <span>{{ $penjualan->stokMobil->nama_lengkap_penjualan ?? 'N/A' }}</span>
                </div>
                @if($penjualan->stokMobil->warna)
                    <div class="flex justify-between py-2 border-b border-dotted border-gray-300 last:border-none">
                        <span class="font-bold text-gray-600">Warna:</span>
                        <span>{{ $penjualan->stokMobil->warna }}</span>
                    </div>
                @endif
                @if($penjualan->stokMobil->no_rangka)
                    <div class="flex justify-between py-2 border-b border-dotted border-gray-300 last:border-none">
                        <span class="font-bold text-gray-600">No. Rangka:</span>
                        <span>{{ $penjualan->stokMobil->no_rangka }}</span>
                    </div>
                @endif
                @if($penjualan->stokMobil->no_mesin)
                    <div class="flex justify-between py-2 border-b border-dotted border-gray-300 last:border-none">
                        <span class="font-bold text-gray-600">No. Mesin:</span>
                        <span>{{ $penjualan->stokMobil->no_mesin }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Price Breakdown -->
        <table class="w-full my-8 bg-white rounded-lg shadow-sm overflow-hidden">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="p-3 text-left font-bold text-sm">Deskripsi</th>
                    <th class="p-3 text-right font-bold text-sm">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-50">
                    <td class="p-3">Harga Kendaraan</td>
                    <td class="p-3 text-right font-bold">{{ number_format($penjualan->harga_jual, 0, ',', '.') }}</td>
                </tr>
                @if($penjualan->diskon > 0)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">Diskon</td>
                        <td class="p-3 text-right font-bold text-red-600">
                            -{{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($penjualan->biaya_tambahan > 0)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">Biaya Tambahan</td>
                        <td class="p-3 text-right font-bold">{{ number_format($penjualan->biaya_tambahan, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                @if($penjualan->ppn > 0)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">PPN (11%)</td>
                        <td class="p-3 text-right font-bold">{{ number_format($penjualan->ppn, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="bg-gray-100 font-bold text-sm">
                    <td class="p-3">Subtotal</td>
                    <td class="p-3 text-right">
                        {{ number_format($penjualan->harga_jual - $penjualan->diskon + $penjualan->biaya_tambahan + $penjualan->ppn, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-blue-600 text-white font-bold text-base">
                    <td class="p-3">TOTAL PEMBAYARAN</td>
                    <td class="p-3 text-right">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 my-8">
            <div class="p-5 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                <h3 class="text-sm font-bold mb-2">💳 Metode Pembayaran</h3>
                <p class="font-bold">
                    {{ \App\Models\Penjualan::METODE_PEMBAYARAN[$penjualan->metode_pembayaran] ?? $penjualan->metode_pembayaran }}
                </p>
                @if($penjualan->metode_pembayaran === 'kredit')
                    @if($penjualan->leasing_bank)
                        <p>Bank:
                            {{ \App\Models\Penjualan::LEASING_BANKS[$penjualan->leasing_bank] ?? $penjualan->leasing_bank }}</p>
                    @endif
                    @if($penjualan->tenor_bulan)
                        <p>Tenor: {{ $penjualan->tenor_bulan }} bulan</p>
                    @endif
                    @if($penjualan->uang_muka)
                        <p>Uang Muka: Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</p>
                    @endif
                    @if($penjualan->cicilan_bulanan)
                        <p>Cicilan: Rp {{ number_format($penjualan->cicilan_bulanan, 0, ',', '.') }}/bulan</p>
                    @endif
                @endif
            </div>
            <div class="p-5 bg-green-50 border-l-4 border-green-500 rounded-lg">
                <h3 class="text-sm font-bold mb-2">📊 Status Pembayaran</h3>
                <div class="mb-2">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase
                        @if($penjualan->status === 'lunas') bg-green-100 text-green-800
                        @elseif($penjualan->status === 'booking') bg-yellow-100 text-yellow-800
                        @elseif($penjualan->status === 'kredit') bg-blue-100 text-blue-800
                        @elseif($penjualan->status === 'draft') bg-gray-100 text-gray-600
                        @elseif($penjualan->status === 'batal') bg-red-100 text-red-800
                        @endif">
                        {{ \App\Models\Penjualan::STATUS[$penjualan->status] ?? $penjualan->status }}
                    </span>
                </div>
                @if($penjualan->metode_pembayaran === 'kredit' && $penjualan->uang_muka)
                    <p>Sisa Bayar: Rp {{ number_format($penjualan->total - $penjualan->uang_muka, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>

        <!-- Trade In Section -->
        @if(!empty($penjualan->trade_in) && is_array($penjualan->trade_in))
            <div class="my-8 p-5 bg-purple-50 border border-purple-200 rounded-lg">
                <h3 class="text-base font-bold mb-4">🔄 INFORMASI TRADE IN</h3>
                @foreach($penjualan->trade_in as $tradeIn)
                    <div class="bg-white p-4 mb-3 rounded-md border border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="font-bold">{{ $tradeIn['merk'] ?? '' }} {{ $tradeIn['model'] ?? '' }}
                                    ({{ $tradeIn['tahun'] ?? '' }})</p>
                                @if(!empty($tradeIn['no_rangka']))
                                    <p class="text-xs">No. Rangka: {{ $tradeIn['no_rangka'] }}</p>
                                @endif
                                <p class="text-xs">Kondisi: {{ $tradeIn['kondisi'] ?? '' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">Nilai Trade In:</p>
                                <p class="text-lg font-bold text-green-600">Rp
                                    {{ number_format($tradeIn['nilai_trade_in'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @if(!empty($tradeIn['catatan_trade_in']))
                            <p class="text-xs italic mt-3">Catatan: {{ $tradeIn['catatan_trade_in'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Notes -->
        @if($penjualan->catatan)
            <div class="my-8 p-5 bg-yellow-50 border-l-4 border-yellow-600 rounded-r-lg">
                <h3 class="text-sm font-bold mb-2">📝 Catatan</h3>
                <p>{{ $penjualan->catatan }}</p>
            </div>
        @endif

        <!-- Footer with Signatures -->
        <div class="mt-12 pt-5 border-t-2 border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="min-h-[100px] flex flex-col justify-between">
                <p class="font-bold">Sales Executive</p>
                <div class="mt-auto border-t pt-2 text-xs">
                    {{ $penjualan->karyawan->nama_lengkap ?? '________________' }}</div>
            </div>
            <div class="min-h-[100px] flex flex-col justify-between">
                <p class="font-bold">Driver</p>
                <div class="mt-auto border-t pt-2 text-xs">________________</div>
            </div>
            <div class="min-h-[100px] flex flex-col justify-between">
                <p class="font-bold">Pelanggan</p>
                <div class="mt-auto border-t pt-2 text-xs">{{ $penjualan->pelanggan->nama_lengkap }}</div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-center text-gray-500 text-xs">
            <p>Invoice ini dicetak secara otomatis oleh sistem pada {{ $printDate }}</p>
            <p>Untuk pertanyaan, hubungi {{ $company['phone'] }} atau {{ $company['email'] }}</p>
        </div>
    </div>

    <script>
        // Auto print jika bukan preview
        @if(!isset($isPreview) || !$isPreview)
            window.onload = function () {
                setTimeout(() => window.print(), 500);
            }
        @endif
    </script>
</body>

</html>