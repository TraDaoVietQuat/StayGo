<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon   = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel  = 'Quản lý phòng';
    protected static ?string $modelLabel       = 'Phòng';
    protected static ?string $pluralModelLabel = 'Quản lý phòng';
    protected static ?string $navigationGroup  = 'Khách sạn';
    protected static ?int    $navigationSort   = 2;

    public static function getEloquentQuery(): Builder
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        return parent::getEloquentQuery()->where('hotel_id', $hotel?->id ?? 0);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin phòng')->schema([
                Forms\Components\TextInput::make('room_name')
                    ->label('Tên loại phòng')->required()->maxLength(100),
                Forms\Components\TextInput::make('package_name')
                    ->label('Tên gói')->maxLength(100),
                Forms\Components\Select::make('bed_type')
                    ->label('Loại giường')
                    ->options([
                        'single'   => 'Giường đơn',
                        'double'   => 'Giường đôi',
                        'twin'     => '2 giường đơn',
                        'king'     => 'Giường King',
                        'queen'    => 'Giường Queen',
                        'bunk'     => 'Giường tầng',
                        'suite'    => 'Phòng Suite',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Số phòng')->numeric()->default(1)->minValue(1),
                Forms\Components\TextInput::make('max_guests')
                    ->label('Người lớn tối đa')->numeric()->default(2),
                Forms\Components\TextInput::make('max_children')
                    ->label('Trẻ em tối đa')->numeric()->default(1),
                Forms\Components\TextInput::make('area')
                    ->label('Diện tích (m²)')->numeric(),
                Forms\Components\TextInput::make('room_badge')
                    ->label('Badge (VD: Phổ biến, Mới)')->maxLength(50),
            ])->columns(4),

            Forms\Components\Section::make('Giá phòng')->schema([
                Forms\Components\TextInput::make('price')
                    ->label('Giá qua đêm (₫)')->numeric()->required()->prefix('₫'),
                Forms\Components\TextInput::make('day_price')
                    ->label('Giá qua ngày (₫)')->numeric()->prefix('₫'),
            ])->columns(2),

            Forms\Components\Section::make('Tiện nghi & Gói')->schema([
                Forms\Components\CheckboxList::make('room_amenities')
                    ->label('Tiện nghi phòng')
                    ->options([
                        'ac'         => 'Điều hòa',
                        'tv'         => 'TV màn hình phẳng',
                        'wifi'       => 'WiFi',
                        'minibar'    => 'Minibar',
                        'bathtub'    => 'Bồn tắm',
                        'shower'     => 'Vòi hoa sen',
                        'balcony'    => 'Ban công',
                        'seaview'    => 'View biển',
                        'safe'       => 'Két an toàn',
                        'kettle'     => 'Ấm đun nước',
                        'hair_dryer' => 'Máy sấy tóc',
                        'desk'       => 'Bàn làm việc',
                    ])
                    ->columns(3)->columnSpan(2),
                Forms\Components\Repeater::make('benefits')
                    ->label('Quyền lợi kèm theo')
                    ->schema([
                        Forms\Components\TextInput::make('item')->label('Quyền lợi')->required(),
                    ])
                    ->addActionLabel('Thêm quyền lợi')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Chính sách')->schema([
                Forms\Components\Textarea::make('cancellation_policy')
                    ->label('Chính sách hủy phòng')->rows(3),
                Forms\Components\Toggle::make('is_refundable')->label('Có thể hoàn tiền'),
                Forms\Components\Toggle::make('is_tax_included')->label('Đã bao gồm thuế'),
                Forms\Components\Toggle::make('is_sale')->label('Đang khuyến mãi'),
            ])->columns(2),

            Forms\Components\Section::make('Ảnh phòng')->schema([
                Forms\Components\Repeater::make('image')
                    ->label('Danh sách ảnh (URL hoặc đường dẫn)')
                    ->schema([
                        Forms\Components\TextInput::make('url')->label('URL ảnh')->required(),
                    ])
                    ->addActionLabel('Thêm ảnh')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('room_name')->label('Tên phòng')->searchable(),
                Tables\Columns\TextColumn::make('bed_type')->label('Loại giường')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'double' => 'Đôi', 'twin' => '2 đơn', 'king' => 'King',
                        'queen'  => 'Queen', 'suite' => 'Suite', default => ucfirst($state ?? ''),
                    })->badge(),
                Tables\Columns\TextColumn::make('quantity')->label('Số phòng'),
                Tables\Columns\TextColumn::make('max_guests')->label('Khách tối đa'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá/đêm')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                Tables\Columns\IconColumn::make('is_refundable')->label('Hoàn tiền')->boolean(),
                Tables\Columns\IconColumn::make('is_sale')->label('Đang sale')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->headerActions([])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit'   => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        $data['hotel_id'] = $hotel?->id;
        return $data;
    }
}
