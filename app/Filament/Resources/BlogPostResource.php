<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon   = 'heroicon-o-map';
    protected static ?string $navigationLabel  = 'Cẩm nang';
    protected static ?string $modelLabel       = 'Cẩm nang du lịch';
    protected static ?string $pluralModelLabel = 'Cẩm nang du lịch';
    protected static ?string $navigationGroup  = 'Nội Dung';
    protected static ?int    $navigationSort   = 2;

    public static function categoryOptions(): array
    {
        return [
            'Đà Lạt'    => '🌿 Đà Lạt',
            'Nha Trang'  => '🌊 Nha Trang',
            'Vũng Tàu'   => '🏖️ Vũng Tàu',
            'Đà Nẵng'    => '🌉 Đà Nẵng',
            'travel'     => '✈️ Du lịch',
            'tips'       => '💡 Mẹo & Kinh nghiệm',
            'review'     => '⭐ Review khách sạn',
            'news'       => '📰 Tin tức',
            'promotion'  => '🎁 Khuyến mãi',
        ];
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin bài viết')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Tiêu đề')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\Select::make('category')
                    ->label('Danh mục')
                    ->options(static::categoryOptions())
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('author')
                    ->label('Tác giả')
                    ->required()
                    ->maxLength(200)
                    ->default('Admin'),
                Forms\Components\TextInput::make('read_time')
                    ->label('Thời gian đọc')
                    ->required()
                    ->maxLength(50)
                    ->default('5 phút đọc'),
                Forms\Components\TextInput::make('tags')
                    ->label('Tags (cách nhau bởi dấu phẩy)')
                    ->maxLength(500)
                    ->placeholder('du lịch, biển, đà nẵng, nha trang')
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Hình ảnh')->schema([
                Forms\Components\FileUpload::make('thumb')
                    ->label('Ảnh thumbnail (nhỏ)')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('blog/thumbs')
                    ->panelLayout('integrated')
                    ->imagePreviewHeight('200')
                    ->maxSize(2048),
                Forms\Components\FileUpload::make('img')
                    ->label('Ảnh bìa (lớn)')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('blog/covers')
                    ->panelLayout('integrated')
                    ->imagePreviewHeight('200')
                    ->maxSize(4096),
            ])->columns(2),

            Forms\Components\Section::make('Nội dung')->schema([
                Forms\Components\Textarea::make('summary')
                    ->label('Tóm tắt')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->label('Nội dung bài viết')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Xuất bản')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('Xuất bản công khai')
                    ->default(true),
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
            ->searchPlaceholder('Tiêu đề, tác giả, tag...')

            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(55)
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(BlogPost $record): string => $record->summary ? \Str::limit($record->summary, 60) : ''),

                Tables\Columns\TextColumn::make('category')
                    ->label('Danh mục')
                    ->badge()
                    ->formatStateUsing(fn($state) => static::categoryOptions()[$state] ?? $state)
                    ->color('info'),

                Tables\Columns\TextColumn::make('author')
                    ->label('Tác giả')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tags')
                    ->label('Tags')
                    ->limit(30)
                    ->placeholder('—')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('read_time')
                    ->label('Thời gian đọc')
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Xuất bản'),
            ])

            ->filters([
                SelectFilter::make('category')
                    ->label('Danh mục')
                    ->options(static::categoryOptions())
                    ->placeholder('Tất cả danh mục'),

                TernaryFilter::make('is_active')
                    ->label('Trạng thái xuất bản')
                    ->trueLabel('Đã xuất bản')
                    ->falseLabel('Chưa xuất bản'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)

            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa')->button(),
                Action::make('toggle_active')
                    ->label(fn(BlogPost $record): string => $record->is_active ? 'Ẩn' : 'Xuất bản')
                    ->icon(fn(BlogPost $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn(BlogPost $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(function (BlogPost $record): void {
                        $record->update(['is_active' => ! $record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Đã xuất bản bài viết' : 'Đã ẩn bài viết')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()->label('')->tooltip('Xóa')->iconButton(),
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
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
