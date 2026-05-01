<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Booking;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';
    protected static ?string $title = 'Lịch sử đặt phòng';
    protected static ?string $icon  = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('order_code')
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Mã đơn')
                    ->weight(FontWeight::SemiBold)
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('room.hotel.name')
                    ->label('Khách sạn / Phòng')
                    ->description(fn(Booking $r): string => $r->room?->room_name ?? '—'),

                Tables\Columns\TextColumn::make('stay_info')
                    ->label('Ngày lưu trú')
                    ->getStateUsing(function (Booking $r): string {
                        if (!$r->check_in || !$r->check_out) return '—';
                        $nights = $r->check_in->diffInDays($r->check_out);
                        return $r->check_in->format('d/m/Y') . ' → ' . $r->check_out->format('d/m/Y')
                            . ' (' . $nights . ' ' . ($r->stay_type === 'day' ? 'ngày' : 'đêm') . ')';
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->getStateUsing(function (Booking $r): HtmlString {
                        $total = number_format((int) $r->total_price, 0, ',', '.') . 'đ';
                        if ($r->discount_amount > 0) {
                            return new HtmlString(
                                '<span class="font-bold text-green-600">' . $total . '</span>'
                                . '<div class="text-xs text-rose-500">-' . number_format($r->discount_amount, 0, ',', '.') . 'đ (' . $r->discount_code . ')</div>'
                            );
                        }
                        return new HtmlString('<span class="font-bold text-green-600">' . $total . '</span>');
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending'   => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'cancelled' => 'Đã hủy',
                        'refunded'  => 'Hoàn tiền',
                        'completed' => 'Hoàn thành',
                        default     => $state,
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'confirmed', 'completed' => 'success',
                        'pending'   => 'warning',
                        'cancelled' => 'danger',
                        'refunded'  => 'info',
                        default     => 'gray',
                    }),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
