<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportRequestResource\Pages;
use App\Mail\SupportRequestReplied;
use App\Models\SupportReply;
use App\Models\SupportRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SupportRequestResource extends Resource
{
    protected static ?string $model = SupportRequest::class;

    protected static ?string $navigationIcon   = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel  = 'Hỗ trợ';
    protected static ?string $modelLabel       = 'Yêu cầu hỗ trợ';
    protected static ?string $pluralModelLabel = 'Quản lý hỗ trợ';
    protected static ?string $navigationGroup  = 'Giao Dịch';
    protected static ?int    $navigationSort   = 4;

    public static function getNavigationBadge(): ?string
    {
        $count = Cache::remember('badge.support.pending', 60, fn () => SupportRequest::where('status', 'pending')->count());
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function statusOptions(): array
    {
        return [
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'resolved'   => 'Đã giải quyết',
            'closed'     => 'Đã đóng',
        ];
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin khách hàng')->schema([
                Forms\Components\TextInput::make('full_name')
                    ->label('Họ và tên')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->tel()
                    ->required()
                    ->maxLength(20),
            ])->columns(3),

            Forms\Components\Section::make('Nội dung yêu cầu')->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('Chủ đề')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('note')
                    ->label('Nội dung khách gửi')
                    ->rows(4)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Xử lý')->schema([
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options(static::statusOptions())
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('admin_note')
                    ->label('Phản hồi / Ghi chú admin')
                    ->rows(4)
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
            ->searchPlaceholder('Tên, email, chủ đề...')

            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Khách hàng')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->description(fn(SupportRequest $record): string => $record->email ?? $record->phone),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Chủ đề')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn(SupportRequest $record): ?string => $record->subject),

                Tables\Columns\TextColumn::make('note')
                    ->label('Nội dung')
                    ->limit(50)
                    ->tooltip(fn(SupportRequest $record): ?string => $record->note)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('admin_note')
                    ->label('Phản hồi admin')
                    ->limit(40)
                    ->placeholder('Chưa có phản hồi')
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($state) => static::statusOptions()[$state] ?? $state)
                    ->color(fn(?string $state): string => match ($state) {
                        'pending'    => 'danger',
                        'processing' => 'warning',
                        'resolved'   => 'success',
                        'closed'     => 'gray',
                        default      => 'gray',
                    }),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(static::statusOptions())
                    ->placeholder('Tất cả trạng thái'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)

            ->actions([
                Action::make('reply')
                    ->label('Phản hồi')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('primary')
                    ->button()
                    ->form([
                        Forms\Components\Placeholder::make('customer_note')
                            ->label('Nội dung khách gửi')
                            ->content(fn(SupportRequest $record): ?string => $record->note),
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Phản hồi của admin')
                            ->rows(4)
                            ->required()
                            ->default(fn(SupportRequest $record): ?string => $record->admin_note),
                        Forms\Components\Select::make('status')
                            ->label('Cập nhật trạng thái')
                            ->options(static::statusOptions())
                            ->required()
                            ->default(fn(SupportRequest $record): string => $record->status),
                    ])
                    ->action(function (SupportRequest $record, array $data): void {
                        $isResolved = $data['status'] === 'resolved';
                        $record->update([
                            'admin_note' => $data['admin_note'],
                            'status'     => $data['status'],
                        ]);
                        // Lưu vào thread replies để user thấy
                        SupportReply::create([
                            'support_request_id' => $record->id,
                            'user_id'            => null, // admin/system
                            'message'            => $data['admin_note'],
                            'is_admin'           => true,
                        ]);
                        // Gửi email thông báo cho user nếu có email
                        if ($record->email) {
                            Mail::to($record->email)->send(
                                new SupportRequestReplied($record, $data['admin_note'], $isResolved)
                            );
                        }
                        Notification::make()->title('Đã lưu phản hồi')->success()->send();
                    }),

                Action::make('resolve')
                    ->label('Đã giải quyết')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(SupportRequest $record): bool => ! in_array($record->status, ['resolved', 'closed']))
                    ->action(function (SupportRequest $record): void {
                        $record->update(['status' => 'resolved']);
                        // Gửi email thông báo cho user nếu có email
                        if ($record->email) {
                            Mail::to($record->email)->send(
                                new SupportRequestReplied($record, $record->admin_note ?? '', isResolved: true)
                            );
                        }
                        Notification::make()->title('Đánh dấu đã giải quyết')->success()->send();
                    }),

                Tables\Actions\EditAction::make()->label('')->tooltip('Chi tiết')->iconButton(),
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
            'index'  => Pages\ListSupportRequests::route('/'),
            'create' => Pages\CreateSupportRequest::route('/create'),
            'edit'   => Pages\EditSupportRequest::route('/{record}/edit'),
        ];
    }
}
