<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header dengan informasi -->
        <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-document-arrow-down class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Export Rekap Presensi</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Export data presensi karyawan dalam format Excel atau CSV dengan berbagai pilihan filter dan
                        periode.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-users class="w-8 h-8 text-blue-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Karyawan</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Karyawan::count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-calendar-days class="w-8 h-8 text-green-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Presensi Hari Ini</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Presensi::whereDate('tanggal', today())->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-yellow-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Presensi Bulan Ini</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Presensi::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-clock class="w-8 h-8 text-red-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Terlambat Hari Ini</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Presensi::whereDate('tanggal', today())->where('status', \App\Models\Presensi::STATUS_TERLAMBAT)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Export -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <form wire:submit.prevent="export">
                    {{ $this->form }}
                </form>
            </div>
        </div>

        <!-- Tips dan Petunjuk -->
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-o-light-bulb class="w-5 h-5 text-amber-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">Tips Export</h3>
                    <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Gunakan periode yang tidak terlalu lama untuk performa yang optimal</li>
                            <li>Format Excel (.xlsx) menyediakan fitur lebih lengkap dibanding CSV</li>
                            <li>Data lokasi GPS membutuhkan ruang file yang lebih besar</li>
                            <li>Preview data terlebih dahulu untuk memastikan filter yang benar</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>