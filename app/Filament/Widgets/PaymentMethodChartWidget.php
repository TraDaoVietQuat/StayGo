<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaymentMethodChartWidget extends ChartWidget
{
    protected static ?string $heading    = '💳 Phương thức thanh toán';
    protected static ?int    $sort       = 3;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight  = '260px';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all'       => 'Tất cả',
            'completed' => 'Đã thanh toán',
            'pending'   => 'Chờ xử lý',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'all';
        $data = Cache::get("widget.payment_method.data.{$filter}") ?? $this->computeLive($filter);

        $labels = $data->pluck('method')->map(fn($m) => match ($m) {
            'hotel'         => 'Tại khách sạn',
            'momo'          => 'MoMo',
            'vnpay'         => 'VNPay',
            'bank_transfer', 'bank' => 'Chuyển khoản',
            'zalopay'       => 'ZaloPay',
            'cod'           => 'Nhận phòng',
            default         => strtoupper($m),
        })->toArray();

        $values = $data->pluck('total')->toArray();

        $colors = [
            '#e91e8c', '#3b82f6', '#22c55e',
            '#f59e0b', '#a855f7', '#ef4444', '#64748b',
        ];

        return [
            'datasets' => [[
                'data'            => $values,
                'backgroundColor' => array_slice($colors, 0, count($values)),
                'borderWidth'     => 2,
                'borderColor'     => '#fff',
                'hoverOffset'     => 8,
            ]],
            'labels' => $labels,
        ];
    }

    private function computeLive(string $filter)
    {
        $q = Payment::select('method', DB::raw('COUNT(*) as total'))
            ->groupBy('method')
            ->orderByDesc('total');

        if ($filter !== 'all') {
            $q->where('payment_status', $filter);
        }

        return $q->get();
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'cutout'              => '65%',
            'plugins' => [
                'legend' => [
                    'position'  => 'right',
                    'labels'    => [
                        'font'      => ['size' => 11],
                        'padding'   => 10,
                        'boxWidth'  => 12,
                        'boxHeight' => 12,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [],
                ],
            ],
        ];
    }
}
