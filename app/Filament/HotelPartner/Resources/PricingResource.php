<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\PricingResource\Pages;
use App\Models\Room;
use App\Models\RoomPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PricingResource extends Resource
{
    protected static ?string $model = RoomPrice::class;

    protected static ?string $navigationIcon   = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel  = 'Giá đặc biệt';
    protected static ?string $modelLabel       = 'Mức giá';
    protected static ?string $pluralModelLabel = 'Quản lý giá đặc biệt';
    protected static ?string $navigationGroup  = 'Khách sạn';
    protected static ?int    $navigationSort   = 3;

    public static function getEloquentQuery(): Builder
    {
        $hotel   = auth('hotel_partner')->user()?->managedHotel;
        $roomIds = $hotel ? $hotel->rooms()->pluck('id') : collect();
        return parent::getEloquentQuery()->whereIn('room_id', $roomIds)->with(['room']);
    }

    public static function form(Form $form): Form
    {
        $hotel   = auth('hotel_partner')->user()?->managedHotel;
        $rooms   = $hotel ? Room::where('hotel_id', $hotel->id)->pluck('room_name', 'id') : collect();

        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('room_id')
                    ->label('Phòng áp dụng')
                    ->options($rooms)->required()->searchable(),
                Forms\Components\Select::make('price_type')
                    ->label('Loại giá')
                    ->options(RoomPrice::typeLabels())->required()->default('custom'),
                Forms\Components\TextInput::make('label')
                    ->label('Nhãn (VD: Tết Nguyên Đán 2027)')
                    ->maxLength(100),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Từ ngày')->required()->native(false)->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Đến ngày')->required()->native(false)->displayFormat('d/m/Y')
                    ->afterOrEqual('start_date'),
                Forms\Components\TextInput::make('price')
                    ->label('Giá đặc biệt (₫/đêm)')->numeric()->required()->prefix('₫'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('room.room_name')->label('Phòng'),
                Tables\Columns\TextColumn::make('label')->label('Nhãn')->placeholder('—'),
                Tables\Columns\TextColumn::make('price_type')
                    ->label('Loại giá')
                    ->formatStateUsing(fn($state) => RoomPrice::typeLabels()[$state] ?? $state)->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Từ ngày')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Đến ngày')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá/đêm')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
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
            'index'  => Pages\ListPricing::route('/'),
            'create' => Pages\CreatePricing::route('/create'),
            'edit'   => Pages\EditPricing::route('/{record}/edit'),
        ];
    }
}
