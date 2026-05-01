<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TopHotelsChartWidget extends ChartWidget
{
    protected static ?string $heading = '🏆 Top khách sạn được đặt nhiều';
    protected static ?int $sort = 4;
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '260px';

    public ?string $filter = 'bookings';

    protected function getFilters(): ?array
    {
        return [
            'bookings' => 'Theo số lượt đặt',
            'revenue'  => 'Theo doanh thu',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'bookings';
        $d = Cache::get("widget.top_hotels.data.{$filter}") ?? $this->computeLive($filter);

        $colors = ['#3b82f6', '#22c55e', '#f59e0b', '#a855f7', '#ef4444'];

        return [
            'datasets' => [[
                'label'           => $d['label'],
                'data'            => $d['values'],
                'backgroundColor' => array_slice($colors, 0, count($d['values'])),
                'borderRadius'    => 6,
            ]],
            'labels' => $d['labels'],
        ];
    }

    private function computeLive(string $filter): array
    {
        if ($filter === 'revenue') {
            $data   = Booking::whereNotIn('status', ['cancelled'])
                ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
                ->select('hotels.name', DB::raw('SUM(bookings.total_price) as total'))
                ->groupBy('hotels.id', 'hotels.name')->orderByDesc('total')->limit(3)->get();
            $values = $data->pluck('total')->map(fn($v) => round($v / 1_000_000, 1))->toArray();
            $label  = 'Doanh thu (triệu đ)';
        } else {
            $data   = Booking::join('rooms', 'bookings.room_id', '=', 'rooms.id')
                ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
                ->select('hotels.name', DB::raw('COUNT(bookings.id) as total'))
                ->groupBy('hotels.id', 'hotels.name')->orderByDesc('total')->limit(3)->get();
            $values = $data->pluck('total')->toArray();
            $label  = 'Số lượt đặt';
        }
        $labels = $data->pluck('name')
            ->map(fn($n) => mb_strlen($n) > 20 ? mb_substr($n, 0, 20) . '…' : $n)->toArray();

        return compact('labels', 'values', 'label');
    }

    protected function getType(): string { return 'bar'; }

    protected function getOptions(): array
    {
        return [
            'indexAxis'           => 'y',
            'maintainAspectRatio' => false,
            'layout'   => ['padding' => ['left' => 8, 'right' => 8]],
            'scales'   => [
                'x' => ['beginAtZero' => true, 'ticks' => ['font' => ['size' => 11]]],
                'y' => ['ticks' => ['font' => ['size' => 10], 'crossAlign' => 'far']],
            ],
            'datasets' => ['bar' => ['barThickness' => 24, 'maxBarThickness' => 28]],
            'plugins'  => ['legend' => ['display' => false]],
        ];
    }
}
