<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $penggajian->karyawan->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-4xl mx-auto bg-white shadow-sm border border-gray-200 my-8">
        
        <!-- Header -->
        <div class="bg-slate-900 text-white p-8">
            <div class="text-center">
                <h1 class="text-2xl font-semibold mb-2">PT. NAMA PERUSAHAAN</h1>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Jl. Alamat Perusahaan No. 123, Kota, Provinsi 12345<br>
                    Telp: (021) 1234-5678 | Email: info@perusahaan.com
                </p>
                <div class="mt-6 pt-4 border-t border-slate-700">
                    <h2 class="text-lg font-medium tracking-wide">SLIP GAJI KARYAWAN</h2>
                </div>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="p-8 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm font-medium text-gray-600">NIP</span>
                        <span class="text-sm text-gray-800">: {{ $penggajian->karyawan->nip ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm font-medium text-gray-600">Nama Karyawan</span>
                        <span class="text-sm text-gray-800">: {{ $penggajian->karyawan->nama_lengkap ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm font-medium text-gray-600">Jabatan</span>
                        <span class="text-sm text-gray-800">: {{ $penggajian->karyawan->jabatan ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm font-medium text-gray-600">Departemen</span>
                        <span class="text-sm text-gray-800">: {{ $penggajian->karyawan->departemen ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm font-medium text-gray-600">Periode Gaji</span>
                        <span class="text-sm text-gray-800">: {{ $penggajian->bulan_tahun }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm font-medium text-gray-600">Tanggal Gaji</span>
                        <span class="text-sm text-gray-800">: {{ $penggajian->tanggal_gaji->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-8 space-y-8">
            
            <!-- Earnings Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Pendapatan</h3>
                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komponen</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">Gaji Pokok</td>
                                <td class="px-6 py-4 text-sm font-medium text-right text-green-600 font-mono">
                                    Rp {{ number_format($penggajian->gaji_pokok, 2, ',', '.') }}
                                </td>
                            </tr>
                            @if($penggajian->tunjangan > 0)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">Tunjangan</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right text-green-600 font-mono">
                                        Rp {{ number_format($penggajian->tunjangan, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            @if($penggajian->bonus > 0)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">Bonus</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right text-green-600 font-mono">
                                        Rp {{ number_format($penggajian->bonus, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            @if($penggajian->lembur > 0)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">Lembur</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right text-green-600 font-mono">
                                        Rp {{ number_format($penggajian->lembur, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            @if($penggajian->insentif > 0)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">Insentif</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right text-green-600 font-mono">
                                        Rp {{ number_format($penggajian->insentif, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="bg-green-50 border-t-2 border-green-200">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">Total Pendapatan</td>
                                <td class="px-6 py-4 text-sm font-bold text-right text-green-700 font-mono">
                                    Rp {{ number_format($penggajian->total_pendapatan, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deductions Section -->
            @if($penggajian->total_potongan_real > 0)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Potongan</h3>
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komponen</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @if($penggajian->potongan_terlambat > 0)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Potongan Terlambat</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-red-600 font-mono">
                                            Rp {{ number_format($penggajian->potongan_terlambat, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                @if($penggajian->potongan_absensi > 0)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Potongan Absensi</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-red-600 font-mono">
                                            Rp {{ number_format($penggajian->potongan_absensi, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                @if($penggajian->potongan_lainnya > 0)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Potongan Lainnya</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-red-600 font-mono">
                                            Rp {{ number_format($penggajian->potongan_lainnya, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="bg-red-50 border-t-2 border-red-200">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">Total Potongan</td>
                                    <td class="px-6 py-4 text-sm font-bold text-right text-red-700 font-mono">
                                        Rp {{ number_format($penggajian->total_potongan_real, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Net Salary Card -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-8 text-white text-center shadow-lg">
                <p class="text-blue-100 text-sm font-medium mb-2">GAJI BERSIH</p>
                <p class="text-3xl font-bold font-mono">Rp {{ number_format($penggajian->gaji_bersih, 2, ',', '.') }}</p>
            </div>

            <!-- Summary Table -->
            <div class="border border-gray-300 rounded-lg overflow-hidden">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-200">
                        <tr class="bg-green-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">Total Pendapatan</td>
                            <td class="px-6 py-4 text-sm font-bold text-right text-green-700 font-mono">
                                Rp {{ number_format($penggajian->total_pendapatan, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="bg-red-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">Total Potongan</td>
                            <td class="px-6 py-4 text-sm font-bold text-right text-red-700 font-mono">
                                Rp {{ number_format($penggajian->total_potongan_real, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="bg-slate-900 text-white">
                            <td class="px-6 py-4 text-sm font-bold">GAJI BERSIH</td>
                            <td class="px-6 py-4 text-sm font-bold text-right font-mono">
                                Rp {{ number_format($penggajian->gaji_bersih, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notes -->
            @if($penggajian->catatan)
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-amber-800">Catatan:</p>
                            <p class="text-sm text-amber-700 mt-1">{{ $penggajian->catatan }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8">
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">Menyetujui,</p>
                    <div class="border-2 border-dashed border-gray-300 h-20 mb-4 rounded-lg bg-gray-50"></div>
                    <p class="text-sm font-medium text-gray-900">HRD</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">Menerima,</p>
                    <div class="border-2 border-dashed border-gray-300 h-20 mb-4 rounded-lg bg-gray-50"></div>
                    <p class="text-sm font-medium text-gray-900">{{ $penggajian->karyawan->nama_lengkap ?? 'Karyawan' }}</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>Dokumen ini dicetak secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
            <p>Status: {{ $penggajian->status }} | ID: {{ $penggajian->id }}</p>
        </div>
    </div>

    <style>
        @media print {
            body { background: white; }
            .shadow-sm, .shadow-lg { box-shadow: none !important; }
            .border { border-color: #000 !important; }
        }
    </style>
</body>

</html>