<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Kalender Presensi - {{ $this->getViewData()['currentMonth']->format('F Y') }}
                </h3>
                <div class="flex space-x-2">
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Hadir</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-yellow-500 rounded"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Terlambat</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-red-500 rounded"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Tidak Hadir</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-1 mb-2">
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Min</div>
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Sen</div>
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Sel</div>
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Rab</div>
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Kam</div>
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Jum</div>
                <div class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Sab</div>
            </div>

            <div class="grid grid-cols-7 gap-1">
                @php
                    $data = $this->getViewData();
                    $days = $data['days'];
                    $totalKaryawan = $data['totalKaryawan'];
                    $firstDay = $days->first()['date'];
                    $startPadding = $firstDay->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                @endphp

                {{-- Padding untuk hari sebelum tanggal 1 --}}
                @for ($i = 0; $i < $startPadding; $i++)
                    <div class="p-2 h-20"></div>
                @endfor

                @foreach ($days as $dayData)
                    @php
                        $date = $dayData['date'];
                        $presensi = $dayData['presensi'];
                        $hadir = $presensi->get('hadir', 0);
                        $terlambat = $presensi->get('terlambat', 0);
                        $tidakHadir = $presensi->get('tidak_hadir', 0);
                        $totalPresensi = $hadir + $terlambat + $tidakHadir;
                        $persentaseKehadiran = $totalKaryawan > 0 ? (($hadir + $terlambat) / $totalKaryawan) * 100 : 0;
                    @endphp

                    <div class="p-2 h-20 border border-gray-200 dark:border-gray-700 rounded-lg relative
                                {{ $dayData['isToday'] ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300' : '' }}
                                {{ $dayData['isWeekend'] ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }}
                                hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer"
                        wire:click="$dispatch('open-presensi-detail', { date: '{{ $date->format('Y-m-d') }}' })">

                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                            {{ $date->day }}
                        </div>

                        @if ($totalPresensi > 0)
                            <div class="space-y-1">
                                @if ($hadir > 0)
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $hadir }}</span>
                                    </div>
                                @endif

                                @if ($terlambat > 0)
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $terlambat }}</span>
                                    </div>
                                @endif

                                @if ($tidakHadir > 0)
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $tidakHadir }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Progress bar kehadiran --}}
                            <div class="absolute bottom-1 left-1 right-1">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                    <div class="h-1 rounded-full transition-all duration-300
                                                        {{ $persentaseKehadiran >= 85 ? 'bg-green-500' : ($persentaseKehadiran >= 70 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                        style="width: {{ min($persentaseKehadiran, 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        @if ($dayData['isToday'])
                            <div class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                * Progress bar menunjukkan persentase kehadiran per hari
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>