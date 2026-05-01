<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon   = 'heroicon-o-home';
    protected static ?string $navigationLabel  = 'Phòng';
    protected static ?string $modelLabel       = 'Phòng';
    protected static ?string $pluralModelLabel = 'Quản lý phòng';
    protected static ?string $navigationGroup  = 'Nội Dung';
    protected static ?int    $navigationSort   = 2;

    public static function roomTypeOptions(): array
    {
        return [
            'Phòng Tiêu Chuẩn' => 'Phòng Tiêu Chuẩn',
            'Phòng Cao Cấp'     => 'Phòng Cao Cấp',
            'Phòng Hạng Sang'   => 'Phòng Hạng Sang',
            'Phòng Suite'       => 'Phòng Suite',
            'Phòng Gia Đình'    => 'Phòng Gia Đình',
            'Phòng Đơn'         => 'Phòng Đơn',
            'Phòng Đôi'         => 'Phòng Đôi',
        ];
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin phòng')->schema([
                Forms\Components\Select::make('hotel_id')
                    ->label('Khách sạn')
                    ->options(Hotel::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('room_name')
                    ->label('Loại phòng')
                    ->options(static::roomTypeOptions())
                    ->placeholder('— Chọn loại phòng hoặc nhập tên tuỳ chỉnh bên dưới —')
                    ->nullable()
                    ->helperText('Chọn từ danh sách hoặc bỏ qua và nhập tên tuỳ chỉnh'),

                Forms\Components\TextInput::make('room_name_custom')
                    ->label('Tên tuỳ chỉnh')
                    ->maxLength(100)
                    ->placeholder('VD: Phòng Bungalow, Phòng View Rừng...')
                    ->helperText('Nếu nhập tên tuỳ chỉnh sẽ ưu tiên hơn loại phòng ở trên')
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                        if ($record && ! in_array($record->room_name, array_keys(static::roomTypeOptions()))) {
                            $component->state($record->room_name);
                        }
                    }),
            ])->columns(2),

            Forms\Components\Section::make('Giá & Số lượng')->schema([
                Forms\Components\TextInput::make('price')
                    ->label('Giá phòng/đêm (đ)')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('₫')
                    ->placeholder('VD: 800000'),

                Forms\Components\TextInput::make('day_price')
                    ->label('Giá qua ngày (đ)')
                    ->numeric()
                    ->nullable()
                    ->minValue(0)
                    ->prefix('₫')
                    ->placeholder('Để trống nếu không có gói qua ngày')
                    ->helperText('Giá áp dụng khi khách chọn "Chỗ ở Qua ngày"'),

                Forms\Components\TextInput::make('quantity')
                    ->label('Số lượng phòng')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->placeholder('VD: 5'),

                Forms\Components\TextInput::make('max_guests')
                    ->label('Số khách tối đa')
                    ->numeric()
                    ->nullable()
                    ->minValue(1)
                    ->placeholder('VD: 2')
                    ->helperText('Số khách tối đa có thể ở trong phòng'),

                Forms\Components\TextInput::make('bed_type')
                    ->label('Loại giường')
                    ->maxLength(50)
                    ->placeholder('VD: 1 giường đôi, 2 giường đơn'),
            ])->columns(3),

            Forms\Components\Section::make('Ảnh phòng')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Ảnh phòng')
                    ->image()
                    ->directory('rooms')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->maxSize(5120)
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
            ->defaultSort('hotel_id', 'asc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with('hotel'))
            ->searchPlaceholder('Tên khách sạn, loại phòng...')

            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->height(52)
                    ->width(76),

                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Khách sạn')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(30),

                Tables\Columns\TextColumn::make('room_name')
                    ->label('Loại phòng')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('bed_type')
                    ->label('Giường')
                    ->placeholder('—')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá/đêm')
                    ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.') . 'đ')
                    ->sortable()
                    ->color('success')
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('day_price')
                    ->label('Giá/ngày')
                    ->formatStateUsing(fn($state) => $state ? number_format((int) $state, 0, ',', '.') . 'đ' : '—')
                    ->color('warning')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Khách tối đa')
                    ->formatStateUsing(fn($state) => $state ? $state . ' khách' : '—')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Số phòng')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Đang có booking')
                    ->getStateUsing(function (Room $record): HtmlString {
                        $active = Booking::where('room_id', $record->id)
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->count();
                        $color = $active > 0 ? 'text-orange-500' : 'text-gray-400';
                        return new HtmlString(
                            '<span class="font-semibold ' . $color . '">' . $active . '</span>'
                        );
                    })
                    ->html()
                    ->label('Booking'),
            ])

            ->filters([
                SelectFilter::make('hotel_id')
                    ->label('Khách sạn')
                    ->options(Hotel::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Tất cả khách sạn'),

                SelectFilter::make('room_name')
                    ->label('Loại phòng')
                    ->options(static::roomTypeOptions())
                    ->placeholder('Tất cả loại phòng'),
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa')->button(),

                Action::make('delete_safe')
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Xóa phòng')
                    ->modalDescription(fn(Room $record): string =>
                        'Xóa phòng "' . $record->room_name . '"? Hành động này không thể hoàn tác.'
                    )
                    ->modalSubmitActionLabel('Xóa phòng')
                    ->action(function (Room $record): void {
                        $activeBookings = Booking::where('room_id', $record->id)
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->count();

                        if ($activeBookings > 0) {
                            Notification::make()
                                ->title('Không thể xóa')
                                ->body("Phòng đang có {$activeBookings} booking chưa hoàn thành. Hủy các booking trước.")
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->delete();
                        Notification::make()->title('Đã xóa phòng')->success()->send();
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit'   => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
