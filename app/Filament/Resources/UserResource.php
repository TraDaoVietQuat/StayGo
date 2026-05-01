<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\BookingsRelationManager;
use App\Models\User;
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
use Illuminate\Database\Eloquent\Builder;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationLabel  = 'Quản lý User';
    protected static ?string $modelLabel       = 'Người dùng';
    protected static ?string $pluralModelLabel = 'Quản lý người dùng';
    protected static ?string $navigationGroup  = 'Quản Lý';
    protected static ?int    $navigationSort   = 1;

    public static function roleOptions(): array
    {
        return [
            'user'  => 'Người dùng',
            'admin' => 'Quản trị viên',
        ];
    }

    // ------------------------------------------------------------------
    // Form
    // ------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin cá nhân')->schema([
                Forms\Components\FileUpload::make('avatar')
                    ->label('Ảnh đại diện')
                    ->image()
                    ->directory('avatars')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->maxSize(1024)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('full_name')
                    ->label('Họ và tên')
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->tel()
                    ->maxLength(20),
            ])->columns(3),

            Forms\Components\Section::make('Tài khoản')->schema([
                Forms\Components\Select::make('role')
                    ->label('Vai trò')
                    ->options(static::roleOptions())
                    ->required()
                    ->default('user'),
                Forms\Components\TextInput::make('password')
                    ->label('Mật khẩu')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->helperText('Để trống nếu không muốn đổi mật khẩu'),
                Forms\Components\Toggle::make('is_new_user')
                    ->label('Người dùng mới')
                    ->default(true),
            ])->columns(3),

            Forms\Components\Section::make('Liên kết mạng xã hội & Xác minh')->schema([
                Forms\Components\TextInput::make('google_id')
                    ->label('Google ID')
                    ->placeholder('Chưa liên kết')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('facebook_id')
                    ->label('Facebook ID')
                    ->placeholder('Chưa liên kết')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('Email xác minh lúc')
                    ->placeholder('Chưa xác minh')
                    ->nullable(),
            ])->columns(3),
        ]);
    }

    // ------------------------------------------------------------------
    // Table
    // ------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount('bookings'))
            ->searchPlaceholder('Tên, email, số điện thoại...')

            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn(User $record): string =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->full_name ?? 'User') . '&color=7F9CF5&background=EBF4FF'
                    ),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Người dùng')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(User $record): string => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Vai trò')
                    ->badge()
                    ->formatStateUsing(fn($state) => static::roleOptions()[$state] ?? $state)
                    ->color(fn(?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user'  => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Đặt phòng')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_new_user')
                    ->label('Mới')
                    ->boolean()
                    ->trueIcon('heroicon-o-sparkles')
                    ->trueColor('warning'),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email xác minh')
                    ->boolean()
                    ->getStateUsing(fn(User $record): bool => !is_null($record->email_verified_at))
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('social_links')
                    ->label('Liên kết')
                    ->getStateUsing(fn(User $record): string =>
                        collect([
                            $record->google_id   ? 'Google'   : null,
                            $record->facebook_id ? 'Facebook' : null,
                        ])->filter()->join(' · ') ?: '—'
                    )
                    ->badge()
                    ->color(fn(string $state): string => $state === '—' ? 'gray' : 'info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đăng ký')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('role')
                    ->label('Vai trò')
                    ->options(static::roleOptions())
                    ->placeholder('Tất cả vai trò'),

                TernaryFilter::make('is_new_user')
                    ->label('Người dùng mới')
                    ->trueLabel('Người dùng mới')
                    ->falseLabel('Đã sử dụng lâu'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)

            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa')->button(),

                Action::make('toggle_role')
                    ->label(fn(User $record): string => $record->role === 'admin' ? 'Hạ xuống User' : 'Nâng lên Admin')
                    ->icon(fn(User $record): string => $record->role === 'admin' ? 'heroicon-o-arrow-down' : 'heroicon-o-arrow-up')
                    ->color(fn(User $record): string => $record->role === 'admin' ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn(User $record): string => $record->role === 'admin' ? 'Hạ xuống người dùng thường' : 'Nâng lên quản trị viên')
                    ->modalDescription('Bạn có chắc chắn muốn thay đổi quyền của người dùng này?')
                    ->action(function (User $record): void {
                        $newRole = $record->role === 'admin' ? 'user' : 'admin';
                        $record->update(['role' => $newRole]);
                        Notification::make()
                            ->title('Đã cập nhật vai trò thành ' . static::roleOptions()[$newRole])
                            ->success()
                            ->send();
                    }),

                Action::make('reset_password')
                    ->label('Đặt lại MK')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Mật khẩu mới')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(6),
                    ])
                    ->action(function (User $record, array $data): void {
                        // Model cast 'password' => 'hashed' auto-hashes the value
                        $record->update(['password' => $data['new_password']]);
                        Notification::make()->title('Đã đặt lại mật khẩu')->success()->send();
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
        return [
            BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
