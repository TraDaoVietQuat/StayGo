<?php

namespace App\Filament\Resources\HotelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HotelImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'Ảnh gallery';
    protected static ?string $label = 'ảnh';
    protected static ?string $pluralLabel = 'ảnh gallery';

    // Form dùng cho Sửa ảnh đơn lẻ
    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('image')
                ->label('Ảnh')
                ->image()
                ->disk('hotel_images')
                ->directory('hotels/gallery')
                ->imageResizeMode('cover')
                ->maxSize(8192)
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('caption')
                ->label('Chú thích')
                ->maxLength(150)
                ->placeholder('VD: Hồ bơi, Sảnh chờ, Phòng Deluxe...'),

            Forms\Components\TextInput::make('sort_order')
                ->label('Thứ tự hiển thị')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->helperText('Số nhỏ = hiển thị trước'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Ảnh')
                    ->disk('hotel_images')
                    ->height(60)
                    ->width(90)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                Tables\Columns\TextColumn::make('caption')
                    ->label('Chú thích')
                    ->placeholder('Chưa có chú thích')
                    ->limit(40),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
            ])

            ->headerActions([
                // Upload nhiều ảnh cùng lúc
                Tables\Actions\Action::make('bulk_upload')
                    ->label('Upload nhiều ảnh')
                    ->icon('heroicon-o-photo')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('images')
                            ->label('Chọn ảnh (có thể chọn nhiều file)')
                            ->image()
                            ->disk('hotel_images')
                            ->directory('hotels/gallery')
                            ->multiple()
                            ->maxFiles(30)
                            ->maxSize(8192)
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        $images = $data['images'] ?? [];
                        foreach ($images as $path) {
                            $this->getOwnerRecord()->images()->create([
                                'image'      => $path,
                                'caption'    => null,
                                'sort_order' => 0,
                            ]);
                        }
                        Notification::make()
                            ->title('Đã thêm ' . count($images) . ' ảnh vào gallery')
                            ->success()
                            ->send();
                    }),

                // Thêm 1 ảnh kèm chú thích & thứ tự
                Tables\Actions\CreateAction::make()->label('Thêm 1 ảnh'),
            ])

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
}
