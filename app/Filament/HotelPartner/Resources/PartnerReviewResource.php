<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\PartnerReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon   = 'heroicon-o-star';
    protected static ?string $navigationLabel  = 'Đánh giá';
    protected static ?string $modelLabel       = 'Đánh giá';
    protected static ?string $pluralModelLabel = 'Đánh giá của khách';
    protected static ?string $navigationGroup  = 'Đánh giá';
    protected static ?int    $navigationSort   = 1;

    public static function getEloquentQuery(): Builder
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        return parent::getEloquentQuery()
            ->where('hotel_id', $hotel?->id ?? 0)
            ->where('is_active', true)
            ->with(['user', 'booking'])
            ->latest('created_at');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Khách hàng')
                    ->description(fn(Review $r) => $r->booking?->order_code ?? ''),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Điểm')
                    ->formatStateUsing(fn($s) => str_repeat('⭐', (int)$s) . " ($s/5)")
                    ->badge()
                    ->color(fn($s) => (int)$s >= 4 ? 'success' : ((int)$s >= 3 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Nhận xét')->limit(80)->wrap(),
                Tables\Columns\TextColumn::make('partner_reply')
                    ->label('Phản hồi của bạn')->limit(60)->wrap()
                    ->placeholder('Chưa phản hồi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đánh giá')->date('d/m/Y'),
            ])
            ->actions([
                Action::make('reply')
                    ->label('Phản hồi')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->form([
                        Forms\Components\Textarea::make('reply')
                            ->label('Nội dung phản hồi')
                            ->required()->rows(4)
                            ->placeholder('Cảm ơn quý khách đã đánh giá...'),
                    ])
                    ->fillForm(fn(Review $r) => ['reply' => $r->partner_reply])
                    ->action(function (Review $record, array $data) {
                        $record->update([
                            'partner_reply'      => $data['reply'],
                            'partner_replied_at' => now(),
                        ]);
                        Notification::make()->title('Đã lưu phản hồi')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerReviews::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
