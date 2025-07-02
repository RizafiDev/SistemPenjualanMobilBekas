<div class="p-4 bg-gray-50 rounded-lg border">
    <h4 class="font-semibold text-gray-900 mb-2">Ringkasan Pembayaran</h4>
    <div class="space-y-1 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-600">Total Harga:</span>
            <span class="font-semibold">Rp {{ number_format($total_harga, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Total Pembayaran:</span>
            <span>Rp {{ number_format($total_pembayaran, 0, ',', '.') }}</span>
        </div>
        <hr class="my-2">
        <div class="flex justify-between">
            <span class="text-gray-600">Sisa Pembayaran:</span>
            <span class="font-semibold {{ $sisa_pembayaran > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}
            </span>
        </div>
        @if($sisa_pembayaran <= 0)
            <div class="text-center mt-2">
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ✓ LUNAS
                </span>
            </div>
        @endif
    </div>
</div>