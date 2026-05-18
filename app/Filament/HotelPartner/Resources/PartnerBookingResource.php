<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\PartnerBookingResource\Pages;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
class PartnerBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon   = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel  = 'Đặt phòng';
    protected static ?string $modelLabel       = 'Đặt phòng';
    protected static ?string $pluralModelLabel = 'Quản lý đặt phòng';
    protected static ?string $navigationGroup  = 'Đặt phòng & Doanh thu';
    protected static ?int    $navigationSort   = 1;

    public static function getNavigationBadge(): ?string
    {
        try {
            $hotel = auth('hotel_partner')->user()?->managedHotel;
            if (!$hotel) return null;
            $hotelId = $hotel->id;
            $count = Cache::remember("badge.partner.bookings.{$hotelId}", 60, function () use ($hotelId) {
                $roomIds = \App\Models\Room::where('hotel_id', $hotelId)->pluck('id');
                return Booking::whereIn('room_id', $roomIds)->where('status', 'pending')->count();
            });
            return $count > 0 ? (string) $count : null;
        } catch (\Exception) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $hotel   = auth('hotel_partner')->user()?->managedHotel;
        $roomIds = $hotel ? $hotel->rooms()->pluck('id') : collect();
        return parent::getEloquentQuery()->whereIn('room_id', $roomIds)->with(['room']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('order_code')->label('Mã đơn')->disabled(),
                Forms\Components\TextInput::make('full_name')->label('Tên khách')->disabled(),
                Forms\Components\TextInput::make('email')->label('Email')->disabled(),
                Forms\Components\TextInput::make('phone')->label('SĐT')->disabled(),
                Forms\Components\TextInput::make('check_in')->label('Ngày nhận phòng')->disabled(),
                Forms\Components\TextInput::make('check_out')->label('Ngày trả phòng')->disabled(),
                Forms\Components\TextInput::make('total_price')->label('Tổng tiền')->disabled(),
                Forms\Components\TextInput::make('status')->label('Trạng thái')->disabled(),
                Forms\Components\Textarea::make('note')->label('Ghi chú khách')->disabled()->columnSpanFull(),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Mã đơn')->searchable()->copyable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->description(fn(Booking $r) => $r->phone ?? ''),
                Tables\Columns\TextColumn::make('room.room_name')->label('Phòng'),
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Check-in')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Check-out')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending'   => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'cancelled' => 'Đã hủy',
                        'completed' => 'Hoàn thành',
                        'refunded'  => 'Đã hoàn tiền',
                        default     => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'confirmed','completed' => 'success',
                        'pending'               => 'warning',
                        'cancelled','refunded'  => 'danger',
                        default                 => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')
                    ->options([
                        'pending'   => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'cancelled' => 'Đã hủy',
                        'completed' => 'Hoàn thành',
                    ]),
            ])
            ->actions([
                Action::make('confirm')
                    ->label('Xác nhận')->icon('heroicon-o-check')->color('success')->button()
                    ->visible(fn(Booking $r) => $r->status === 'pending')
                    ->requiresConfirmation()->modalHeading('Xác nhận đặt phòng?')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'confirmed']);
                        if ($record->email) {
                            try {
                                Mail::to($record->email)->send(new BookingConfirmation($record));
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning('PartnerBooking confirm mail failed: ' . $e->getMessage());
                            }
                        }
                        Notification::make()->title('Đã xác nhận đơn #' . $record->order_code)->success()->send();
                    }),

                Action::make('checkin')
                    ->label('Check-in')->icon('heroicon-o-arrow-right-circle')->color('info')->button()
                    ->visible(fn(Booking $r) => $r->status === 'confirmed' && $r->check_in?->lte(now()))
                    ->requiresConfirmation()->modalHeading('Xác nhận khách đã nhận phòng?')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'confirmed']);
                        Notification::make()->title('Đã đánh dấu check-in')->success()->send();
                    }),

                Action::make('checkout')
                    ->label('Check-out')->icon('heroicon-o-arrow-left-circle')->color('gray')->button()
                    ->visible(fn(Booking $r) => $r->status === 'confirmed' && $r->check_out?->lte(now()))
                    ->requiresConfirmation()->modalHeading('Xác nhận khách đã trả phòng?')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()->title('Đã đánh dấu hoàn thành')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Từ chối')->icon('heroicon-o-x-mark')->color('danger')->button()
                    ->visible(fn(Booking $r) => $r->status === 'pending')
                    ->requiresConfirmation()->modalHeading('Từ chối đặt phòng?')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'cancelled']);
                        if ($record->email) {
                            try {
                                Mail::to($record->email)->send(new BookingCancelled($record));
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning('PartnerBooking reject mail failed: ' . $e->getMessage());
                            }
                        }
                        Notification::make()->title('Đã từ chối đơn #' . $record->order_code)->danger()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerBookings::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
