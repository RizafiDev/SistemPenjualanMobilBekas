<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi Karyawan - {{ $karyawan->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #3b82f6;
            --primary-light: #60a5fa;
            --primary-dark: #2563eb;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
        }

        body {
            font-family: 'Inter', sans-serif;
            /* background: linear-gradient(135deg, #f0f4f8 0%, #e5e9f0 100%); */
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        #map {
            height: 300px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .loader {
            border: 3px solid rgba(243, 244, 246, 0.3);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: none;
            margin-left: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        .status-hadir {
            background: rgba(220, 252, 231, 0.7);
            color: #166534;
        }

        .status-terlambat {
            background: rgba(254, 249, 195, 0.7);
            color: #854d0e;
        }

        .status-tidak_hadir {
            background: rgba(254, 226, 226, 0.7);
            color: #991b1b;
        }

        .status-izin {
            background: rgba(224, 242, 254, 0.7);
            color: #0369a1;
        }

        .status-sakit {
            background: rgba(243, 232, 255, 0.7);
            color: #6b21a8;
        }

        .status-cuti {
            background: rgba(224, 247, 250, 0.7);
            color: #0891b2;
        }

        .status-libur {
            background: rgba(241, 245, 249, 0.7);
            color: #475569;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #f87171 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, var(--danger) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, var(--success) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }

        #cameraFeed {
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            background-color: #000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #photoPreview {
            max-width: 100%;
            max-height: 250px;
            margin-top: 0.75rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.1) 50%, transparent 100%);
            margin: 1rem 0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0 0 0 / 60%);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .status-menunggu {
            background: rgba(254, 249, 195, 0.7);
            /* kuning muda */
            color: #b45309;
        }

        .status-disetujui {
            background: rgba(220, 252, 231, 0.7);
            /* hijau muda */
            color: #059669;
        }

        .status-ditolak {
            background: rgba(254, 226, 226, 0.7);
            /* merah muda */
            color: #b91c1c;
        }

        .jenis-tahunan {
            background: rgba(191, 219, 254, 0.7);
            /* biru muda */
            color: #2563eb;
        }

        .jenis-sakit {
            background: rgba(233, 213, 255, 0.7);
            /* ungu muda */
            color: #7c3aed;
        }

        .jenis-darurat {
            background: rgba(254, 215, 170, 0.7);
            /* oranye muda */
            color: #ea580c;
        }

        .jenis-lainnya {
            background: rgba(229, 231, 235, 0.7);
            /* abu muda */
            color: #374151;
        }
    </style>
</head>

<body class="antialiased">
    <!-- Glassmorphism Navbar -->
    <nav class="w-full glass-nav sticky top-0 z-50">
        <div class="max-w-8xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-white bg-opacity-80 shadow-sm">
                    <i class="fas fa-fingerprint text-blue-500 text-xl"></i>
                </div>
                <span class="font-bold text-xl text-slate-800 tracking-tight">Absensi Digital</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2 bg-white bg-opacity-70 px-3 py-2 rounded-lg shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user text-blue-500 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">
                        {{ $karyawan->nama_lengkap }}
                    </span>
                </div>
                <form method="POST" action="{{ route('karyawan.logout') }}">
                    @csrf
                    <button type="submit"
                        class="btn-danger text-white font-semibold px-4 py-2 rounded-lg transition duration-150 ease-in-out flex items-center gap-2 text-sm">
                        <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4 md:p-6 lg:p-8 max-w-8xl">
        <!-- Header -->
        <header class="mb-6 md:mb-8 glass-card p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Halo, {{ $karyawan->nama_lengkap }} 👋
                    </h1>
                    <p class="text-slate-500 text-sm">NIP: {{ $karyawan->nip ?? '-' }}</p>
                </div>
                <div class="flex items-center gap-3 bg-white bg-opacity-70 px-4 py-2 rounded-lg shadow-sm">
                    <div class="text-blue-500">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <div class="text-right">
                        <div id="currentDateTime" class="text-sm font-medium text-slate-700">Memuat tanggal...</div>
                        <div class="text-xs text-slate-500">Status:
                            <span id="statusKehadiran" class="font-medium">
                                {{ $presensiHariIni ? $presensiHariIni->status_label : 'Belum Absen' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Alert Container -->
        <div id="alertContainer" class="mb-6"></div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Left Column: Actions & Map -->
            <div class="lg:col-span-2 space-y-6 md:space-y-8">
                <!-- Attendance Card -->
                <div class="glass-card p-6">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        <span>Pencatatan Kehadiran</span>
                    </h2>

                    <!-- Map Section -->
                    <div id="map" class="mb-4"></div>
                    <div class="flex flex-wrap gap-4 mb-4">
                        <div class="flex-1 min-w-[200px] bg-white bg-opacity-70 p-3 rounded-lg shadow-sm">
                            <div class="text-xs text-slate-500 mb-1 flex items-center gap-1">
                                <i class="fas fa-map-marker-alt text-blue-500"></i> Lokasi Anda
                            </div>
                            <div id="currentLocation" class="text-sm font-medium text-slate-700 truncate">-</div>
                        </div>
                        @if ($pengaturanKantor && $pengaturanKantor->nama_kantor)
                            <div class="flex-1 min-w-[200px] bg-white bg-opacity-70 p-3 rounded-lg shadow-sm">
                                <div class="text-xs text-slate-500 mb-1 flex items-center gap-1">
                                    <i class="fas fa-building text-slate-500"></i> Kantor
                                </div>
                                <div class="text-sm font-medium text-slate-700">
                                    {{ $pengaturanKantor->nama_kantor }} ({{ $pengaturanKantor->radius_meter }}m)
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Camera Section -->
                    <div class="mb-4">
                        <div id="cameraContainer" class="mt-3" style="display: none;">
                            <div class="text-xs text-slate-500 mb-2">Ambil foto selfie untuk verifikasi:</div>
                            <video id="cameraFeed" autoplay playsinline muted></video>
                            <div id="photoActions" class="mt-3 space-x-2">
                                <button id="btnCapturePhoto"
                                    class="btn-success text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out text-sm">
                                    <i class="fas fa-camera-retro mr-2"></i> Ambil Gambar
                                </button>
                                <button id="btnRetakePhoto"
                                    class="btn-danger text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out text-sm"
                                    style="display: none;">
                                    <i class="fas fa-redo mr-2"></i> Ulangi
                                </button>
                            </div>
                        </div>
                        <img id="photoPreview" src="#" alt="Preview Foto Absen" class="hidden" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-6" id="absenUtamaWrapper">
                        <button id="btnAbsenUtama"
                            class="w-full flex-1 btn-primary hover:bg-green-600 disabled:bg-slate-300 text-white font-semibold py-3 px-5 rounded-lg transition duration-150 ease-in-out flex items-center justify-center hover:shadow-lg"
                            disabled>
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span id="btnAbsenUtamaText">Absen Masuk</span>
                            <span class="loader" id="loaderUtama"></span>
                        </button>
                    </div>
                    <div id="sudahAbsenInfo" class="mt-6 text-center text-green-700 font-semibold hidden">
                        <i class="fas fa-check-circle mr-2"></i>Anda sudah menyelesaikan absen hari ini.
                    </div>
                </div>

                <!-- Riwayat Presensi -->
                <div class="glass-card p-6">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i>
                        <span>Riwayat Presensi (7 Hari Terakhir)</span>
                    </h2>

                    @if($riwayatPresensi && $riwayatPresensi->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-white border-opacity-50">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-700 uppercase bg-white bg-opacity-70">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Tanggal</th>
                                        <th scope="col" class="px-4 py-3">Masuk</th>
                                        <th scope="col" class="px-4 py-3">Pulang</th>
                                        <th scope="col" class="px-4 py-3">Status</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white divide-opacity-20">
                                    @foreach($riwayatPresensi as $presensi)
                                        <tr class="hover:bg-white hover:bg-opacity-30 transition-colors">
                                            <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                                                {{ $presensi->tanggal->isoFormat('D MMM') }}
                                                <div class="text-xs text-slate-500">{{ $presensi->tanggal->isoFormat('dddd') }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-slate-700">
                                                {{ $presensi->jam_masuk ? $presensi->jam_masuk->format('H:i') : '-' }}
                                                @if($presensi->menit_terlambat > 0)
                                                    <div class="text-xs text-yellow-600">{{ $presensi->keterangan_terlambat }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-slate-700">
                                                {{ $presensi->jam_pulang ? $presensi->jam_pulang->format('H:i') : '-' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="status-badge status-{{ str_replace(' ', '_', strtolower($presensi->status)) }}">{{ $presensi->status_label }}</span>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-white bg-opacity-50 p-4 rounded-lg text-center text-slate-500">
                            <i class="fas fa-info-circle mr-2"></i> Tidak ada riwayat presensi dalam 7 hari terakhir.
                        </div>
                    @endif
                </div>

                <!-- pengajuan cuti -->
                <div class="glass-card p-6 mb-6 mt-6">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-plane text-blue-500"></i>
                        <span>Status Pengajuan Cuti</span>
                    </h2>
                    <!-- Tombol Ajukan Cuti di dalam card -->
                    <button onclick="toggleModalCuti(true)"
                        class="btn-primary px-4 py-2 rounded mb-4 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Ajukan Cuti
                    </button>
                    @if(isset($pengajuanCutiList) && count($pengajuanCutiList) > 0)
                        <div class="overflow-x-auto rounded-lg border border-white border-opacity-50">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-700 uppercase bg-white bg-opacity-70">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal Pengajuan</th>
                                        <th class="px-4 py-3">Jenis</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Jumlah Hari</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white divide-opacity-20">
                                    @foreach($pengajuanCutiList as $cuti)
                                        <tr>
                                            <td class="px-4 py-3">{{ $cuti->created_at->format('d M Y') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="status-badge jenis-{{ $cuti->jenis }}">
                                                    {{ $cuti->jenis_label }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">{{ $cuti->tanggal_mulai->format('d M') }} -
                                                {{ $cuti->tanggal_selesai->format('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3">{{ $cuti->jumlah_hari }} hari</td>
                                            <td class="px-4 py-3">
                                                <span class="status-badge status-{{ $cuti->status }}">
                                                    {{ $cuti->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-white bg-opacity-50 p-4 rounded-lg text-center text-slate-500">
                            <i class="fas fa-info-circle mr-2"></i> Belum ada pengajuan cuti.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Status & Stats -->
            <div class="lg:col-span-1 space-y-6 md:space-y-8">
                <!-- Today's Status -->
                <div class="glass-card p-6">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-blue-500"></i>
                        <span>Status Hari Ini</span>
                    </h2>

                    <div class="space-y-4">
                        <!-- Status Card -->
                        <div class="stat-card flex items-center">
                            <div class="stat-icon bg-blue-100 text-blue-500">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Status Kehadiran</div>
                                <div class="font-medium text-slate-700">
                                    <span id="statusKehadiran"
                                        class="status-badge {{ $presensiHariIni ? 'status-' . str_replace(' ', '_', strtolower($presensiHariIni->status)) : 'status-tidak_hadir' }}">
                                        {{ $presensiHariIni ? $presensiHariIni->status_label : 'Belum Absen' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Clock-in Card -->
                        <div class="stat-card flex items-center">
                            <div class="stat-icon bg-green-100 text-green-500">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Jam Masuk</div>
                                <div id="statusJamMasuk" class="font-medium text-slate-700">
                                    {{ $presensiHariIni && $presensiHariIni->jam_masuk ? $presensiHariIni->jam_masuk->format('H:i:s') : '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Clock-out Card -->
                        <div class="stat-card flex items-center">
                            <div class="stat-icon bg-red-100 text-red-500">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Jam Pulang</div>
                                <div id="statusJamPulang" class="font-medium text-slate-700">
                                    {{ $presensiHariIni && $presensiHariIni->jam_pulang ? $presensiHariIni->jam_pulang->format('H:i:s') : '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Work Hours Card -->

                    </div>

                    <!-- Photo Preview -->
                    <div class="mt-6 space-y-4">
                        @if($presensiHariIni && $presensiHariIni->foto_masuk_url)
                            <div>
                                <div class="text-sm font-medium text-slate-600 mb-2">Foto Masuk:</div>
                                <img id="imgFotoMasuk" src="{{ $presensiHariIni->foto_masuk_url }}" alt="Foto Masuk"
                                    class="rounded-lg border border-white border-opacity-50 w-full max-h-40 object-cover">
                            </div>
                        @else
                            <div id="imgFotoMasukContainer" class="hidden">
                                <div class="text-sm font-medium text-slate-600 mb-2">Foto Masuk:</div>
                                <img id="imgFotoMasuk" src="#" alt="Foto Masuk"
                                    class="rounded-lg border border-white border-opacity-50 w-full max-h-40 object-cover">
                            </div>
                        @endif

                        @if($presensiHariIni && $presensiHariIni->foto_pulang_url)
                            <div>
                                <div class="text-sm font-medium text-slate-600 mb-2">Foto Pulang:</div>
                                <img id="imgFotoPulang" src="{{ $presensiHariIni->foto_pulang_url }}" alt="Foto Pulang"
                                    class="rounded-lg border border-white border-opacity-50 w-full max-h-40 object-cover">
                            </div>
                        @else
                            <div id="imgFotoPulangContainer" class="hidden">
                                <div class="text-sm font-medium text-slate-600 mb-2">Foto Pulang:</div>
                                <img id="imgFotoPulang" src="#" alt="Foto Pulang"
                                    class="rounded-lg border border-white border-opacity-50 w-full max-h-40 object-cover">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Monthly Stats -->
                <div class="glass-card p-6">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-blue-500"></i>
                        <span>Statistik Bulan Ini</span>
                    </h2>

                    @if($statistikBulan)
                        <div class="grid grid-cols-2 gap-3">
                            <div class="stat-card">
                                <div class="text-xs text-slate-500">Hadir</div>
                                <div class="text-2xl font-bold text-green-500">{{ $statistikBulan['hadir'] ?? 0 }}</div>
                            </div>

                            <div class="stat-card">
                                <div class="text-xs text-slate-500">Terlambat</div>
                                <div class="text-2xl font-bold text-yellow-500">{{ $statistikBulan['terlambat'] ?? 0 }}
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="text-xs text-slate-500">Tidak Hadir</div>
                                <div class="text-2xl font-bold text-red-500">{{ $statistikBulan['tidak_hadir'] ?? 0 }}</div>
                            </div>

                            <div class="stat-card">
                                <div class="text-xs text-slate-500">Izin</div>
                                <div class="text-2xl font-bold text-blue-500">{{ $statistikBulan['izin'] ?? 0 }}</div>
                            </div>

                            <div class="stat-card">
                                <div class="text-xs text-slate-500">Sakit</div>
                                <div class="text-2xl font-bold text-purple-500">{{ $statistikBulan['sakit'] ?? 0 }}</div>
                            </div>

                            <div class="stat-card">
                                <div class="text-xs text-slate-500">Cuti</div>
                                <div class="text-2xl font-bold text-cyan-500">{{ $statistikBulan['cuti'] ?? 0 }}</div>
                            </div>
                        </div>


                    @else
                        <div class="bg-white bg-opacity-50 p-4 rounded-lg text-center text-slate-500">
                            <i class="fas fa-info-circle mr-2"></i> Data statistik belum tersedia.
                        </div>
                    @endif
                </div>

                <!-- Status Pengajuan Cuti -->

            </div>
        </div>
    </div>

    <!-- Modal Pengajuan Cuti -->
    <div id="modalCuti" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <button onclick="toggleModalCuti(false)"
                class="absolute top-2 right-2 text-slate-400 hover:text-red-500 text-xl">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-bold mb-4 text-blue-600 flex items-center gap-2">
                <i class="fas fa-plane-departure"></i> Pengajuan Cuti
            </h3>
            <form id="formCuti" method="POST" action="{{ route('karyawan.cuti.ajukan') }}">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Jenis Cuti</label>
                    <select name="jenis" class="w-full border rounded px-3 py-2" required>
                        <option value="">Pilih Jenis</option>
                        <option value="tahunan">Cuti Tahunan</option>
                        <option value="sakit">Cuti Sakit</option>
                        <option value="darurat">Cuti Darurat</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-3 flex gap-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Alasan</label>
                    <textarea name="alasan" class="w-full border rounded px-3 py-2" rows="2" required></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="toggleModalCuti(false)"
                        class="px-4 py-2 rounded bg-slate-200 hover:bg-slate-300">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Ajukan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Keterangan Pulang Cepat -->
    <div id="modalPulangCepat"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <h3 class="text-lg font-bold mb-4 text-yellow-600 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Konfirmasi Pulang Lebih Awal
            </h3>
            <p class="text-sm text-slate-600 mb-4">Anda akan melakukan absen pulang sebelum jam kerja berakhir. Mohon
                berikan alasan Anda.</p>
            <form id="formPulangCepat">
                <div class="mb-3">
                    <label for="keteranganPulang" class="block text-sm font-medium mb-1">Alasan Pulang Cepat</label>
                    <textarea id="keteranganPulang" name="keterangan" class="w-full border rounded px-3 py-2" rows="3"
                        required placeholder="Contoh: Ada urusan keluarga mendadak"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="toggleModalPulangCepat(false)"
                        class="px-4 py-2 rounded bg-slate-200 hover:bg-slate-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-600">Kirim &
                        Absen Pulang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- VARIABEL GLOBAL ---
        let map, userMarker, officeMarker, officeRadiusCircle;
        let currentLatitude, currentLongitude;
        const officeLat = {{ ($pengaturanKantor && $pengaturanKantor->latitude) ? $pengaturanKantor->latitude : 'null' }};
        const officeLng = {{ ($pengaturanKantor && $pengaturanKantor->longitude) ? $pengaturanKantor->longitude : 'null' }};
        const officeRadius = {{ ($pengaturanKantor && $pengaturanKantor->radius_meter) ? $pengaturanKantor->radius_meter : 'null' }};
        const officePulang = '{{ ($pengaturanKantor && $pengaturanKantor->jam_pulang) ? $pengaturanKantor->jam_pulang->format("H:i:s") : null }}';

        let cameraStream = null;
        let capturedImageBlob = null;
        let modeAbsen = 'masuk'; // Default mode
        let keteranganPulangCepat = '';

        const initialPresensiHariIni = {!! json_encode($presensiHariIni) !!};

        // --- EVENT LISTENER UTAMA ---
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil semua elemen DOM yang dibutuhkan
            const cameraFeed = document.getElementById('cameraFeed');
            const photoPreview = document.getElementById('photoPreview');
            const cameraContainer = document.getElementById('cameraContainer');
            const btnCapturePhoto = document.getElementById('btnCapturePhoto');
            const btnRetakePhoto = document.getElementById('btnRetakePhoto');
            const btnAbsenUtama = document.getElementById('btnAbsenUtama');
            const formPulangCepat = document.getElementById('formPulangCepat');

            // Inisialisasi halaman
            updateDateTime();
            setInterval(updateDateTime, 1000);
            initMap();
            getLocation();
            updateAbsenUtamaButton();

            // Tambahkan event listener dengan aman (cek jika elemen ada)
            if (btnCapturePhoto) {
                btnCapturePhoto.addEventListener('click', capturePhoto);
            }

            if (btnRetakePhoto) {
                btnRetakePhoto.addEventListener('click', () => {
                    photoPreview.src = '#';
                    photoPreview.classList.add('hidden');
                    capturedImageBlob = null;
                    startCamera();
                });
            }

            if (btnAbsenUtama) {
                btnAbsenUtama.addEventListener('click', async function () {
                    if (modeAbsen === 'pulang' && officePulang) {
                        const now = new Date();
                        const [hours, minutes, seconds] = officePulang.split(':');
                        const officePulangTime = new Date();
                        officePulangTime.setHours(hours, minutes, seconds, 0);

                        if (now < officePulangTime) {
                            toggleModalPulangCepat(true);
                            return;
                        }
                    }

                    // Untuk 'masuk' atau 'pulang' normal, mulai kamera jika belum
                    if (!cameraStream && !capturedImageBlob) {
                        await startCamera();
                    } else if (capturedImageBlob) {
                        prosesAbsenUtama();
                    } else {
                        showAlert('Silakan ambil foto selfie Anda terlebih dahulu.', 'info');
                    }
                });
            }

            if (formPulangCepat) {
                formPulangCepat.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const keteranganInput = document.getElementById('keteranganPulang');
                    if (!keteranganInput.value.trim()) {
                        showAlert('Alasan tidak boleh kosong.', 'warning');
                        return;
                    }
                    keteranganPulangCepat = keteranganInput.value;
                    toggleModalPulangCepat(false);
                    keteranganInput.value = ''; // Kosongkan field

                    // Lanjutkan alur kamera
                    if (!cameraStream && !capturedImageBlob) {
                        await startCamera();
                    } else if (capturedImageBlob) {
                        prosesAbsenUtama();
                    } else {
                        showAlert('Silakan ambil foto selfie Anda terlebih dahulu.', 'info');
                    }
                });
            }
        });

        // --- FUNGSI-FUNGSI ---

        function updateAbsenUtamaButton() {
            const absenUtamaWrapper = document.getElementById('absenUtamaWrapper');
            const sudahAbsenInfo = document.getElementById('sudahAbsenInfo');
            const btnAbsenUtama = document.getElementById('btnAbsenUtama');
            const btnAbsenUtamaText = document.getElementById('btnAbsenUtamaText');

            if (!absenUtamaWrapper || !sudahAbsenInfo || !btnAbsenUtama || !btnAbsenUtamaText) return;

            if (!initialPresensiHariIni || !initialPresensiHariIni.jam_masuk) {
                modeAbsen = 'masuk';
                btnAbsenUtamaText.textContent = 'Absen Masuk';
                btnAbsenUtama.classList.remove('btn-danger');
                btnAbsenUtama.classList.add('btn-primary');
                absenUtamaWrapper.style.display = 'flex';
                sudahAbsenInfo.style.display = 'none';
            } else if (initialPresensiHariIni.jam_masuk && !initialPresensiHariIni.jam_pulang) {
                modeAbsen = 'pulang';
                btnAbsenUtamaText.textContent = 'Absen Pulang';
                btnAbsenUtama.classList.remove('btn-primary');
                btnAbsenUtama.classList.add('btn-danger');
                absenUtamaWrapper.style.display = 'flex';
                sudahAbsenInfo.style.display = 'none';
            } else {
                absenUtamaWrapper.style.display = 'none';
                sudahAbsenInfo.style.display = 'block';
            }

            // Aktifkan tombol jika lokasi sudah didapat
            if (currentLatitude && currentLongitude && absenUtamaWrapper.style.display !== 'none') {
                btnAbsenUtama.disabled = false;
            }
        }

        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const dateTimeEl = document.getElementById('currentDateTime');
            if (dateTimeEl) {
                dateTimeEl.textContent = now.toLocaleDateString('id-ID', options);
            }
        }

        function initMap() {
            const defaultLat = officeLat || -6.200000;
            const defaultLng = officeLng || 106.816666;
            map = L.map('map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            if (officeLat && officeLng) {
                officeMarker = L.marker([officeLat, officeLng], {
                    icon: L.divIcon({
                        className: 'office-marker',
                        html: `<div class="relative">
                            <div class="absolute w-8 h-8 bg-blue-500 rounded-full opacity-80 animate-ping"></div>
                            <div class="relative w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-building text-xs"></i>
                            </div>
                        </div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    })
                }).addTo(map)
                    .bindPopup("Lokasi Kantor: {{ $pengaturanKantor->nama_kantor ?? 'Kantor Pusat' }}");

                if (officeRadius) {
                    officeRadiusCircle = L.circle([officeLat, officeLng], {
                        color: '#3b82f6',
                        fillColor: '#60a5fa',
                        fillOpacity: 0.2,
                        radius: officeRadius
                    }).addTo(map);
                }
                map.setView([officeLat, officeLng], 15);
            }
        }

        function getLocation() {
            if (!navigator.geolocation) {
                const msg = "Geolocation tidak didukung oleh browser ini.";
                document.getElementById('currentLocation').textContent = msg;
                showAlert(msg, 'danger');
                return;
            }
            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        }

        function showPosition(position) {
            currentLatitude = position.coords.latitude;
            currentLongitude = position.coords.longitude;

            document.getElementById('currentLocation').textContent =
                `${currentLatitude.toFixed(6)}, ${currentLongitude.toFixed(6)} (Akurasi: ${Math.round(position.coords.accuracy)}m)`;

            if (userMarker) map.removeLayer(userMarker);

            userMarker = L.marker([currentLatitude, currentLongitude], {
                icon: L.divIcon({
                    className: 'user-marker',
                    html: `<div class="relative">
                        <div class="absolute w-8 h-8 bg-green-500 rounded-full opacity-80 animate-ping"></div>
                        <div class="relative w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                    </div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                })
            }).addTo(map).bindPopup("Lokasi Anda Saat Ini").openPopup();

            if (!officeLat || !officeLng) {
                map.setView([currentLatitude, currentLongitude], 16);
            } else {
                const bounds = L.latLngBounds([currentLatitude, currentLongitude], [officeLat, officeLng]);
                map.fitBounds(bounds.pad(0.3));
            }
            updateAbsenUtamaButton();
        }

        function showError(error) {
            let message = "Gagal mendapatkan lokasi: ";
            switch (error.code) {
                case error.PERMISSION_DENIED: message += "Izin lokasi ditolak."; break;
                case error.POSITION_UNAVAILABLE: message += "Informasi lokasi tidak tersedia."; break;
                case error.TIMEOUT: message += "Waktu permintaan lokasi habis."; break;
                default: message += "Kesalahan tidak diketahui.";
            }
            document.getElementById('currentLocation').textContent = message;
            showAlert(message, 'danger');
        }

        async function startCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' },
                    audio: false
                });
                const cameraFeed = document.getElementById('cameraFeed');
                const cameraContainer = document.getElementById('cameraContainer');
                const photoActions = document.getElementById('photoActions');
                const btnCapturePhoto = document.getElementById('btnCapturePhoto');
                const btnRetakePhoto = document.getElementById('btnRetakePhoto');

                cameraFeed.srcObject = cameraStream;
                cameraContainer.style.display = 'block';
                document.getElementById('photoPreview').classList.add('hidden');
                photoActions.style.display = 'flex';
                btnCapturePhoto.style.display = 'inline-flex';
                btnRetakePhoto.style.display = 'none';
                capturedImageBlob = null;
            } catch (err) {
                showAlert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.', 'danger');
            }
        }

        function capturePhoto() {
            const cameraFeed = document.getElementById('cameraFeed');
            const photoPreview = document.getElementById('photoPreview');
            const photoCanvas = document.createElement('canvas');

            if (!cameraStream || !cameraFeed.srcObject) {
                showAlert('Kamera belum siap.', 'warning');
                return;
            }
            const context = photoCanvas.getContext('2d');
            photoCanvas.width = cameraFeed.videoWidth;
            photoCanvas.height = cameraFeed.videoHeight;
            context.drawImage(cameraFeed, 0, 0, photoCanvas.width, photoCanvas.height);

            photoPreview.src = photoCanvas.toDataURL('image/jpeg');
            photoPreview.classList.remove('hidden');

            photoCanvas.toBlob(function (blob) {
                capturedImageBlob = blob;
                // Setelah foto diambil, langsung proses absen
                prosesAbsenUtama();
            }, 'image/jpeg', 0.9);

            stopCameraStreamOnly();
            document.getElementById('btnCapturePhoto').style.display = 'none';
            document.getElementById('btnRetakePhoto').style.display = 'inline-flex';
        }

        function stopCameraStreamOnly() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        async function prosesAbsenUtama() {
            const btnAbsenUtama = document.getElementById('btnAbsenUtama');
            const loaderUtama = document.getElementById('loaderUtama');

            if (!capturedImageBlob) {
                showAlert('Foto wajib diambil untuk absen.', 'danger');
                return;
            }
            if (!currentLatitude || !currentLongitude) {
                showAlert('Lokasi belum terdeteksi. Mohon tunggu.', 'danger');
                return;
            }

            loaderUtama.style.display = 'inline-block';
            btnAbsenUtama.disabled = true;

            const url = modeAbsen === 'masuk'
                ? '{{ route("karyawan.absensi.masuk") }}'
                : '{{ route("karyawan.absensi.pulang") }}';

            const formData = new FormData();
            formData.append('latitude', currentLatitude);
            formData.append('longitude', currentLongitude);
            formData.append('foto', capturedImageBlob, `absen_${modeAbsen}_${new Date().toISOString().split('T')[0]}.jpg`);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            if (modeAbsen === 'pulang' && keteranganPulangCepat) {
                formData.append('keterangan', keteranganPulangCepat);
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        background: 'rgba(255, 255, 255, 0.95)',
                        backdrop: `
                            rgba(59,130,246,0.1)
                            url("data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7")
                            left top
                            no-repeat
                        `
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan.',
                        icon: 'error',
                        background: 'rgba(255, 255, 255, 0.95)'
                    });
                    btnAbsenUtama.disabled = false;
                }
            } catch (error) {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Terjadi kesalahan jaringan atau server.',
                    icon: 'error',
                    background: 'rgba(255, 255, 255, 0.95)'
                });
                btnAbsenUtama.disabled = false;
            } finally {
                loaderUtama.style.display = 'none';
                keteranganPulangCepat = ''; // Reset keterangan
            }
        }

        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            if (!alertContainer) return;

            const alertId = 'alert-' + Date.now();
            const alertDiv = document.createElement('div');
            let bgColor, textColor, borderColor, iconClass;

            switch (type) {
                case 'success':
                    bgColor = 'bg-green-100';
                    textColor = 'text-green-700';
                    borderColor = 'border-green-400';
                    iconClass = 'fas fa-check-circle';
                    break;
                case 'danger':
                    bgColor = 'bg-red-100';
                    textColor = 'text-red-700';
                    borderColor = 'border-red-400';
                    iconClass = 'fas fa-times-circle';
                    break;
                case 'warning':
                    bgColor = 'bg-yellow-100';
                    textColor = 'text-yellow-700';
                    borderColor = 'border-yellow-400';
                    iconClass = 'fas fa-exclamation-triangle';
                    break;
                default:
                    bgColor = 'bg-blue-100';
                    textColor = 'text-blue-700';
                    borderColor = 'border-blue-400';
                    iconClass = 'fas fa-info-circle';
                    break;
            }

            alertDiv.id = alertId;
            alertDiv.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded-md shadow-md flex items-center backdrop-blur-sm bg-opacity-80`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `<i class="${iconClass} mr-3 text-xl"></i> <span class="font-medium">${message}</span>`;

            alertContainer.innerHTML = '';
            alertContainer.appendChild(alertDiv);

            setTimeout(() => {
                const currentAlert = document.getElementById(alertId);
                if (currentAlert) {
                    currentAlert.style.transition = 'opacity 0.5s ease-out';
                    currentAlert.style.opacity = '0';
                    setTimeout(() => currentAlert.remove(), 500);
                }
            }, 5000);
        }

        // Modal Cuti
        function toggleModalCuti(show = true) {
            document.getElementById('modalCuti').classList.toggle('hidden', !show);
        }

        // Modal Pulang Cepat
        function toggleModalPulangCepat(show = true) {
            document.getElementById('modalPulangCepat').classList.toggle('hidden', !show);
        }
    </script>
</body>

</html>