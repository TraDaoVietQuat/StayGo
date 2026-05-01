<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Hotel;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon   = 'heroicon-o-star';
    protected static ?string $navigationLabel  = 'Đánh giá';
    protected static ?string $modelLabel       = 'Đánh giá';
    protected static ?string $pluralModelLabel = 'Quản lý đánh giá';
    protected static ?string $navigationGroup  = 'Giao Dịch';
    protected static ?int    $navigationSort   = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = Review::where('is_active', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin đánh giá')->schema([
                Forms\Components\Select::make('hotel_id')
                    ->label('Khách sạn')
                    ->options(Hotel::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('rating')
                    ->label('Điểm đánh giá')
                    ->options([
                        1 => '⭐ 1 — Rất tệ',
                        2 => '⭐⭐ 2 — Tệ',
                        3 => '⭐⭐⭐ 3 — Bình thường',
                        4 => '⭐⭐⭐⭐ 4 — Tốt',
                        5 => '⭐⭐⭐⭐⭐ 5 — Xuất sắc',
                        6 => '⭐⭐⭐⭐⭐⭐ 6',
                        7 => '7',
                        8 => '8',
                        9 => '9',
                        10 => '10 — Hoàn hảo',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Hiển thị')
                    ->default(true),
            ])->columns(3),
            Forms\Components\Textarea::make('comment')
                ->label('Nội dung đánh giá')
                ->required()
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    // ------------------------------------------------------------------
    // Table
    // ------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['hotel', 'user', 'booking']))
            ->searchPlaceholder('Tên khách sạn, người dùng...')

            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Khách sạn')
                    ->searchable()
                    ->limit(30)
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Người đánh giá')
                    ->description(fn(Review $record): string => $record->user?->email ?? '—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating_stars')
                    ->label('Điểm')
                    ->getStateUsing(function (Review $record): HtmlString {
                        $r     = (int) $record->rating;
                        $stars = str_repeat('★', min($r, 10)) . str_repeat('☆', max(0, 10 - $r));
                        $color = $r >= 8 ? 'text-green-600' : ($r >= 6 ? 'text-amber-500' : 'text-red-500');
                        return new HtmlString(
                            '<div class="flex flex-col gap-0.5">'
                            . '<span class="font-bold ' . $color . '">' . $r . '/10</span>'
                            . '<span class="text-xs text-amber-400">' . $stars . '</span>'
                            . '</div>'
                        );
                    })
                    ->html()
                    ->sortable('rating'),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Nội dung')
                    ->limit(60)
                    ->tooltip(fn(Review $record): string => $record->comment),

                Tables\Columns\TextColumn::make('booking.order_code')
                    ->label('Mã đặt phòng')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đánh giá')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hiển thị'),
            ])

            ->filters([
                SelectFilter::make('hotel_id')
                    ->label('Khách sạn')
                    ->options(Hotel::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Tất cả khách sạn'),

                SelectFilter::make('rating')
                    ->label('Điểm đánh giá')
                    ->options([
                        '10' => '10 ★',
                        '9'  => '9 ★',
                        '8'  => '8 ★',
                        '7'  => '7 ★',
                        '6'  => '6 ★',
                        '5'  => '5 ★',
                        '4'  => '4 ★',
                        '3'  => '3 ★',
                        '2'  => '2 ★',
                        '1'  => '1 ★',
                    ])
                    ->placeholder('Tất cả điểm'),

                TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đã ẩn'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)

            ->actions([
                Action::make('toggle_active')
                    ->label(fn(Review $record): string => $record->is_active ? 'Ẩn' : 'Duyệt')
                    ->icon(fn(Review $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn(Review $record): string => $record->is_active ? 'warning' : 'success')
                    ->button()
                    ->action(function (Review $record): void {
                        $record->update(['is_active' => ! $record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Đã duyệt đánh giá' : 'Đã ẩn đánh giá')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('')->tooltip('Chỉnh sửa')->iconButton(),
                Tables\Actions\DeleteAction::make()->label('')->tooltip('Xóa')->iconButton(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Duyệt đã chọn')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Duyệt hàng loạt')
                        ->modalDescription('Hiện thị tất cả đánh giá đã chọn?')
                        ->modalSubmitActionLabel('Duyệt')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(fn($r) => $r->update(['is_active' => true]));
                            Notification::make()->title('Đã duyệt ' . $records->count() . ' đánh giá')->success()->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_hide')
                        ->label('Ẩn đã chọn')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Ẩn hàng loạt')
                        ->modalDescription('Ẩn tất cả đánh giá đã chọn?')
                        ->modalSubmitActionLabel('Ẩn')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(fn($r) => $r->update(['is_active' => false]));
                            Notification::make()->title('Đã ẩn ' . $records->count() . ' đánh giá')->warning()->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),
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
            'index'  => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit'   => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
