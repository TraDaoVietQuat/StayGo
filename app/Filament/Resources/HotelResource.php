<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Models\Hotel;
use App\Models\Location;
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
use Illuminate\Support\HtmlString;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon   = 'heroicon-o-building-office';
    protected static ?string $navigationLabel  = 'Khách sạn';
    protected static ?string $modelLabel       = 'Khách sạn';
    protected static ?string $pluralModelLabel = 'Quản lý khách sạn';
    protected static ?string $navigationGroup  = 'Nội Dung';
    protected static ?int    $navigationSort   = 1;

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin cơ bản')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên khách sạn')
                    ->required()
                    ->maxLength(150)
                    ->columnSpan(2),

                Forms\Components\Select::make('stars')
                    ->label('Số sao')
                    ->options([
                        0 => '—  Chưa phân hạng',
                        1 => '⭐  1 sao',
                        2 => '⭐⭐  2 sao',
                        3 => '⭐⭐⭐  3 sao',
                        4 => '⭐⭐⭐⭐  4 sao',
                        5 => '⭐⭐⭐⭐⭐  5 sao',
                    ])
                    ->default(0),

                Forms\Components\TextInput::make('ranking_title')
                    ->label('Danh hiệu / Hạng')
                    ->placeholder('VD: Hạng 3 trong số khách sạn được đánh giá hàng đầu tại Vũng Tàu')
                    ->maxLength(200)
                    ->columnSpan(2),

                Forms\Components\Select::make('type')
                    ->label('Loại hình')
                    ->options([
                        'hotel'        => '🏨 Khách sạn',
                        'hotel_resort' => '🏨 Khách sạn & Resort',
                        'homestay'     => '🏡 Homestay',
                        'resort'       => '🌴 Resort',
                    ])
                    ->default('hotel')
                    ->required(),
                Forms\Components\Select::make('location_id')
                    ->label('Địa điểm')
                    ->options(Location::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('address')
                    ->label('Địa chỉ')
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->rows(4)
                    ->columnSpanFull(),
            ])->columns(4),

            Forms\Components\Section::make('Hình ảnh')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Ảnh đại diện')
                    ->image()
                    ->disk('hotel_images')
                    ->directory('hotels')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->maxSize(2048)
                    ->columnSpan(2),
                Forms\Components\Select::make('cover_position')
                    ->label('Vị trí hiển thị ảnh')
                    ->helperText('Chọn phần nào của ảnh được ưu tiên hiển thị khi bị crop')
                    ->options([
                        'center center' => '⬛ Giữa (mặc định)',
                        'center top'    => '⬆️ Trên – giữa',
                        'center bottom' => '⬇️ Dưới – giữa',
                        'left top'      => '↖️ Trên – trái',
                        'right top'     => '↗️ Trên – phải',
                        'left center'   => '⬅️ Giữa – trái',
                        'right center'  => '➡️ Giữa – phải',
                        'left bottom'   => '↙️ Dưới – trái',
                        'right bottom'  => '↘️ Dưới – phải',
                    ])
                    ->default('center center')
                    ->selectablePlaceholder(false),
            ])->columns(3),

            Forms\Components\Section::make('Giá & Đánh giá')->schema([
                Forms\Components\TextInput::make('price')
                    ->label('Giá từ (đ)')
                    ->numeric()
                    ->prefix('₫'),
                Forms\Components\TextInput::make('old_price')
                    ->label('Giá gốc (đ)')
                    ->numeric()
                    ->prefix('₫'),
                Forms\Components\TextInput::make('rating')
                    ->label('Điểm đánh giá (0-10)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10)
                    ->step(0.1)
                    ->default(0),
                Forms\Components\TextInput::make('review_text')
                    ->label('Nhận xét ngắn')
                    ->maxLength(50)
                    ->placeholder('Vd: Tuyệt vời'),
                Forms\Components\TextInput::make('review_count')
                    ->label('Số lượt đánh giá')
                    ->placeholder('VD: 218, 1,5N, 2.3K')
                    ->maxLength(30)
                    ->default('0'),
            ])->columns(3),

            Forms\Components\Section::make('Check-in / Check-out & Cài đặt')->schema([
                Forms\Components\TextInput::make('checkin_time')
                    ->label('Giờ check-in')
                    ->default('14:00')
                    ->maxLength(10),
                Forms\Components\TextInput::make('checkout_time')
                    ->label('Giờ check-out')
                    ->default('12:00')
                    ->maxLength(10),
                Forms\Components\Toggle::make('is_active')
                    ->label('Hiển thị công khai')
                    ->default(true),
                Forms\Components\Toggle::make('is_weekend_deal')
                    ->label('Ưu đãi cuối tuần')
                    ->default(false),
            ])->columns(4),

            Forms\Components\Section::make('Tiện nghi & Chính sách')->schema([
                Forms\Components\CheckboxList::make('amenities')
                    ->label('Tiện nghi')
                    ->options([
                        'wifi'             => '📶 WiFi miễn phí',
                        'parking'          => '🚗 Đỗ xe miễn phí',
                        'ac'               => '❄️ Điều hòa',
                        'breakfast'        => '🍳 Bữa sáng',
                        'reception_24h'    => '🛎️ Lễ tân 24/7',
                        'private_bathroom' => '🚿 Phòng tắm riêng',
                        'tv'               => '📺 TV màn hình phẳng',
                        'cleaning'         => '🧹 Dọn phòng hàng ngày',
                        'no_smoking'       => '🚭 Không hút thuốc',
                        'airport_shuttle'  => '🚐 Đưa đón sân bay',
                        'pool'             => '🏊 Hồ bơi',
                        'child_friendly'   => '👶 Thân thiện trẻ em',
                        'restaurant'       => '🍽️ Nhà hàng',
                        'gym'              => '💪 Phòng gym',
                        'spa'              => '💆 Spa & Massage',
                        'bar'              => '🍹 Quầy bar',
                    ])
                    ->columns(4)
                    ->gridDirection('row')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cancellation_policy')
                    ->label('Chính sách hủy phòng')
                    ->rows(3)
                    ->placeholder('Vd: Hủy miễn phí trước 24h. Hủy trong vòng 24h tính phí 1 đêm đầu tiên.')
                    ->columnSpanFull(),

                Forms\Components\Section::make('📍 Vị trí & Bản đồ')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Vĩ độ (Latitude)')
                            ->numeric()
                            ->placeholder('VD: 10.346711')
                            ->helperText('Lấy từ Google Maps → chuột phải vào vị trí → copy tọa độ đầu tiên'),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Kinh độ (Longitude)')
                            ->numeric()
                            ->placeholder('VD: 107.084965')
                            ->helperText('Tọa độ thứ hai từ Google Maps'),

                        Forms\Components\Repeater::make('nearby_places')
                            ->label('Địa điểm lân cận')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên địa điểm')
                                    ->required()
                                    ->placeholder('VD: Bãi Dâu'),
                                Forms\Components\TextInput::make('distance')
                                    ->label('Khoảng cách')
                                    ->required()
                                    ->placeholder('VD: 500 m'),
                                Forms\Components\Select::make('type')
                                    ->label('Loại')
                                    ->options([
                                        'beach'      => '🏖️ Bãi biển',
                                        'food'       => '🍜 Ăn uống',
                                        'landmark'   => '🏛️ Danh lam',
                                        'transport'  => '🚌 Giao thông',
                                        'shopping'   => '🛍️ Mua sắm',
                                        'hospital'   => '🏥 Y tế',
                                        'other'      => '📍 Khác',
                                    ])
                                    ->default('other'),
                            ])
                            ->columns(3)
                            ->addActionLabel('+ Thêm địa điểm')
                            ->columnSpanFull()
                            ->collapsible(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    // ------------------------------------------------------------------
    // Table
    // ------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['location', 'rooms']))
            ->searchPlaceholder('Tên khách sạn, địa chỉ...')

            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên khách sạn')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(Hotel $record): string => $record->address ?? ''),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại hình')
                    ->formatStateUsing(fn($state) => match($state) {
                        'hotel'        => '🏨 Khách sạn',
                        'hotel_resort' => '🏨 Khách sạn & Resort',
                        'homestay'     => '🏡 Homestay',
                        'resort'       => '🌴 Resort',
                        default    => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'hotel'    => 'info',
                        'homestay' => 'success',
                        'resort'   => 'warning',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Địa điểm')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('rating_stars')
                    ->label('Đánh giá')
                    ->getStateUsing(function (Hotel $record): HtmlString {
                        $r     = (float) $record->rating;
                        $label = $record->review_text ?? '';
                        $count = $record->review_count;
                        return new HtmlString(
                            '<div class="flex flex-col gap-0.5">'
                            . '<span class="font-semibold text-amber-500">' . number_format($r, 1) . ' ★</span>'
                            . '<span class="text-xs text-gray-500">' . e($label) . ' · ' . $count . ' đánh giá</span>'
                            . '</div>'
                        );
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá từ')
                    ->formatStateUsing(fn($state) => $state ? number_format((int) $state, 0, ',', '.') . 'đ' : '—')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('rooms_count')
                    ->label('Phòng')
                    ->getStateUsing(fn(Hotel $record): int => $record->rooms->count())
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('checkin_time')
                    ->label('Check-in')
                    ->formatStateUsing(fn($state) => '🕐 ' . $state),

                Tables\Columns\TextColumn::make('checkout_time')
                    ->label('Check-out')
                    ->formatStateUsing(fn($state) => '🕑 ' . $state),

                Tables\Columns\IconColumn::make('is_weekend_deal')
                    ->label('Ưu đãi')
                    ->boolean()
                    ->trueIcon('heroicon-o-fire')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hiển thị'),
            ])

            ->filters([
                SelectFilter::make('type')
                    ->label('Loại hình')
                    ->options([
                        'hotel'        => '🏨 Khách sạn',
                        'hotel_resort' => '🏨 Khách sạn & Resort',
                        'homestay'     => '🏡 Homestay',
                        'resort'       => '🌴 Resort',
                    ])
                    ->placeholder('Tất cả loại hình'),

                SelectFilter::make('location_id')
                    ->label('Địa điểm')
                    ->options(Location::pluck('name', 'id'))
                    ->placeholder('Tất cả địa điểm'),

                TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đã ẩn'),

                TernaryFilter::make('is_weekend_deal')
                    ->label('Ưu đãi cuối tuần')
                    ->trueLabel('Có ưu đãi')
                    ->falseLabel('Không có'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)

            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa')->button(),
                Action::make('toggle_active')
                    ->label(fn(Hotel $record): string => $record->is_active ? 'Ẩn' : 'Hiện')
                    ->icon(fn(Hotel $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn(Hotel $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(function (Hotel $record): void {
                        $record->update(['is_active' => ! $record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Đã hiển thị khách sạn' : 'Đã ẩn khách sạn')
                            ->success()
                            ->send();
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\HotelResource\RelationManagers\RoomsRelationManager::class,
            \App\Filament\Resources\HotelResource\RelationManagers\HotelImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit'   => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
