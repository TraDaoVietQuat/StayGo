<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">📋 Đặt phòng gần đây</h3>
            <a href="{{ route('filament.admin.resources.bookings.index') }}"
               class="text-sm text-primary-600 hover:underline font-medium">Xem tất cả →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã đơn</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách sạn</th>
                        <th class="text-right py-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($bookings as $booking)
                    @php
                        $statusMap = [
                            'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'bg-yellow-100 text-yellow-800'],
                            'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'bg-blue-100 text-blue-800'],
                            'completed' => ['label' => 'Hoàn thành',   'class' => 'bg-green-100 text-green-800'],
                            'cancelled' => ['label' => 'Đã hủy',       'class' => 'bg-red-100 text-red-800'],
                        ];
                        $s = $statusMap[$booking->status] ?? ['label' => $booking->status, 'class' => 'bg-gray-100 text-gray-700'];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2 px-3">
                            <a href="{{ route('filament.admin.resources.bookings.edit', $booking) }}"
                               class="text-primary-600 hover:underline font-mono text-xs">{{ $booking->order_code }}</a>
                        </td>
                        <td class="py-2 px-3 font-medium text-gray-900 dark:text-white">{{ $booking->full_name }}</td>
                        <td class="py-2 px-3 text-gray-600 dark:text-gray-400">
                            {{ $booking->room?->hotel?->name ?? '—' }}
                        </td>
                        <td class="py-2 px-3 text-right font-semibold text-emerald-600">
                            {{ number_format($booking->total_price, 0, ',', '.') }}đ
                        </td>
                        <td class="py-2 px-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ $booking->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $s['class'] }}">
                                {{ $s['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Chưa có đơn đặt phòng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
