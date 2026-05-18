<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmation;
use App\Mail\BookingRefunded;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon   = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel  = 'Đặt phòng';
    protected static ?string $modelLabel       = 'Đặt phòng';
    protected static ?string $pluralModelLabel = 'Quản lý đặt phòng';
    protected static ?string $navigationGroup  = 'Giao Dịch';
    protected static ?int    $navigationSort   = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Booking::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function statusOptions(): array
    {
        return [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'refunded'  => 'Đã hoàn tiền',
            'completed' => 'Hoàn thành',
        ];
    }

    public static function paymentMethodOptions(): array
    {
        return [
            'hotel'         => 'Thanh toán tại khách sạn',
            'momo'          => 'Ví MoMo',
            'vnpay'         => 'VNPay',
            'bank'          => 'Chuyển khoản ngân hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'zalopay'       => 'ZaloPay',
            'cod'           => 'Thanh toán khi nhận phòng',
        ];
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin đặt phòng')->schema([
                Forms\Components\TextInput::make('order_code')
                    ->label('Mã đặt phòng')
                    ->required()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('room_id')
                    ->label('Phòng')
                    ->options(
                        Room::with('hotel')
                            ->get()
                            ->mapWithKeys(fn($r) => [
                                $r->id => ($r->hotel?->name ?? 'N/A') . ' — ' . $r->room_name,
                            ])
                    )
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options(static::statusOptions())
                    ->required()
                    ->default('pending'),
            ])->columns(3),

            Forms\Components\Section::make('Thông tin khách hàng')->schema([
                Forms\Components\TextInput::make('full_name')->label('Họ và tên')->maxLength(100),
                Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(100),
                Forms\Components\TextInput::make('phone')->label('Số điện thoại')->tel()->maxLength(20),
            ])->columns(3),

            Forms\Components\Section::make('Lịch lưu trú & Thanh toán')->schema([
                Forms\Components\DatePicker::make('check_in')
                    ->label('Ngày nhận phòng')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('check_out')
                    ->label('Ngày trả phòng')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('stay_type')
                    ->label('Loại lưu trú')
                    ->options(['night' => 'Qua đêm', 'day' => 'Qua ngày'])
                    ->default('night'),
                Forms\Components\TextInput::make('total_price')
                    ->label('Tổng tiền (đ)')
                    ->numeric()
                    ->prefix('₫'),
                Forms\Components\Select::make('payment_method')
                    ->label('Phương thức thanh toán')
                    ->options(static::paymentMethodOptions())
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('Ghi chú')
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Mã giảm giá')->schema([
                Forms\Components\TextInput::make('discount_code')
                    ->label('Mã giảm giá')
                    ->maxLength(50)
                    ->nullable(),
                Forms\Components\TextInput::make('discount_percent')
                    ->label('% Giảm')
                    ->numeric()
                    ->default(0)
                    ->suffix('%'),
                Forms\Components\TextInput::make('discount_amount')
                    ->label('Số tiền giảm (đ)')
                    ->numeric()
                    ->default(0)
                    ->prefix('₫'),
            ])->columns(3),

            Forms\Components\Section::make('Hoàn tiền')->schema([
                Forms\Components\Toggle::make('refund_requested')
                    ->label('Yêu cầu hoàn tiền'),
                Forms\Components\DateTimePicker::make('refund_requested_at')
                    ->label('Thời gian yêu cầu')
                    ->native(false),
                Forms\Components\TextInput::make('refund_amount')
                    ->label('Số tiền hoàn (đ)')
                    ->numeric()
                    ->default(0)
                    ->prefix('₫'),
            ])->columns(3),
        ]);
    }

    // ------------------------------------------------------------------
    // Table
    // ------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['room.hotel', 'payment']))
            ->searchPlaceholder('Tên, email, SĐT, mã đơn...')

            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_code')
                    ->label('Mã đơn')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->weight(FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Khách hàng')
                    ->searchable(['full_name', 'email', 'phone'])
                    ->html()
                    ->formatStateUsing(function ($state, Booking $record): HtmlString {
                        $name  = e($state ?? '');
                        $extra = '';
                        if ($record->refund_requested) {
                            $extra = '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700 ring-1 ring-inset ring-red-600/20 ml-1">↩ Hoàn tiền</span>';
                        }
                        $email = e($record->email ?? '');
                        $phone = e($record->phone ?? '');
                        return new HtmlString(
                            '<div class="flex flex-col gap-0.5">'
                            . '<div class="font-medium flex items-center gap-1">' . $name . $extra . '</div>'
                            . '<div class="text-xs text-gray-500 dark:text-gray-400">' . $email . '</div>'
                            . '<div class="text-xs text-gray-500 dark:text-gray-400">' . $phone . '</div>'
                            . '</div>'
                        );
                    }),

                Tables\Columns\TextColumn::make('room.hotel.name')
                    ->label('Khách sạn / Phòng')
                    ->description(fn(Booking $record): string => $record->room?->room_name ?? '—'),

                Tables\Columns\TextColumn::make('stay_dates')
                    ->label('Ngày lưu trú')
                    ->getStateUsing(function (Booking $record): string {
                        if (! $record->check_in || ! $record->check_out) {
                            return '—';
                        }
                        $nights = $record->check_in->diffInDays($record->check_out);
                        return $record->check_in->format('d/m/Y')
                            . ' → ' . $record->check_out->format('d/m/Y')
                            . ' (' . $nights . ' đêm)';
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->getStateUsing(function (Booking $record): HtmlString {
                        $total = number_format((int) $record->total_price, 0, ',', '.') . 'đ';
                        if ($record->discount_amount > 0) {
                            $disc = '<div class="text-xs text-rose-500">🎫 ' . $record->discount_code . ' -' . number_format($record->discount_amount, 0, ',', '.') . 'đ</div>';
                            return new HtmlString('<div class="font-bold text-green-600">' . $total . '</div>' . $disc);
                        }
                        return new HtmlString('<span class="font-bold text-green-600">' . $total . '</span>');
                    })
                    ->html()
                    ->sortable('total_price'),

                Tables\Columns\TextColumn::make('stay_type')
                    ->label('Loại ở')
                    ->formatStateUsing(fn($state) => $state === 'day' ? '🌅 Qua ngày' : '🌙 Qua đêm')
                    ->badge()
                    ->color(fn($state) => $state === 'day' ? 'warning' : 'info')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Thanh toán')
                    ->formatStateUsing(fn($state) => static::paymentMethodOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'momo'                    => 'danger',
                        'vnpay'                   => 'info',
                        'hotel'                   => 'warning',
                        'bank', 'bank_transfer'   => 'gray',
                        'zalopay'                 => 'success',
                        default                   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($state) => static::statusOptions()[$state] ?? $state)
                    ->color(fn(?string $state): string => match ($state) {
                        'confirmed'  => 'success',
                        'completed'  => 'success',
                        'pending'    => 'warning',
                        'cancelled'  => 'danger',
                        'refunded'   => 'info',
                        default      => 'gray',
                    }),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(static::statusOptions())
                    ->placeholder('Tất cả trạng thái'),

                SelectFilter::make('payment_method')
                    ->label('Phương thức')
                    ->options(static::paymentMethodOptions())
                    ->placeholder('Tất cả phương thức'),

                Filter::make('date_range')
                    ->label('Khoảng ngày đặt')
                    ->columns(2)
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Từ ngày')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Đến ngày')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from_date'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['to_date'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),

                Filter::make('refund_requested')
                    ->label('Yêu cầu hoàn tiền')
                    ->query(fn(Builder $query) => $query->where('refund_requested', true))
                    ->toggle(),

                TernaryFilter::make('has_discount')
                    ->label('Có mã giảm giá')
                    ->queries(
                        true:  fn(Builder $q) => $q->whereNotNull('discount_code'),
                        false: fn(Builder $q) => $q->whereNull('discount_code'),
                    ),

                SelectFilter::make('stay_type')
                    ->label('Loại lưu trú')
                    ->options(['night' => 'Qua đêm', 'day' => 'Qua ngày'])
                    ->placeholder('Tất cả'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)

            ->actions([
                Action::make('confirm')
                    ->label('Xác nhận')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->button()
                    ->visible(fn(Booking $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận đặt phòng')
                    ->modalDescription('Xác nhận đơn đặt phòng này và thông báo cho khách hàng?')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->action(function (Booking $record): void {
                        $record->update(['status' => 'confirmed']);
                        if ($record->payment) {
                            $record->payment->update(['payment_status' => 'completed']);
                        }
                        if ($record->email) {
                            Mail::to($record->email)->send(new BookingConfirmation($record));
                        }
                        Notification::make()->title('Đã xác nhận đặt phòng')->success()->send();
                    }),

                Action::make('cancel')
                    ->label('Hủy')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->button()
                    ->visible(fn(Booking $record): bool => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->modalHeading('Hủy đặt phòng')
                    ->modalDescription('Bạn có chắc chắn muốn hủy đơn đặt phòng này?')
                    ->modalSubmitActionLabel('Hủy đặt phòng')
                    ->action(function (Booking $record): void {
                        $record->update(['status' => 'cancelled']);
                        if ($record->email) {
                            Mail::to($record->email)->send(new BookingCancelled($record));
                        }
                        Notification::make()->title('Đã hủy đặt phòng')->danger()->send();
                    }),

                Action::make('approve_refund')
                    ->label('Duyệt hoàn tiền')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->button()
                    ->visible(fn(Booking $record): bool => $record->refund_requested && $record->status !== 'refunded')
                    ->requiresConfirmation()
                    ->modalHeading('Duyệt yêu cầu hoàn tiền')
                    ->modalDescription('Xác nhận hoàn tiền cho khách hàng?')
                    ->modalSubmitActionLabel('Duyệt hoàn tiền')
                    ->action(function (Booking $record): void {
                        $record->update([
                            'status'           => 'refunded',
                            'refund_requested' => false,
                        ]);
                        if ($record->payment) {
                            $record->payment->update(['payment_status' => 'refunded']);
                        }
                        if ($record->email) {
                            Mail::to($record->email)->send(new BookingRefunded($record));
                        }
                        Notification::make()->title('Đã duyệt hoàn tiền')->warning()->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Chỉnh sửa')
                    ->iconButton(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),

                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Xuất CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(function (Collection $records) {
                            $headers = ['ID', 'Mã đơn', 'Khách hàng', 'Email', 'SĐT', 'Khách sạn', 'Phòng',
                                'Check-in', 'Check-out', 'Loại ở', 'Tổng tiền', 'Mã giảm giá', 'Tiền giảm',
                                'Phương thức TT', 'Trạng thái', 'Ngày đặt'];

                            $rows = $records->map(fn(Booking $b) => [
                                $b->id,
                                $b->order_code,
                                $b->full_name,
                                $b->email,
                                $b->phone,
                                $b->room?->hotel?->name ?? '—',
                                $b->room?->room_name ?? '—',
                                $b->check_in?->format('d/m/Y'),
                                $b->check_out?->format('d/m/Y'),
                                $b->stay_type === 'day' ? 'Qua ngày' : 'Qua đêm',
                                $b->total_price,
                                $b->discount_code ?? '',
                                $b->discount_amount ?? 0,
                                $b->payment_method,
                                $b->status,
                                $b->created_at?->format('d/m/Y H:i'),
                            ]);

                            $csv = implode(',', $headers) . "\n";
                            foreach ($rows as $row) {
                                $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
                            }

                            return response()->streamDownload(
                                fn() => print($csv),
                                'bookings-' . now()->format('Ymd-His') . '.csv',
                                ['Content-Type' => 'text/csv; charset=UTF-8']
                            );
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit'   => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
