<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\PayoutResource\Pages;
use App\Models\PartnerPayout;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayoutResource extends Resource
{
    protected static ?string $model = PartnerPayout::class;

    protected static ?string $navigationIcon   = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel  = 'Doanh thu & Chi trả';
    protected static ?string $modelLabel       = 'Kỳ thanh toán';
    protected static ?string $pluralModelLabel = 'Doanh thu & Chi trả';
    protected static ?string $navigationGroup  = 'Đặt phòng & Doanh thu';
    protected static ?int    $navigationSort   = 3;

    public static function getEloquentQuery(): Builder
    {
        $userId = auth('hotel_partner')->id();
        return parent::getEloquentQuery()->where('partner_user_id', $userId)->with(['hotel']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Kỳ thanh toán')
                    ->formatStateUsing(fn(PartnerPayout $record) =>
                        $record->period_start->format('d/m/Y') . ' → ' . $record->period_end->format('d/m/Y')
                    ),
                Tables\Columns\TextColumn::make('booking_count')->label('Số đơn'),
                Tables\Columns\TextColumn::make('gross_revenue')
                    ->label('Doanh thu')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Hoa hồng')
                    ->formatStateUsing(fn(PartnerPayout $record) =>
                        $record->commission_rate . '% = ' . number_format($record->commission_amount, 0, ',', '.') . ' ₫'
                    ),
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Nhận về')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')->badge()
                    ->formatStateUsing(fn($state) => PartnerPayout::statusLabels()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'paid'       => 'success',
                        'processing' => 'info',
                        'pending'    => 'warning',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Ngày thanh toán')->dateTime('d/m/Y')->placeholder('—'),
            ])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayouts::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
