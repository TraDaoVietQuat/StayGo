<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\HotelProfileResource\Pages;
use App\Models\Hotel;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HotelProfileResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon   = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel  = 'Hồ sơ khách sạn';
    protected static ?string $modelLabel       = 'Khách sạn';
    protected static ?string $pluralModelLabel = 'Hồ sơ khách sạn';
    protected static ?string $navigationGroup  = 'Khách sạn';
    protected static ?int    $navigationSort   = 1;

    public static function getEloquentQuery(): Builder
    {
        $userId = auth('hotel_partner')->id();
        return parent::getEloquentQuery()->where('partner_user_id', $userId)->with(['location']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin cơ bản')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên khách sạn / resort')
                    ->required()->maxLength(200),
                Forms\Components\Select::make('location_id')
                    ->label('Địa điểm')
                    ->options(Location::where('is_active', true)->pluck('name', 'id'))
                    ->required()->searchable(),
                Forms\Components\Select::make('type')
                    ->label('Loại hình')
                    ->options(['hotel' => 'Khách sạn', 'resort' => 'Resort'])
                    ->required(),
                Forms\Components\Select::make('stars')
                    ->label('Hạng sao')
                    ->options([1=>'1 sao',2=>'2 sao',3=>'3 sao',4=>'4 sao',5=>'5 sao'])
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->label('Địa chỉ đầy đủ')
                    ->required()->maxLength(255)->columnSpan(2),
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->rows(4)->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Chính sách & Tiện nghi')->schema([
                Forms\Components\TextInput::make('checkin_time')
                    ->label('Giờ check-in')->default('14:00'),
                Forms\Components\TextInput::make('checkout_time')
                    ->label('Giờ check-out')->default('12:00'),
                Forms\Components\Textarea::make('cancellation_policy')
                    ->label('Chính sách hủy phòng')->rows(3)->columnSpan(2),
                Forms\Components\CheckboxList::make('amenities')
                    ->label('Tiện nghi khách sạn')
                    ->options([
                        'wifi'        => 'WiFi miễn phí',
                        'pool'        => 'Hồ bơi',
                        'parking'     => 'Bãi đậu xe',
                        'gym'         => 'Phòng gym',
                        'spa'         => 'Spa',
                        'restaurant'  => 'Nhà hàng',
                        'bar'         => 'Bar / Quầy bar',
                        'beach'       => 'Bãi biển riêng',
                        'airport'     => 'Đưa đón sân bay',
                        'laundry'     => 'Dịch vụ giặt ủi',
                        'concierge'   => 'Lễ tân 24/7',
                        'kids'        => 'Khu vui chơi trẻ em',
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Vị trí bản đồ')->schema([
                Forms\Components\TextInput::make('latitude')
                    ->label('Vĩ độ (Latitude)')->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->label('Kinh độ (Longitude)')->numeric(),
            ])->columns(2),

            Forms\Components\Section::make('Trạng thái hiển thị')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('Hiển thị trên website')
                    ->helperText('Bật để khách có thể đặt phòng'),
                Forms\Components\Toggle::make('is_weekend_deal')
                    ->label('Ưu đãi cuối tuần'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tên khách sạn'),
                Tables\Columns\TextColumn::make('location.name')->label('Địa điểm'),
                Tables\Columns\TextColumn::make('stars')->label('Hạng sao')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', (int)$state)),
                Tables\Columns\IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Chỉnh sửa hồ sơ'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotelProfile::route('/'),
            'edit'  => Pages\EditHotelProfile::route('/{record}/edit'),
        ];
    }
}
