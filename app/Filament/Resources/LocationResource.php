<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon  = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Địa điểm';
    protected static ?string $modelLabel      = 'địa điểm';
    protected static ?string $pluralModelLabel = 'Địa điểm';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Tên địa điểm')
                ->required()
                ->maxLength(100)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('description')
                ->label('Mô tả')
                ->rows(3)
                ->maxLength(500)
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('image')
                ->label('Ảnh đại diện')
                ->image()
                ->directory('locations')
                ->imageResizeMode('cover')
                ->maxSize(4096)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên địa điểm')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('hotels_count')
                    ->label('Khách sạn')
                    ->counts('hotels')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(60)
                    ->placeholder('Chưa có mô tả')
                    ->toggleable(),
            ])
            ->defaultSort('name')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
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
            'index'  => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit'   => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
