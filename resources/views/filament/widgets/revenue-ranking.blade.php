<x-filament-widgets::widget>
    <x-filament::section heading="🥇 Bảng xếp hạng doanh thu" description="Top 5 khách sạn">
        <div class="space-y-3">
            @forelse($hotels as $i => $hotel)
            @php
                $medals = ['🥇','🥈','🥉','4️⃣','5️⃣'];
                $colors = ['text-yellow-500','text-gray-400','text-orange-400','text-gray-500','text-gray-500'];
            @endphp
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 dark:border-gray-800">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="text-xl flex-shrink-0">{{ $medals[$i] ?? ($i+1) }}</span>
                    <div class="min-w-0">
                        <div class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $hotel->name }}</div>
                        <div class="text-xs text-gray-500">{{ $hotel->booking_count }} đơn</div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <div class="font-bold text-sm text-emerald-600">{{ number_format($hotel->total_revenue, 0, ',', '.') }}đ</div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Chưa có dữ liệu</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
