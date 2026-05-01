<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = '📈 Doanh thu & Số đơn theo tháng';
    protected static ?int $sort = 2;
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    public ?string $filter = '6';

    protected function getFilters(): ?array
    {
        return [
            '3'  => '3 tháng gần nhất',
            '6'  => '6 tháng gần nhất',
            '12' => '12 tháng gần nhất',
        ];
    }

    protected function getData(): array
    {
        $months = (int) ($this->filter ?? 6);
        $d = Cache::get("widget.revenue_chart.data.{$months}") ?? $this->computeLive($months);

        return [
            'datasets' => [
                [
                    'label'           => 'Doanh thu (triệu đ)',
                    'data'            => $d['revenues'],
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'yAxisID'         => 'y',
                ],
                [
                    'label'           => 'Số đơn',
                    'data'            => $d['orders'],
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'yAxisID'         => 'y1',
                ],
            ],
            'labels' => $d['labels'],
        ];
    }

    private function computeLive(int $months): array
    {
        $labels = $revenues = $orders = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $labels[] = $date->format('m/Y');
            $revenues[] = round(
                Booking::whereNotIn('status', ['cancelled'])
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_price') / 1_000_000, 2
            );
            $orders[] = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
        }
        return compact('labels', 'revenues', 'orders');
    }

    protected function getType(): string { return 'line'; }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y'  => ['position' => 'left',  'beginAtZero' => true, 'title' => ['display' => true, 'text' => 'Triệu đồng']],
                'y1' => ['position' => 'right', 'beginAtZero' => true, 'grid' => ['drawOnChartArea' => false], 'title' => ['display' => true, 'text' => 'Số đơn']],
            ],
        ];
    }
}
