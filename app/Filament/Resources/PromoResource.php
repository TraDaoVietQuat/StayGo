<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon   = 'heroicon-o-ticket';
    protected static ?string $navigationLabel  = 'Mã giảm giá';
    protected static ?string $modelLabel       = 'Mã giảm giá';
    protected static ?string $pluralModelLabel = 'Quản lý mã giảm giá';
    protected static ?string $navigationGroup  = 'Nội Dung';
    protected static ?int    $navigationSort   = 5;

    public static function getNavigationBadge(): ?string
    {
        $count = Promo::where('is_active', true)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin mã giảm giá')->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Mã giảm giá')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('VD: SUMMER20')
                    ->helperText('Mã sẽ được tự động chuyển thành chữ hoa')
                    ->dehydrateStateUsing(fn($state) => strtoupper($state)),

                Forms\Components\Select::make('type')
                    ->label('Loại giảm giá')
                    ->options([
                        'percent' => '% Phần trăm',
                        'fixed'   => '₫ Số tiền cố định',
                    ])
                    ->required()
                    ->default('percent')
                    ->reactive(),

                Forms\Components\TextInput::make('value')
                    ->label(fn($get) => $get('type') === 'fixed' ? 'Số tiền giảm (đ)' : 'Phần trăm giảm (%)')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(fn($get) => $get('type') === 'percent' ? 100 : null)
                    ->prefix(fn($get) => $get('type') === 'fixed' ? '₫' : null)
                    ->suffix(fn($get) => $get('type') === 'percent' ? '%' : null)
                    ->placeholder(fn($get) => $get('type') === 'fixed' ? '50000' : '10'),
            ])->columns(3),

            Forms\Components\Section::make('Điều kiện áp dụng')->schema([
                Forms\Components\TextInput::make('min_order')
                    ->label('Giá trị đơn tối thiểu (đ)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix('₫')
                    ->placeholder('0 = không giới hạn'),

                Forms\Components\TextInput::make('max_discount')
                    ->label('Giảm tối đa (đ)')
                    ->numeric()
                    ->nullable()
                    ->prefix('₫')
                    ->placeholder('Để trống = không giới hạn')
                    ->helperText('Chỉ áp dụng cho loại % phần trăm'),

                Forms\Components\TextInput::make('max_uses')
                    ->label('Tổng lượt dùng tối đa')
                    ->numeric()
                    ->nullable()
                    ->minValue(1)
                    ->placeholder('Để trống = không giới hạn'),

                Forms\Components\TextInput::make('max_uses_per_user')
                    ->label('Lượt dùng/người')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->placeholder('1'),

                Forms\Components\Toggle::make('new_user_only')
                    ->label('Chỉ cho khách mới (chưa có booking)')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('Đang hoạt động')
                    ->default(true),
            ])->columns(3),

            Forms\Components\Section::make('Thời gian hiệu lực')->schema([
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Bắt đầu từ')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->nullable()
                    ->placeholder('Để trống = có hiệu lực ngay'),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Hết hạn vào')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->nullable()
                    ->placeholder('Để trống = không hết hạn'),
            ])->columns(2),

            Forms\Components\Section::make('Mô tả')->schema([
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả (hiển thị cho admin)')
                    ->rows(2)
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
            ->defaultSort('id', 'desc')
            ->searchPlaceholder('Tìm mã, mô tả...')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->weight(FontWeight::Bold)
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('discount_display')
                    ->label('Giảm giá')
                    ->getStateUsing(function (Promo $record): HtmlString {
                        $val = $record->type === 'percent'
                            ? $record->value . '%'
                            : number_format($record->value, 0, ',', '.') . 'đ';
                        $cap = $record->max_discount
                            ? '<span class="text-xs text-gray-400"> (tối đa ' . number_format($record->max_discount, 0, ',', '.') . 'đ)</span>'
                            : '';
                        return new HtmlString('<span class="font-bold text-rose-600">' . $val . '</span>' . $cap);
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('min_order')
                    ->label('Đơn tối thiểu')
                    ->formatStateUsing(fn($state) => $state > 0 ? number_format($state, 0, ',', '.') . 'đ' : 'Không giới hạn')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('usage')
                    ->label('Lượt dùng')
                    ->getStateUsing(function (Promo $record): string {
                        $max = $record->max_uses ? '/' . $record->max_uses : '/∞';
                        return $record->used_count . $max;
                    })
                    ->badge()
                    ->color(fn(Promo $record): string => (
                        $record->max_uses && $record->used_count >= $record->max_uses
                    ) ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('validity')
                    ->label('Hiệu lực')
                    ->getStateUsing(function (Promo $record): HtmlString {
                        $now = now();
                        if ($record->expires_at && $now->gt($record->expires_at)) {
                            return new HtmlString('<span class="text-red-500 text-xs">Hết hạn ' . $record->expires_at->format('d/m/Y') . '</span>');
                        }
                        if ($record->starts_at && $now->lt($record->starts_at)) {
                            return new HtmlString('<span class="text-amber-500 text-xs">Bắt đầu ' . $record->starts_at->format('d/m/Y') . '</span>');
                        }
                        $end = $record->expires_at ? '<span class="text-gray-400 text-xs"> → ' . $record->expires_at->format('d/m/Y') . '</span>' : '';
                        return new HtmlString('<span class="text-green-600 text-xs">Đang áp dụng</span>' . $end);
                    })
                    ->html(),

                Tables\Columns\IconColumn::make('new_user_only')
                    ->label('Khách mới')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hoạt động'),
            ])

            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Đã tắt'),

                SelectFilter::make('type')
                    ->label('Loại')
                    ->options(['percent' => '% Phần trăm', 'fixed' => '₫ Số tiền cố định'])
                    ->placeholder('Tất cả loại'),

                TernaryFilter::make('new_user_only')
                    ->label('Khách mới')
                    ->trueLabel('Chỉ khách mới')
                    ->falseLabel('Tất cả'),
            ])

            ->actions([
                Action::make('duplicate')
                    ->label('Nhân bản')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Promo $record): void {
                        $newCode = $record->code . '_COPY';
                        Promo::create(array_merge(
                            $record->toArray(),
                            ['code' => $newCode, 'used_count' => 0, 'is_active' => false]
                        ));
                        Notification::make()->title('Đã nhân bản mã "' . $newCode . '"')->success()->send();
                    }),

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
            'index'  => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit'   => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}
