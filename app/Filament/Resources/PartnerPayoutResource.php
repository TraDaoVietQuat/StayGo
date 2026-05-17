<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerPayoutResource\Pages;
use App\Models\Hotel;
use App\Models\PartnerPayout;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerPayoutResource extends Resource
{
    protected static ?string $model = PartnerPayout::class;

    protected static ?string $navigationIcon   = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel  = 'Chi trả đối tác';
    protected static ?string $modelLabel       = 'Kỳ chi trả';
    protected static ?string $pluralModelLabel = 'Quản lý chi trả';
    protected static ?string $navigationGroup  = 'Đối tác';
    protected static ?int    $navigationSort   = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = PartnerPayout::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin kỳ chi trả')->schema([
                Forms\Components\Select::make('partner_user_id')
                    ->label('Đối tác')
                    ->options(User::where('role', 'hotel_partner')->pluck('full_name', 'id'))
                    ->required()->searchable(),
                Forms\Components\Select::make('hotel_id')
                    ->label('Khách sạn')
                    ->options(Hotel::pluck('name', 'id'))
                    ->required()->searchable(),
                Forms\Components\DatePicker::make('period_start')
                    ->label('Từ ngày')->required()->native(false)->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('period_end')
                    ->label('Đến ngày')->required()->native(false)->displayFormat('d/m/Y'),
            ])->columns(2),

            Forms\Components\Section::make('Tài chính')->schema([
                Forms\Components\TextInput::make('gross_revenue')
                    ->label('Doanh thu gộp (₫)')->numeric()->required()->prefix('₫'),
                Forms\Components\TextInput::make('commission_rate')
                    ->label('Tỷ lệ hoa hồng (%)')->numeric()->required()->suffix('%')->default(15),
                Forms\Components\TextInput::make('commission_amount')
                    ->label('Tiền hoa hồng (₫)')->numeric()->prefix('₫'),
                Forms\Components\TextInput::make('net_amount')
                    ->label('Số tiền chi trả (₫)')->numeric()->required()->prefix('₫'),
                Forms\Components\TextInput::make('booking_count')
                    ->label('Số đơn')->numeric()->default(0),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options(PartnerPayout::statusLabels())->required()->default('pending'),
            ])->columns(3),

            Forms\Components\Section::make('Thanh toán')->schema([
                Forms\Components\TextInput::make('transfer_ref')
                    ->label('Mã chuyển khoản')->maxLength(100),
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('Ngày thanh toán')->native(false),
                Forms\Components\Textarea::make('note')->label('Ghi chú')->rows(3),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn(Builder $q) => $q->with(['partner', 'hotel']))
            ->columns([
                Tables\Columns\TextColumn::make('partner.full_name')
                    ->label('Đối tác')->searchable(),
                Tables\Columns\TextColumn::make('hotel.name')->label('Khách sạn'),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Kỳ')
                    ->formatStateUsing(fn($s, PartnerPayout $r) =>
                        $r->period_start->format('d/m') . ' – ' . $r->period_end->format('d/m/Y')
                    ),
                Tables\Columns\TextColumn::make('booking_count')->label('Đơn'),
                Tables\Columns\TextColumn::make('gross_revenue')
                    ->label('Doanh thu')
                    ->formatStateUsing(fn($s) => number_format($s, 0, ',', '.') . ' ₫'),
                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Hoa hồng')
                    ->formatStateUsing(fn($s, PartnerPayout $r) =>
                        $r->commission_rate . '% = ' . number_format($r->commission_amount, 0, ',', '.') . ' ₫'
                    ),
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Chi trả')
                    ->formatStateUsing(fn($s) => number_format($s, 0, ',', '.') . ' ₫')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')->badge()
                    ->formatStateUsing(fn($s) => PartnerPayout::statusLabels()[$s] ?? $s)
                    ->color(fn($s) => match ($s) {
                        'paid'       => 'success',
                        'processing' => 'info',
                        'pending'    => 'warning',
                        default      => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(PartnerPayout::statusLabels()),
            ])
            ->actions([
                Action::make('mark_paid')
                    ->label('Đánh dấu đã trả')->icon('heroicon-o-check')->color('success')->button()
                    ->visible(fn(PartnerPayout $r) => in_array($r->status, ['pending', 'processing']))
                    ->form([
                        Forms\Components\TextInput::make('transfer_ref')
                            ->label('Mã chuyển khoản')->required(),
                    ])
                    ->action(function (PartnerPayout $record, array $data) {
                        $record->update([
                            'status'       => 'paid',
                            'transfer_ref' => $data['transfer_ref'],
                            'processed_by' => auth()->id(),
                            'paid_at'      => now(),
                        ]);
                        Notification::make()->title('Đã thanh toán cho ' . $record->partner?->full_name)->success()->send();
                    }),
                Tables\Actions\EditAction::make()->label('Sửa')->iconButton(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()->label('Tạo kỳ chi trả')])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartnerPayouts::route('/'),
            'create' => Pages\CreatePartnerPayout::route('/create'),
            'edit'   => Pages\EditPartnerPayout::route('/{record}/edit'),
        ];
    }
}
