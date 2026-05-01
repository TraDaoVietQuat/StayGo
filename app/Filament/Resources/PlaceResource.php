<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Models\Location;
use App\Models\Place;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon   = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel  = 'Địa điểm';
    protected static ?string $modelLabel       = 'Địa điểm';
    protected static ?string $pluralModelLabel = 'Quản lý địa điểm';
    protected static ?string $navigationGroup  = 'Nội Dung';
    protected static ?int    $navigationSort   = 3;

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin địa điểm')->schema([
                Forms\Components\Select::make('location_id')
                    ->label('Khu vực')
                    ->options(Location::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Chọn khu vực'),

                Forms\Components\TextInput::make('name')
                    ->label('Tên địa điểm')
                    ->required()
                    ->maxLength(200)
                    ->placeholder('VD: Hồ Toong Zơ Ri'),
            ])->columns(2),

            Forms\Components\Section::make('Nội dung')->schema([
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->rows(4)
                    ->nullable()
                    ->columnSpanFull()
                    ->placeholder('Mô tả ngắn về địa điểm tham quan...'),

                Forms\Components\FileUpload::make('image')
                    ->label('Ảnh đại diện')
                    ->image()
                    ->directory('places')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->maxSize(5120)
                    ->nullable()
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
            ->defaultSort('location_id', 'asc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with('location'))
            ->searchPlaceholder('Tìm tên địa điểm, khu vực...')

            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->height(48)
                    ->width(72)
                    ->defaultImageUrl(asset('assets/images/placeholder.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên địa điểm')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(40),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Khu vực')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(60)
                    ->color('gray')
                    ->placeholder('Chưa có mô tả'),
            ])

            ->filters([
                SelectFilter::make('location_id')
                    ->label('Khu vực')
                    ->options(Location::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Tất cả khu vực'),
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa')->button(),
                Tables\Actions\DeleteAction::make()->label('Xóa')->button()->color('danger'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit'   => Pages\EditPlace::route('/{record}/edit'),
        ];
    }
}
