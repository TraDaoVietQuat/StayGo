<?php

namespace App\Filament\HotelPartner\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingCheckInsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Check-in sắp tới (7 ngày)';

    public function table(Table $table): Table
    {
        $hotel   = auth('hotel_partner')->user()?->managedHotel;
        $roomIds = $hotel ? $hotel->rooms()->pluck('id') : collect();

        return $table
            ->query(
                Booking::query()
                    ->whereIn('room_id', $roomIds)
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->whereBetween('check_in', [now()->toDateString(), now()->addDays(7)->toDateString()])
                    ->with(['room'])
                    ->orderBy('check_in')
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Mã đơn')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Khách hàng')
                    ->description(fn(Booking $r) => $r->phone ?? ''),
                Tables\Columns\TextColumn::make('room.room_name')
                    ->label('Phòng'),
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Check-in')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Check-out')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'confirmed' => 'Đã xác nhận', 'pending' => 'Chờ xác nhận', default => $state,
                    })
                    ->color(fn($state) => $state === 'confirmed' ? 'success' : 'warning'),
            ])
            ->paginated(false);
    }
}
