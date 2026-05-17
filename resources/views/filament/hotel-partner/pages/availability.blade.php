<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Room selector + month nav --}}
        <div class="flex flex-wrap items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Chọn phòng</label>
                <select wire:model.live="selectedRoomId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                    @foreach($this->getRooms() as $room)
                        <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <button wire:click="prevMonth"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800">
                    ← Tháng trước
                </button>
                <span class="font-semibold text-gray-800 dark:text-gray-100 px-2">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $viewMonth)->format('m/Y') }}
                </span>
                <button wire:click="nextMonth"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800">
                    Tháng sau →
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex gap-4 text-xs text-gray-600 dark:text-gray-400">
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-white border border-gray-300"></span> Còn phòng</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-blue-500"></span> Đã có đặt phòng</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-red-400"></span> Đã khóa</span>
            <span class="text-gray-400 italic">Click vào ngày để khóa/mở khóa (chỉ khi chưa có đặt phòng)</span>
        </div>

        {{-- Calendar grid --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 overflow-hidden">
            {{-- Day headers --}}
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                @foreach(['T2','T3','T4','T5','T6','T7','CN'] as $day)
                    <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $day }}</div>
                @endforeach
            </div>

            {{-- Days --}}
            @php
                $calData = $this->getCalendarData();
                $firstDay = !empty($calData) ? \Carbon\Carbon::parse($calData[0]['date'])->dayOfWeekIso : 1;
                $offset = $firstDay - 1;
            @endphp

            <div class="grid grid-cols-7">
                {{-- Empty cells before first day --}}
                @for($i = 0; $i < $offset; $i++)
                    <div class="min-h-16 border-b border-r border-gray-100 dark:border-gray-800"></div>
                @endfor

                @foreach($calData as $day)
                    @php
                        $isPast   = \Carbon\Carbon::parse($day['date'])->isPast() && $day['date'] !== now()->toDateString();
                        $isToday  = $day['date'] === now()->toDateString();
                        $bgClass  = match(true) {
                            $day['blocked'] => 'bg-red-50 dark:bg-red-950',
                            $day['booked']  => 'bg-blue-50 dark:bg-blue-950',
                            $isPast         => 'bg-gray-50 dark:bg-gray-800 opacity-60',
                            $isToday        => 'bg-yellow-50 dark:bg-yellow-950',
                            default         => 'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800',
                        };
                        $canToggle = !$day['booked'] && !$isPast;
                    @endphp
                    <div class="min-h-16 border-b border-r border-gray-100 dark:border-gray-800 p-1.5 {{ $bgClass }} {{ $canToggle ? 'cursor-pointer' : 'cursor-default' }} transition-colors"
                         @if($canToggle) wire:click="blockDate('{{ $day['date'] }}')" @endif>
                        <div class="flex items-start justify-between">
                            <span class="text-xs font-medium {{ $isToday ? 'text-yellow-700 dark:text-yellow-300 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $day['day'] }}
                            </span>
                            @if($day['blocked'])
                                <span class="text-[10px] rounded-full bg-red-500 text-white px-1.5 py-0.5">Khóa</span>
                            @elseif($day['booked'])
                                <span class="text-[10px] rounded-full bg-blue-500 text-white px-1.5 py-0.5">Đặt</span>
                            @endif
                        </div>
                        @if($day['blocked'] && $day['reason'])
                            <div class="mt-1 text-[10px] text-red-600 dark:text-red-400 truncate">{{ $day['reason'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <p class="text-xs text-gray-400">* Ngày đã có đặt phòng không thể khóa thủ công. Liên hệ Admin để xử lý ngoại lệ.</p>
    </div>
</x-filament-panels::page>
