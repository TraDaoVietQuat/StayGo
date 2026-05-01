<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Widgets\BookingStatsWidget;
use App\Models\Booking;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Thêm đặt phòng'),

            Action::make('export_csv')
                ->label('Xuất Excel / CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): StreamedResponse {
                    $statusVi = [
                        'pending'   => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        'refunded'  => 'Đã hoàn tiền',
                    ];
                    $methodVi = [
                        'hotel'         => 'Tại khách sạn',
                        'momo'          => 'Ví MoMo',
                        'vnpay'         => 'VNPay',
                        'bank'          => 'Chuyển khoản',
                        'bank_transfer' => 'Chuyển khoản',
                        'zalopay'       => 'ZaloPay',
                        'cod'           => 'Khi nhận phòng',
                    ];

                    $bookings = Booking::with(['room.hotel'])
                        ->latest()
                        ->get();

                    return response()->streamDownload(function () use ($bookings, $statusVi, $methodVi) {
                        $out = fopen('php://output', 'w');
                        fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 cho Excel
                        fputcsv($out, [
                            'Mã đơn', 'Họ tên khách', 'Email', 'SĐT',
                            'Khách sạn', 'Loại phòng',
                            'Ngày nhận phòng', 'Ngày trả phòng', 'Số đêm',
                            'Tổng tiền (đ)', 'Phương thức TT',
                            'Trạng thái', 'Ngày đặt', 'Ghi chú',
                        ]);

                        foreach ($bookings as $b) {
                            $nights = ($b->check_in && $b->check_out)
                                ? $b->check_in->diffInDays($b->check_out)
                                : '—';
                            fputcsv($out, [
                                $b->order_code ?? '',
                                $b->full_name ?? '',
                                $b->email ?? '',
                                $b->phone ?? '',
                                $b->room?->hotel?->name ?? '—',
                                $b->room?->room_name ?? '—',
                                $b->check_in?->format('d/m/Y') ?? '',
                                $b->check_out?->format('d/m/Y') ?? '',
                                $nights,
                                $b->total_price ?? 0,
                                $methodVi[$b->payment_method] ?? strtoupper($b->payment_method ?? ''),
                                $statusVi[$b->status] ?? ($b->status ?? ''),
                                $b->created_at?->format('d/m/Y H:i') ?? '',
                                $b->note ?? '',
                            ]);
                        }
                        fclose($out);
                    }, 'bookings_' . now()->format('Ymd_His') . '.csv');
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookingStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(Booking::count()),

            'pending' => Tab::make('Chờ xác nhận')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(Booking::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'confirmed' => Tab::make('Đã xác nhận')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'confirmed'))
                ->badge(Booking::where('status', 'confirmed')->count())
                ->badgeColor('success'),

            'completed' => Tab::make('Hoàn thành')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(Booking::where('status', 'completed')->count())
                ->badgeColor('success'),

            'cancelled' => Tab::make('Đã hủy')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(Booking::where('status', 'cancelled')->count())
                ->badgeColor('danger'),

            'refunded' => Tab::make('Đã hoàn tiền')
                ->icon('heroicon-o-arrow-uturn-left')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'refunded'))
                ->badge(Booking::where('status', 'refunded')->count())
                ->badgeColor('info'),

            'refund_requested' => Tab::make('Yêu cầu hoàn tiền')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('refund_requested', true)->where('status', '!=', 'refunded'))
                ->badge(Booking::where('refund_requested', true)->where('status', '!=', 'refunded')->count())
                ->badgeColor('danger'),
        ];
    }
}
