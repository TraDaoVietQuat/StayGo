<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Mail\PaymentConfirmed;
use App\Mail\PaymentFailed;
use App\Models\Payment;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Thanh toán';
    protected static ?string $modelLabel      = 'Thanh toán';
    protected static ?string $pluralModelLabel = 'Lịch sử thanh toán';
    protected static ?string $navigationGroup = 'Giao Dịch';
    protected static ?int    $navigationSort  = 2;

    // ------------------------------------------------------------------
    // Navigation badge — pending payments count
    // ------------------------------------------------------------------
    public static function getNavigationBadge(): ?string
    {
        $count = Cache::remember('badge.payments.pending', 60, fn () => Payment::where('payment_status', 'pending')->count());
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------
    public static function methodOptions(): array
    {
        return [
            'hotel'         => 'Thanh toán tại khách sạn',
            'momo'          => 'Ví MoMo',
            'vnpay'         => 'VNPay',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'zalopay'       => 'ZaloPay',
            'cod'           => 'Thanh toán khi nhận phòng',
        ];
    }

    public static function methodLabel(string $method): string
    {
        return static::methodOptions()[$method] ?? $method;
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin đặt phòng')->schema([
                Forms\Components\Select::make('booking_id')
                    ->label('Mã đặt phòng')
                    ->relationship('booking', 'order_code')
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('hotel_name')
                    ->label('Tên khách sạn')
                    ->maxLength(255),
                Forms\Components\TextInput::make('room_name')
                    ->label('Tên phòng')
                    ->maxLength(100),
            ])->columns(3),

            Forms\Components\Section::make('Thông tin khách hàng')->schema([
                Forms\Components\TextInput::make('full_name')
                    ->label('Họ và tên')
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(150),
                Forms\Components\TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->tel()
                    ->maxLength(20),
            ])->columns(3),

            Forms\Components\Section::make('Thanh toán')->schema([
                Forms\Components\Select::make('method')
                    ->label('Phương thức thanh toán')
                    ->options(static::methodOptions()),
                Forms\Components\TextInput::make('amount')
                    ->label('Số tiền (đ)')
                    ->numeric()
                    ->prefix('₫'),
                Forms\Components\Select::make('payment_status')
                    ->label('Trạng thái')
                    ->options([
                        'pending'   => 'Chờ xử lý',
                        'completed' => 'Đã thanh toán',
                        'failed'    => 'Thất bại',
                        'refunded'  => 'Hoàn tiền',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Toggle::make('qr_scanned')
                    ->label('Cảnh báo KH (đã quét QR)')
                    ->helperText('Bật nếu khách hàng đã quét QR nhưng chưa được xác nhận.'),
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
            ->modifyQueryUsing(fn(Builder $query) => $query->with('booking'))
            ->searchPlaceholder('Tên, khách sạn, email, SĐT...')

            // ── Columns ──────────────────────────────────────────────
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.order_code')
                    ->label('Mã đơn')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Khách hàng')
                    ->searchable(['full_name', 'email', 'phone'])
                    ->html()
                    ->formatStateUsing(function ($state, Payment $record): HtmlString {
                        $name  = e($state ?? '');
                        $badge = $record->qr_scanned
                            ? '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20 ml-1">⚠ Cảnh báo</span>'
                            : '';
                        $email = e($record->email ?? '');
                        $phone = e($record->phone ?? '');
                        return new HtmlString(
                            '<div class="flex flex-col gap-0.5">'
                            . '<div class="font-medium flex items-center gap-1">' . $name . $badge . '</div>'
                            . '<div class="text-xs text-gray-500 dark:text-gray-400">' . $email . '</div>'
                            . '<div class="text-xs text-gray-500 dark:text-gray-400">' . $phone . '</div>'
                            . '</div>'
                        );
                    }),

                Tables\Columns\TextColumn::make('hotel_name')
                    ->label('Khách sạn / Phòng')
                    ->searchable()
                    ->description(fn(Payment $record): string => $record->room_name ?? ''),

                Tables\Columns\TextColumn::make('stay_dates')
                    ->label('Ngày lưu trú')
                    ->getStateUsing(function (Payment $record): string {
                        if (! $record->booking) {
                            return '—';
                        }
                        $in  = Carbon::parse($record->booking->check_in)->format('d/m/Y');
                        $out = Carbon::parse($record->booking->check_out)->format('d/m/Y');
                        return $in . ' → ' . $out;
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.') . 'đ')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\TextColumn::make('method')
                    ->label('Phương thức')
                    ->formatStateUsing(fn($state) => static::methodLabel($state))
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'momo'          => 'danger',
                        'vnpay'         => 'info',
                        'hotel'         => 'warning',
                        'bank_transfer' => 'gray',
                        'zalopay'       => 'success',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày thanh toán')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending'   => 'Chờ xử lý',
                        'completed' => 'Đã thanh toán',
                        'failed'    => 'Thất bại',
                        'refunded'  => 'Hoàn tiền',
                        default     => $state,
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending'   => 'warning',
                        'failed'    => 'danger',
                        'refunded'  => 'info',
                        default     => 'gray',
                    }),
            ])

            // ── Filters (above table content) ────────────────────────
            ->filters([
                SelectFilter::make('method')
                    ->label('Phương thức')
                    ->options(static::methodOptions())
                    ->placeholder('Tất cả phương thức'),

                Filter::make('date_range')
                    ->label('Khoảng ngày')
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators[] = 'Từ: ' . Carbon::parse($data['from_date'])->format('d/m/Y');
                        }
                        if ($data['to_date'] ?? null) {
                            $indicators[] = 'Đến: ' . Carbon::parse($data['to_date'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)

            // ── Row actions ──────────────────────────────────────────
            ->actions([
                Action::make('confirm')
                    ->label('Xác nhận')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->button()
                    ->visible(fn(Payment $record): bool => $record->payment_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận thanh toán')
                    ->modalDescription('Bạn có chắc chắn muốn xác nhận thanh toán này?')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->action(function (Payment $record): void {
                        $record->update(['payment_status' => 'completed']);
                        if ($record->email) {
                            try {
                                $booking = $record->booking?->load('room.hotel');
                                if ($booking) {
                                    Mail::to($record->email)->send(new PaymentConfirmed($booking));
                                }
                            } catch (\Exception) {}
                        }
                        Notification::make()
                            ->title('Đã xác nhận thanh toán')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->button()
                    ->visible(fn(Payment $record): bool => $record->payment_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Từ chối thanh toán')
                    ->modalDescription('Bạn có chắc chắn muốn từ chối thanh toán này?')
                    ->modalSubmitActionLabel('Từ chối')
                    ->action(function (Payment $record): void {
                        $record->update(['payment_status' => 'failed']);
                        if ($record->email) {
                            try {
                                $booking = $record->booking?->load('room.hotel');
                                if ($booking) {
                                    Mail::to($record->email)->send(new PaymentFailed($booking));
                                }
                            } catch (\Exception) {}
                        }
                        Notification::make()
                            ->title('Đã từ chối thanh toán')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Chỉnh sửa')
                    ->iconButton(),
            ])

            // ── Bulk actions ─────────────────────────────────────────
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),

                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Xuất CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(function (Collection $records) {
                            $headers = ['ID', 'Mã đặt phòng', 'Khách hàng', 'Email', 'SĐT',
                                'Khách sạn', 'Phòng', 'Số tiền', 'Phương thức', 'Trạng thái', 'Ngày TT'];

                            $rows = $records->map(fn(Payment $p) => [
                                $p->id,
                                $p->booking?->order_code ?? '—',
                                $p->full_name,
                                $p->email,
                                $p->phone,
                                $p->hotel_name,
                                $p->room_name,
                                $p->amount,
                                $p->method,
                                $p->payment_status,
                                $p->created_at?->format('d/m/Y H:i'),
                            ]);

                            $csv = implode(',', $headers) . "\n";
                            foreach ($rows as $row) {
                                $csv .= implode(',', array_map(
                                    fn($v) => '"' . str_replace('"', '""', (string) $v) . '"',
                                    $row
                                )) . "\n";
                            }

                            return response()->streamDownload(
                                fn() => print($csv),
                                'payments-' . now()->format('Ymd-His') . '.csv',
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
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
