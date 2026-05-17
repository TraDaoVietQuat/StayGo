<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelPartnerResource\Pages;
use App\Models\Hotel;
use App\Models\HotelPartnerProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class HotelPartnerResource extends Resource
{
    protected static ?string $model = HotelPartnerProfile::class;

    protected static ?string $navigationIcon   = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel  = 'Đối tác khách sạn';
    protected static ?string $modelLabel       = 'Đối tác';
    protected static ?string $pluralModelLabel = 'Quản lý đối tác';
    protected static ?string $navigationGroup  = 'Đối tác';
    protected static ?int    $navigationSort   = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = HotelPartnerProfile::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Tài khoản đối tác')->schema([
                Forms\Components\TextInput::make('user.full_name')
                    ->label('Tên đối tác')->disabled(),
                Forms\Components\TextInput::make('user.email')
                    ->label('Email')->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái tài khoản')
                    ->options(HotelPartnerProfile::statusLabels())->required(),
                Forms\Components\TextInput::make('commission_rate')
                    ->label('Tỷ lệ hoa hồng (%)')
                    ->numeric()->suffix('%')->default(15)->required(),
            ])->columns(2),

            Forms\Components\Section::make('Thông tin doanh nghiệp')->schema([
                Forms\Components\TextInput::make('business_name')->label('Tên doanh nghiệp')->maxLength(150),
                Forms\Components\TextInput::make('contact_name')->label('Người liên hệ')->maxLength(100),
                Forms\Components\TextInput::make('contact_phone')->label('Số điện thoại')->tel()->maxLength(20),
                Forms\Components\TextInput::make('tax_code')->label('Mã số thuế')->maxLength(30),
            ])->columns(2),

            Forms\Components\Section::make('Thông tin ngân hàng')->schema([
                Forms\Components\TextInput::make('bank_name')->label('Tên ngân hàng')->maxLength(100),
                Forms\Components\TextInput::make('bank_account')->label('Số tài khoản')->maxLength(50),
                Forms\Components\TextInput::make('bank_branch')->label('Chi nhánh')->maxLength(150),
                Forms\Components\TextInput::make('bank_owner')->label('Chủ tài khoản')->maxLength(100),
            ])->columns(2),

            Forms\Components\Section::make('Gán khách sạn')->schema([
                Forms\Components\Select::make('hotel_id_assign')
                    ->label('Gán khách sạn cho đối tác này')
                    ->options(Hotel::whereNull('partner_user_id')->pluck('name', 'id'))
                    ->searchable()->helperText('Chỉ hiển thị khách sạn chưa có đối tác. Để trống nếu không thay đổi.')
                    ->nullable(),
            ]),

            Forms\Components\Section::make('Ghi chú admin')->schema([
                Forms\Components\Textarea::make('notes')->label('Ghi chú nội bộ')->rows(3),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Lý do từ chối (nếu có)')->rows(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn(Builder $q) => $q->with(['user', 'hotel']))
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Đối tác')
                    ->description(fn(HotelPartnerProfile $r) => $r->user?->email ?? '')
                    ->searchable(['users.full_name', 'users.email']),
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Doanh nghiệp')->placeholder('—'),
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Khách sạn')->placeholder('Chưa gán'),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Hoa hồng')
                    ->formatStateUsing(fn($s) => $s . '%'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')->badge()
                    ->formatStateUsing(fn($s) => HotelPartnerProfile::statusLabels()[$s] ?? $s)
                    ->color(fn($s) => match ($s) {
                        'active'    => 'success',
                        'pending'   => 'warning',
                        'suspended' => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Đăng ký')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')
                    ->options(HotelPartnerProfile::statusLabels()),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Duyệt')->icon('heroicon-o-check-circle')->color('success')->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'pending')
                    ->requiresConfirmation()->modalHeading('Duyệt đối tác này?')
                    ->action(function (HotelPartnerProfile $record) {
                        $record->update([
                            'status'      => 'active',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Đã duyệt đối tác: ' . $record->user?->full_name)->success()->send();
                    }),

                Action::make('suspend')
                    ->label('Đình chỉ')->icon('heroicon-o-pause-circle')->color('warning')->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'active')
                    ->requiresConfirmation()->modalHeading('Đình chỉ tài khoản đối tác?')
                    ->action(function (HotelPartnerProfile $record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()->title('Đã đình chỉ: ' . $record->user?->full_name)->warning()->send();
                    }),

                Action::make('reject')
                    ->label('Từ chối')->icon('heroicon-o-x-circle')->color('danger')->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Lý do từ chối')->required()->rows(3),
                    ])
                    ->action(function (HotelPartnerProfile $record, array $data) {
                        $record->update(['status' => 'rejected', 'rejection_reason' => $data['reason']]);
                        Notification::make()->title('Đã từ chối đối tác')->danger()->send();
                    }),

                Tables\Actions\EditAction::make()->label('Chi tiết')->iconButton(),
            ])
            ->headerActions([
                Action::make('create_partner')
                    ->label('Tạo tài khoản đối tác')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('full_name')->label('Họ và tên')->required(),
                        Forms\Components\TextInput::make('email')->label('Email')->email()->required()
                            ->unique('users', 'email'),
                        Forms\Components\TextInput::make('phone')->label('Số điện thoại')->tel(),
                        Forms\Components\TextInput::make('password')->label('Mật khẩu')
                            ->password()->required()->minLength(8),
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Tỷ lệ hoa hồng (%)')->numeric()->default(15)->suffix('%'),
                    ])
                    ->action(function (array $data) {
                        $user = User::create([
                            'full_name'         => $data['full_name'],
                            'email'             => $data['email'],
                            'phone'             => $data['phone'] ?? null,
                            'password'          => Hash::make($data['password']),
                            'role'              => 'hotel_partner',
                            'email_verified_at' => now(),
                        ]);
                        HotelPartnerProfile::create([
                            'user_id'         => $user->id,
                            'status'          => 'active',
                            'commission_rate' => $data['commission_rate'],
                            'approved_by'     => auth()->id(),
                            'approved_at'     => now(),
                        ]);
                        Notification::make()->title('Đã tạo tài khoản đối tác: ' . $user->full_name)->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotelPartners::route('/'),
            'edit'  => Pages\EditHotelPartner::route('/{record}/edit'),
        ];
    }
}
