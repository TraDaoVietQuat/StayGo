<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PrecomputeAdminStats extends Command
{
    protected $signature   = 'admin:precompute-stats';
    protected $description = 'Pre-compute admin dashboard widget stats and store in cache';

    private const TTL = 2100; // 35 phút — hơn chu kỳ scheduler 30 phút để không bị hết cache

    public function handle(): int
    {
        $this->info('Pre-computing admin dashboard stats...');

        $this->computeStatsOverview();
        $this->computeOccupancy();
        $this->computeRevenueChart();
        $this->computeTopHotels();
        $this->computeRevenueRanking();
        $this->computeBookingStatus();
        $this->computePaymentMethod();
        $this->computeRecentBookings();

        $this->info('✅ Done. All stats cached for 35 minutes.');
        return self::SUCCESS;
    }

    private function computeStatsOverview(): void
    {
        $data = [
            'total_revenue'   => Booking::whereNotIn('status', ['cancelled'])->sum('total_price'),
            'total_users'     => User::where('role', '!=', 'admin')->count(),
            'total_hotels'    => Hotel::where('is_active', true)->count(),
            'total_rooms'     => Room::count(),
            'total_bookings'  => Booking::count(),
            'pending_bookings'=> Booking::where('status', 'pending')->count(),
            'pending_support' => SupportRequest::where('status', 'pending')->count(),
        ];
        Cache::put('widget.stats_overview.data', $data, self::TTL);
        $this->line('  ✓ StatsOverview');
    }

    private function computeOccupancy(): void
    {
        $today = now()->toDateString();

        $occupiedRooms = Booking::whereIn('status', ['confirmed', 'pending'])
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>', $today)
            ->distinct('room_id')->count('room_id');

        $totalRooms = Room::count();

        $topHotel = Hotel::where('is_active', true)
            ->withCount(['rooms as occupied' => fn($q) => $q
                ->whereHas('bookings', fn($b) => $b
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->whereDate('check_in', '<=', $today)
                    ->whereDate('check_out', '>', $today)
                )
            ])
            ->withCount('rooms')
            ->get()
            ->map(fn($h) => [
                'name' => $h->name,
                'rate' => $h->rooms_count > 0 ? round(($h->occupied / $h->rooms_count) * 100, 1) : 0,
            ])
            ->sortByDesc('rate')->first();

        $checkInsToday  = Booking::whereIn('status', ['confirmed', 'pending'])->whereDate('check_in', $today)->count();
        $checkOutsToday = Booking::whereIn('status', ['confirmed', 'completed'])->whereDate('check_out', $today)->count();
        $revenueMonth   = Booking::whereNotIn('status', ['cancelled'])
            ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)
            ->sum('total_price');

        Cache::put('widget.occupancy.data', compact(
            'occupiedRooms', 'totalRooms', 'checkInsToday', 'checkOutsToday', 'revenueMonth', 'topHotel'
        ), self::TTL);
        $this->line('  ✓ Occupancy');
    }

    private function computeRevenueChart(): void
    {
        foreach ([3, 6, 12] as $months) {
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
            Cache::put("widget.revenue_chart.data.{$months}", compact('labels', 'revenues', 'orders'), self::TTL);
        }
        $this->line('  ✓ RevenueChart (3/6/12 tháng)');
    }

    private function computeTopHotels(): void
    {
        foreach (['bookings', 'revenue'] as $filter) {
            if ($filter === 'revenue') {
                $data = Booking::whereNotIn('status', ['cancelled'])
                    ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                    ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
                    ->select('hotels.name', DB::raw('SUM(bookings.total_price) as total'))
                    ->groupBy('hotels.id', 'hotels.name')->orderByDesc('total')->limit(3)->get();
                $values = $data->pluck('total')->map(fn($v) => round($v / 1_000_000, 1))->toArray();
                $label  = 'Doanh thu (triệu đ)';
            } else {
                $data = Booking::join('rooms', 'bookings.room_id', '=', 'rooms.id')
                    ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
                    ->select('hotels.name', DB::raw('COUNT(bookings.id) as total'))
                    ->groupBy('hotels.id', 'hotels.name')->orderByDesc('total')->limit(3)->get();
                $values = $data->pluck('total')->toArray();
                $label  = 'Số lượt đặt';
            }
            $labels = $data->pluck('name')
                ->map(fn($n) => mb_strlen($n) > 20 ? mb_substr($n, 0, 20) . '…' : $n)->toArray();
            Cache::put("widget.top_hotels.data.{$filter}", compact('labels', 'values', 'label'), self::TTL);
        }
        $this->line('  ✓ TopHotels (bookings/revenue)');
    }

    private function computeRevenueRanking(): void
    {
        $hotels = Booking::whereNotIn('status', ['cancelled'])
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->select('hotels.id', 'hotels.name',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_price) as total_revenue')
            )
            ->groupBy('hotels.id', 'hotels.name')->orderByDesc('total_revenue')->limit(5)->get();

        Cache::put('widget.revenue_ranking.data', $hotels, self::TTL);
        $this->line('  ✓ RevenueRanking');
    }

    private function computeBookingStatus(): void
    {
        foreach (['all', 'month', 'year'] as $filter) {
            $q = Booking::query();
            if ($filter === 'month') $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            if ($filter === 'year')  $q->whereYear('created_at', now()->year);

            $data = [
                'cancelled' => (clone $q)->where('status', 'cancelled')->count(),
                'completed' => (clone $q)->where('status', 'completed')->count(),
                'confirmed' => (clone $q)->where('status', 'confirmed')->count(),
                'pending'   => (clone $q)->where('status', 'pending')->count(),
            ];
            Cache::put("widget.booking_status.data.{$filter}", $data, self::TTL);
        }
        $this->line('  ✓ BookingStatus (all/month/year)');
    }

    private function computePaymentMethod(): void
    {
        foreach (['all', 'completed', 'pending'] as $filter) {
            $q = Payment::select('method', DB::raw('COUNT(*) as total'))->groupBy('method')->orderByDesc('total');
            if ($filter !== 'all') $q->where('payment_status', $filter);
            Cache::put("widget.payment_method.data.{$filter}", $q->get(), self::TTL);
        }
        $this->line('  ✓ PaymentMethod (all/completed/pending)');
    }

    private function computeRecentBookings(): void
    {
        $bookings = Booking::with(['room.hotel'])->latest()->limit(8)->get();
        Cache::put('widget.recent_bookings.data', $bookings, self::TTL);
        $this->line('  ✓ RecentBookings');
    }
}
