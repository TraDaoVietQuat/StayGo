<?php

namespace App\Filament\Resources\HotelResource\RelationManagers;

use App\Models\Booking;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';
    protected static ?string $title = 'Danh sách phòng';
    protected static ?string $label = 'phòng';
    protected static ?string $pluralLabel = 'phòng';

    public function form(Form $form): Form
    {
        $roomTypes = [
            'Phòng Tiêu Chuẩn' => 'Phòng Tiêu Chuẩn',
            'Phòng Cao Cấp'     => 'Phòng Cao Cấp',
            'Phòng Hạng Sang'   => 'Phòng Hạng Sang',
            'Phòng Suite'       => 'Phòng Suite',
            'Phòng Gia Đình'    => 'Phòng Gia Đình',
            'Phòng Đơn'         => 'Phòng Đơn',
            'Phòng Đôi'         => 'Phòng Đôi',
        ];

        return $form->schema([
            Forms\Components\Select::make('room_name')
                ->label('Loại phòng')
                ->options($roomTypes)
                ->placeholder('Chọn hoặc nhập tên tuỳ chỉnh bên dưới')
                ->nullable(),

            Forms\Components\TextInput::make('room_name_custom')
                ->label('Tên tuỳ chỉnh (ưu tiên hơn)')
                ->maxLength(100)
                ->placeholder('VD: Phòng Bungalow, Phòng View Rừng...')
                ->afterStateHydrated(function (Forms\Components\TextInput $component, $record) {
                    if ($record && ! in_array($record->room_name, [
                        'Phòng Tiêu Chuẩn', 'Phòng Cao Cấp', 'Phòng Hạng Sang',
                        'Phòng Suite', 'Phòng Gia Đình', 'Phòng Đơn', 'Phòng Đôi',
                    ])) {
                        $component->state($record->room_name);
                    }
                }),

            Forms\Components\TextInput::make('package_name')
                ->label('Tên gói (để trống nếu chỉ có 1 gói)')
                ->maxLength(150)
                ->placeholder('VD: Bữa sáng cho 2 người, Bữa sáng & Bữa trưa...')
                ->helperText('Dùng khi 1 loại phòng có nhiều lựa chọn giá/dịch vụ khác nhau')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('price')
                ->label('Giá/đêm (đ)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->prefix('₫'),

            Forms\Components\TextInput::make('day_price')
                ->label('Giá/ngày (đ)')
                ->numeric()
                ->nullable()
                ->minValue(0)
                ->prefix('₫')
                ->helperText('Giá cho chỗ ở qua ngày (thường thấp hơn giá đêm). Để trống nếu không hỗ trợ ở qua ngày.'),

            Forms\Components\TextInput::make('quantity')
                ->label('Số lượng phòng')
                ->numeric()
                ->required()
                ->minValue(1)
                ->default(1),

            Forms\Components\Fieldset::make('Sức chứa khách')
                ->schema([
                    Forms\Components\TextInput::make('max_guests')
                        ->label('Người lớn')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(2)
                        ->suffix('người'),

                    Forms\Components\TextInput::make('max_children')
                        ->label('Trẻ em')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->suffix('người')
                        ->helperText('Nhập 0 nếu không có trẻ em.'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('area')
                ->label('Diện tích (m²)')
                ->numeric()
                ->nullable()
                ->minValue(1)
                ->suffix('m²')
                ->placeholder('VD: 30'),

            Forms\Components\Toggle::make('is_refundable')
                ->label('Được hoàn tiền')
                ->helperText('Bật nếu khách có thể huỷ và hoàn tiền. Tắt = không hoàn tiền.')
                ->default(false),

            Forms\Components\Textarea::make('cancellation_policy')
                ->label('Chính sách hủy phòng')
                ->placeholder('VD: Hủy miễn phí trước 24h. Hủy trong 24h tính phí 1 đêm đầu.')
                ->helperText('Điền để hiển thị "Áp dụng chính sách hủy phòng ⓘ" với tooltip chi tiết. Để trống nếu dùng trạng thái hoàn tiền đơn giản.')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_sale')
                ->label('Sale Lễ')
                ->helperText('Bật để hiển thị badge "Sale Lễ" trên thẻ phòng.')
                ->default(false),

            Forms\Components\TextInput::make('room_badge')
                ->label('🏷️ Nhãn nổi bật trên ảnh')
                ->maxLength(80)
                ->placeholder('VD: Phòng view đẹp giá tốt, Lựa chọn hàng đầu...')
                ->helperText('Hiển thị dải nhãn màu xanh phía dưới ảnh phòng. Để trống = không hiển thị.')
                ->nullable()
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_tax_included')
                ->label('Đã bao gồm thuế & phí')
                ->helperText('Bật = "Đã gồm thuế & phí". Tắt = "Chưa bao gồm thuế & phí" (hiển thị màu cam).')
                ->default(true),

            Forms\Components\TextInput::make('bed_type')
                ->label('Loại giường')
                ->maxLength(50)
                ->placeholder('VD: 1 giường đôi'),

            Forms\Components\Repeater::make('benefits')
                ->label('🎁 Đặc quyền / Ưu đãi đi kèm')
                ->schema([
                    Forms\Components\TextInput::make('text')
                        ->label('Nội dung')
                        ->required()
                        ->placeholder('VD: Miễn phí buffet sáng từ 6:30 – 10:00')
                        ->maxLength(200),
                ])
                ->addActionLabel('+ Thêm ưu đãi')
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),

            Forms\Components\Repeater::make('room_notes')
                ->label('📝 Về phòng này')
                ->schema([
                    Forms\Components\TextInput::make('text')
                        ->label('Nội dung')
                        ->required()
                        ->placeholder('VD: 1 giường King, Nhìn ra biển, Không hút thuốc...')
                        ->maxLength(200),
                ])
                ->addActionLabel('+ Thêm mục')
                ->defaultItems(0)
                ->collapsible()
                ->helperText('Mỗi dòng là một đặc điểm nổi bật của phòng. Hiển thị dạng danh sách trong modal chi tiết.')
                ->columnSpanFull(),

            Forms\Components\Section::make('🏨 Tiện nghi phòng')
                ->schema([
                    Forms\Components\CheckboxList::make('amen_info')
                        ->label('📋 Thông tin phòng')
                        ->options([
                            'city_view'        => 'View thành phố',
                            'garden_view'      => 'View vườn/cây xanh',
                            'pool_view'        => 'View hồ bơi',
                            'mountain_view'    => 'View núi',
                            'high_floor'       => 'Tầng cao',
                            'private_entrance' => 'Lối vào riêng',
                        ])
                        ->columns(3)
                        ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                            $component->state(array_values(array_intersect((array)($record?->room_amenities ?? []), ['city_view','garden_view','pool_view','mountain_view','high_floor','private_entrance'])));
                        }),

                    Forms\Components\CheckboxList::make('amen_highlight')
                        ->label('💝 Tính năng phòng bạn thích')
                        ->options([
                            'sea_view'    => 'View biển',
                            'balcony'     => 'Ban công',
                            'pool_access' => 'Hồ bơi riêng',
                        ])
                        ->columns(3)
                        ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                            $component->state(array_values(array_intersect((array)($record?->room_amenities ?? []), ['sea_view','balcony','pool_access'])));
                        }),

                    Forms\Components\CheckboxList::make('amen_basic')
                        ->label('🏠 Tiện nghi cơ bản')
                        ->options([
                            'non_smoking'  => 'Phòng không hút thuốc',
                            'waiting_area' => 'Khu vực chờ',
                            'breakfast'    => 'Bữa sáng',
                        ])
                        ->columns(3)
                        ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                            $component->state(array_values(array_intersect((array)($record?->room_amenities ?? []), ['non_smoking','waiting_area','breakfast'])));
                        }),

                    Forms\Components\CheckboxList::make('amen_room')
                        ->label('🛏 Tiện nghi phòng')
                        ->options([
                            'ac'                 => 'Máy lạnh',
                            'fan'                => 'Quạt',
                            'wifi'               => 'WiFi miễn phí',
                            'tv'                 => 'TV',
                            'minibar'            => 'Quầy bar mini',
                            'fridge'             => 'Tủ lạnh',
                            'safe'               => 'Két an toàn',
                            'desk'               => 'Bàn làm việc',
                            'wardrobe'           => 'Tủ quần áo',
                            'kettle'             => 'Ấm đun nước',
                            'ironing'            => 'Bàn ủi',
                            'blackout_curtains'  => 'Rèm che sáng',
                            'free_bottled_water' => 'Nước đóng chai miễn phí',
                        ])
                        ->columns(3)
                        ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                            $component->state(array_values(array_intersect((array)($record?->room_amenities ?? []), ['ac','fan','wifi','tv','minibar','fridge','safe','desk','wardrobe','kettle','ironing','blackout_curtains','free_bottled_water'])));
                        }),

                    Forms\Components\CheckboxList::make('amen_bathroom')
                        ->label('🚿 Tiện nghi phòng tắm')
                        ->options([
                            'private_bathroom' => 'Phòng tắm riêng',
                            'hot_water'        => 'Nước nóng',
                            'shower'           => 'Vòi tắm thường',
                            'shower_standing'  => 'Vòi tắm đứng',
                            'bathtub'          => 'Bồn tắm',
                            'toiletries'       => 'Bộ vệ sinh cá nhân',
                            'hair_dryer'       => 'Máy sấy tóc',
                            'bathrobe'         => 'Áo choàng tắm',
                            'slippers'         => 'Dép đi trong phòng',
                        ])
                        ->columns(3)
                        ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                            $component->state(array_values(array_intersect((array)($record?->room_amenities ?? []), ['private_bathroom','hot_water','shower','shower_standing','bathtub','toiletries','hair_dryer','bathrobe','slippers'])));
                        }),
                ])
                ->columnSpanFull()
                ->collapsible(),

            Forms\Components\FileUpload::make('image')
                ->label('Ảnh phòng (tối đa 9 ảnh)')
                ->image()
                ->multiple()
                ->maxFiles(9)
                ->reorderable()
                ->nullable()
                ->helperText('Chỉ cần upload ảnh cho 1 gói trong nhóm — các gói còn lại sẽ dùng chung ảnh đó.')
                ->disk('hotel_images')
                ->directory('rooms')
                ->maxSize(5120)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('room_name')
            ->defaultSort('price', 'asc')
            ->groups([
                Tables\Grouping\Group::make('room_name')
                    ->label('Loại phòng')
                    ->collapsible(),
            ])
            ->defaultGroup('room_name')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->disk('hotel_images')
                    ->height(44)
                    ->width(64)
                    ->getStateUsing(function (Room $record): ?string {
                        $imgs = $record->images_list;
                        return $imgs[0] ?? null;
                    }),

                Tables\Columns\TextColumn::make('package_name')
                    ->label('Tên gói')
                    ->placeholder('(gói duy nhất)')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Khách')
                    ->formatStateUsing(fn($state) => $state . ' khách')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\IconColumn::make('is_refundable')
                    ->label('Hoàn tiền')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_sale')
                    ->label('Sale Lễ')
                    ->boolean()
                    ->trueIcon('heroicon-o-tag')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('area')
                    ->label('Diện tích')
                    ->formatStateUsing(fn($state) => $state ? $state . ' m²' : '—')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('bed_type')
                    ->label('Giường')
                    ->placeholder('—')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá/đêm')
                    ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.') . 'đ')
                    ->color('success'),

                Tables\Columns\TextColumn::make('day_price')
                    ->label('Giá/ngày')
                    ->formatStateUsing(fn($state) => $state ? number_format((int) $state, 0, ',', '.') . 'đ' : '—')
                    ->color('warning'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Số phòng')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('active_bookings')
                    ->label('Booking đang chạy')
                    ->getStateUsing(fn(Room $record): int =>
                        Booking::where('room_id', $record->id)->whereIn('status', ['pending', 'confirmed'])->count()
                    )
                    ->badge()
                    ->color(fn($state): string => $state > 0 ? 'warning' : 'gray'),
            ])

            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm phòng')
                    ->mutateFormDataUsing(function (array $data): array {
                        if (!empty($data['room_name_custom'])) {
                            $data['room_name'] = $data['room_name_custom'];
                        }
                        unset($data['room_name_custom']);
                        $data['room_amenities'] = array_values(array_unique(array_merge(
                            $data['amen_info']      ?? [],
                            $data['amen_highlight'] ?? [],
                            $data['amen_basic']     ?? [],
                            $data['amen_room']      ?? [],
                            $data['amen_bathroom']  ?? [],
                        )));
                        unset($data['amen_info'], $data['amen_highlight'], $data['amen_basic'], $data['amen_room'], $data['amen_bathroom']);
                        return $data;
                    }),
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->mutateFormDataUsing(function (array $data): array {
                        if (!empty($data['room_name_custom'])) {
                            $data['room_name'] = $data['room_name_custom'];
                        }
                        unset($data['room_name_custom']);
                        $data['room_amenities'] = array_values(array_unique(array_merge(
                            $data['amen_info']      ?? [],
                            $data['amen_highlight'] ?? [],
                            $data['amen_basic']     ?? [],
                            $data['amen_room']      ?? [],
                            $data['amen_bathroom']  ?? [],
                        )));
                        unset($data['amen_info'], $data['amen_highlight'], $data['amen_basic'], $data['amen_room'], $data['amen_bathroom']);
                        return $data;
                    }),

                Action::make('delete_safe')
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa phòng')
                    ->modalDescription(fn(Room $record): string =>
                        'Xóa phòng "' . $record->room_name . '"?'
                    )
                    ->action(function (Room $record): void {
                        $active = Booking::where('room_id', $record->id)
                            ->whereIn('status', ['pending', 'confirmed'])->count();
                        if ($active > 0) {
                            Notification::make()->title('Không thể xóa')->body("Đang có {$active} booking chưa hoàn thành.")->danger()->send();
                            return;
                        }
                        $record->delete();
                        Notification::make()->title('Đã xóa phòng')->success()->send();
                    }),
            ]);
    }
}
