<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class BookingStatusWidget extends ChartWidget
{
    protected static ?string $heading = '🍩 Tỉ lệ trạng thái đơn';
    protected static ?int $sort = 3;
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all'   => 'Tất cả thời gian',
            'month' => 'Tháng này',
            'year'  => 'Năm nay',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'all';
        $d = Cache::get("widget.booking_status.data.{$filter}") ?? $this->computeLive($filter);

        return [
            'datasets' => [[
                'data'            => [$d['cancelled'], $d['completed'], $d['confirmed'], $d['pending']],
                'backgroundColor' => ['#ef4444', '#22c55e', '#3b82f6', '#f59e0b'],
                'borderWidth'     => 2,
                'borderColor'     => '#fff',
            ]],
            'labels' => ['Đã hủy', 'Hoàn thành', 'Đã xác nhận', 'Chờ xác nhận'],
        ];
    }

    private function computeLive(string $filter): array
    {
        $q = Booking::query();
        if ($filter === 'month') {
            $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($filter === 'year') {
            $q->whereYear('created_at', now()->year);
        }

        return [
            'cancelled' => (clone $q)->where('status', 'cancelled')->count(),
            'completed' => (clone $q)->where('status', 'completed')->count(),
            'confirmed' => (clone $q)->where('status', 'confirmed')->count(),
            'pending'   => (clone $q)->where('status', 'pending')->count(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
