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
use App\Mail\PartnerApproved;
use App\Mail\PartnerRejected;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $count = Cache::remember('badge.hotel_partners.pending', 120, fn () => HotelPartnerProfile::where('status', 'pending')->count());
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    // -----------------------------------------------------------------------
    // AI helpers
    // -----------------------------------------------------------------------

    private static function callAi(string $prompt): string
    {
        $messages = [['role' => 'user', 'content' => $prompt]];

        try {
            $key = config('services.openrouter.api_key');
            if ($key) {
                $res = Http::withToken($key)
                    ->timeout(45)
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemini-flash-1.5'),
                        'messages' => $messages,
                    ]);
                if ($res->successful() && isset($res['choices'][0]['message']['content'])) {
                    return $res['choices'][0]['message']['content'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenRouter failed in PartnerReview: ' . $e->getMessage());
        }

        try {
            $key2 = config('services.openai.api_key');
            if ($key2) {
                $res2 = Http::withToken($key2)
                    ->timeout(45)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => $messages,
                    ]);
                if ($res2->successful() && isset($res2['choices'][0]['message']['content'])) {
                    return $res2['choices'][0]['message']['content'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI failed in PartnerReview: ' . $e->getMessage());
        }

        return 'Không thể kết nối AI. Vui lòng thực hiện xét duyệt thủ công.';
    }

    private static function buildPartnerContext(HotelPartnerProfile $record): string
    {
        $lines = [
            'TÊN DOANH NGHIỆP : ' . ($record->business_name ?: 'Chưa cung cấp'),
            'NGƯỜI LIÊN HỆ    : ' . ($record->contact_name ?: '?') . ' | SĐT: ' . ($record->contact_phone ?: '?'),
            'MÃ SỐ THUẾ       : ' . ($record->tax_code ?: 'Chưa cung cấp'),
            'NGÂN HÀNG        : ' . ($record->bank_name
                ? "{$record->bank_name} | TK: {$record->bank_account} | Chủ TK: {$record->bank_owner}"
                : 'Chưa cung cấp'),
            'GHI CHÚ ĐĂNG KÝ  : ' . ($record->notes ?: 'Không có'),
        ];

        if ($hotel = $record->hotel) {
            $lines[] = 'KHÁCH SẠN         : ' . $hotel->name;
            $lines[] = 'ĐỊA CHỈ           : ' . $hotel->address . ', ' . $hotel->city;
            if ($hotel->star_rating) {
                $lines[] = 'HẠNG SAO          : ' . $hotel->star_rating . ' sao';
            }
            $roomCount = $hotel->rooms()->count();
            if ($roomCount) {
                $lines[] = 'SỐ LOẠI PHÒNG     : ' . $roomCount;
            }
        } else {
            $lines[] = 'KHÁCH SẠN         : Chưa được gán';
        }

        return implode("\n", $lines);
    }

    private static function buildReviewPrompt(string $context): string
    {
        return "Bạn là chuyên gia xét duyệt hồ sơ đối tác cho nền tảng OTA StayGo (đặt phòng khách sạn & resort Việt Nam).\n\n"
            . "HỒ SƠ ĐỐI TÁC:\n{$context}\n\n"
            . "ĐÁNH GIÁ THEO CHECKLIST:\n"
            . "[GIẤY TỜ PHÁP LÝ]\n"
            . "1. Giấy phép kinh doanh hợp lệ\n"
            . "2. Chứng nhận phòng cháy chữa cháy (PCCC)\n"
            . "3. Giấy phép kinh doanh lưu trú du lịch\n"
            . "4. Mã số thuế hợp lệ\n"
            . "5. Thông tin người đại diện pháp luật đầy đủ\n"
            . "[THÔNG TIN KHÁCH SẠN]\n"
            . "6. Tên/địa chỉ/tọa độ GPS chính xác\n"
            . "7. Chứng nhận hạng sao (nếu có)\n"
            . "8. Mô tả chi tiết phòng và tiện nghi\n"
            . "9. Danh sách đầy đủ tiện ích\n"
            . "10. Chính sách check-in/out và hủy phòng rõ ràng\n"
            . "[HÌNH ẢNH & NỘI DUNG]\n"
            . "11. Ít nhất 15 ảnh thực tế chất lượng cao\n"
            . "12. Đủ loại ảnh (phòng, sảnh, hồ bơi, nhà hàng...)\n"
            . "13. Mô tả song ngữ Anh-Việt\n"
            . "14. Thông tin liên hệ đầy đủ\n"
            . "[TÀI CHÍNH]\n"
            . "15. Tài khoản ngân hàng hợp lệ, đúng tên doanh nghiệp\n"
            . "16. Đồng ý tỷ lệ hoa hồng StayGo\n"
            . "17. Hợp đồng điện tử đã ký\n\n"
            . "Trả lời CHÍNH XÁC theo format:\n\n"
            . "QUYẾT ĐỊNH: [APPROVE / REJECT / REQUIRE_MORE_INFO]\n\n"
            . "TÓM TẮT HỒ SƠ:\n(3-5 dòng mô tả tổng quan)\n\n"
            . "ĐIỂM ĐẠT / CHƯA ĐẠT:\n"
            . "✅ Đạt: [số thứ tự]\n"
            . "❌ Chưa đạt: [số thứ tự + mô tả ngắn]\n\n"
            . "CÁC MỤC CẦN BỔ SUNG:\n(liệt kê cụ thể nếu REJECT hoặc REQUIRE_MORE_INFO)\n\n"
            . "HOA HỒNG ĐỀ XUẤT: [X]% — Lý do: ...\n\n"
            . "GHI CHÚ ĐẶC BIỆT: (nếu có)";
    }

    // -----------------------------------------------------------------------
    // Form (edit page)
    // -----------------------------------------------------------------------

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
                    ->searchable()
                    ->helperText('Chỉ hiển thị khách sạn chưa có đối tác. Để trống nếu không thay đổi.')
                    ->nullable(),
            ]),

            Forms\Components\Section::make('Ghi chú & Kết quả xét duyệt')->schema([
                Forms\Components\Textarea::make('notes')->label('Ghi chú nội bộ')->rows(3),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Lý do từ chối (nếu có)')->rows(2),
                Forms\Components\Select::make('review_decision')
                    ->label('Quyết định xét duyệt')
                    ->options([
                        'APPROVE'           => 'DUYỆT',
                        'REJECT'            => 'TỪ CHỐI',
                        'REQUIRE_MORE_INFO' => 'YÊU CẦU BỔ SUNG',
                    ])
                    ->disabled(),
                Forms\Components\TextInput::make('proposed_commission')
                    ->label('Hoa hồng đề xuất (%)')->suffix('%')->disabled(),
                Forms\Components\Textarea::make('review_summary')
                    ->label('Tóm tắt hồ sơ (AI)')->rows(4)->disabled(),
                Forms\Components\Textarea::make('review_missing_items')
                    ->label('Các mục cần bổ sung')->rows(2)->disabled(),
            ])->columns(2),
        ]);
    }

    // -----------------------------------------------------------------------
    // Table
    // -----------------------------------------------------------------------

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
                Tables\Columns\TextColumn::make('review_decision')
                    ->label('KQ xét duyệt')
                    ->badge()
                    ->placeholder('—')
                    ->color(fn($s) => match ($s) {
                        'APPROVE'           => 'success',
                        'REJECT'            => 'danger',
                        'REQUIRE_MORE_INFO' => 'warning',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn($s) => match ($s) {
                        'APPROVE'           => 'Duyệt',
                        'REJECT'            => 'Từ chối',
                        'REQUIRE_MORE_INFO' => 'Bổ sung',
                        default             => '—',
                    }),
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
                // ---- AI analysis ----
                Action::make('ai_analyze')
                    ->label('AI Phân tích')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Phân tích hồ sơ bằng AI?')
                    ->modalDescription('AI sẽ đọc thông tin hồ sơ, đối chiếu checklist và đưa ra đề xuất. Quá trình mất 10-30 giây.')
                    ->modalSubmitActionLabel('Bắt đầu phân tích')
                    ->action(function (HotelPartnerProfile $record) {
                        $context = self::buildPartnerContext($record);
                        $prompt  = self::buildReviewPrompt($context);
                        $reply   = self::callAi($prompt);

                        $record->update([
                            'review_summary' => $reply,
                            'reviewed_by'    => Auth::id(),
                            'reviewed_at'    => now(),
                        ]);

                        Notification::make()
                            ->title('AI đã phân tích xong hồ sơ')
                            ->body('Nhấn "Xét duyệt" để xem kết quả và hoàn tất quyết định.')
                            ->success()
                            ->duration(7000)
                            ->send();
                    }),

                // ---- Checklist review ----
                Action::make('review')
                    ->label('Xét duyệt')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'pending')
                    ->modalWidth('4xl')
                    ->fillForm(fn(HotelPartnerProfile $record) => [
                        'legal_items'          => $record->review_checklist['legal']   ?? [],
                        'hotel_items'          => $record->review_checklist['hotel']   ?? [],
                        'content_items'        => $record->review_checklist['content'] ?? [],
                        'finance_items'        => $record->review_checklist['finance'] ?? [],
                        'review_decision'      => $record->review_decision,
                        'review_summary'       => $record->review_summary,
                        'review_missing_items' => $record->review_missing_items,
                        'proposed_commission'  => $record->proposed_commission ?? $record->commission_rate ?? 15.0,
                        'review_notes'         => $record->review_notes,
                    ])
                    ->form([
                        Forms\Components\Section::make('GIẤY TỜ PHÁP LÝ')->schema([
                            Forms\Components\CheckboxList::make('legal_items')
                                ->label('Đánh dấu các mục đã đáp ứng')
                                ->options([
                                    'business_license' => '1. Giấy phép kinh doanh hợp lệ',
                                    'fire_safety'      => '2. Chứng nhận phòng cháy chữa cháy (PCCC)',
                                    'tourism_license'  => '3. Giấy phép kinh doanh lưu trú du lịch',
                                    'tax_code'         => '4. Mã số thuế hợp lệ',
                                    'legal_rep'        => '5. Thông tin người đại diện pháp luật đầy đủ',
                                ])
                                ->bulkToggleable()
                                ->columns(1),
                        ]),

                        Forms\Components\Section::make('THÔNG TIN KHÁCH SẠN')->schema([
                            Forms\Components\CheckboxList::make('hotel_items')
                                ->label('Đánh dấu các mục đã đáp ứng')
                                ->options([
                                    'name_address_gps' => '6. Tên / địa chỉ / tọa độ GPS chính xác',
                                    'star_cert'        => '7. Chứng nhận hạng sao (nếu có)',
                                    'rooms_desc'       => '8. Mô tả chi tiết các loại phòng và tiện nghi',
                                    'amenities'        => '9. Danh sách đầy đủ tiện ích khách sạn',
                                    'policies'         => '10. Chính sách check-in/check-out và hủy phòng rõ ràng',
                                ])
                                ->bulkToggleable()
                                ->columns(1),
                        ]),

                        Forms\Components\Section::make('HÌNH ẢNH & NỘI DUNG')->schema([
                            Forms\Components\CheckboxList::make('content_items')
                                ->label('Đánh dấu các mục đã đáp ứng')
                                ->options([
                                    'min_15_photos'  => '11. Có ít nhất 15 ảnh thực tế chất lượng cao',
                                    'photo_variety'  => '12. Đủ loại ảnh (phòng, sảnh, hồ bơi, nhà hàng...)',
                                    'bilingual_desc' => '13. Mô tả khách sạn song ngữ Anh-Việt',
                                    'contact_info'   => '14. Thông tin liên hệ đầy đủ (SĐT, email, website)',
                                ])
                                ->bulkToggleable()
                                ->columns(1),
                        ]),

                        Forms\Components\Section::make('TÀI CHÍNH')->schema([
                            Forms\Components\CheckboxList::make('finance_items')
                                ->label('Đánh dấu các mục đã đáp ứng')
                                ->options([
                                    'bank_account'         => '15. Tài khoản ngân hàng hợp lệ, đúng tên doanh nghiệp',
                                    'commission_agreement' => '16. Đồng ý với tỷ lệ hoa hồng StayGo',
                                    'contract_signed'      => '17. Hợp đồng điện tử đã ký kết',
                                ])
                                ->bulkToggleable()
                                ->columns(1),
                        ]),

                        Forms\Components\Section::make('Kết quả phân tích & Quyết định')->schema([
                            Forms\Components\Textarea::make('review_summary')
                                ->label('Tóm tắt hồ sơ (chỉnh sửa kết quả AI nếu cần)')
                                ->rows(6)
                                ->placeholder('Nhấn "AI Phân tích" trước để tự động điền nội dung...')
                                ->columnSpanFull(),

                            Forms\Components\Select::make('review_decision')
                                ->label('Quyết định xét duyệt')
                                ->options([
                                    'APPROVE'           => '✅  DUYỆT — Chấp thuận làm đối tác',
                                    'REJECT'            => '❌  TỪ CHỐI — Không đủ điều kiện',
                                    'REQUIRE_MORE_INFO' => '⚠️  YÊU CẦU BỔ SUNG — Cần thêm thông tin',
                                ])
                                ->required(),

                            Forms\Components\TextInput::make('proposed_commission')
                                ->label('Mức hoa hồng đề xuất (%)')
                                ->numeric()
                                ->suffix('%')
                                ->minValue(5)
                                ->maxValue(35)
                                ->step(0.5),

                            Forms\Components\Textarea::make('review_missing_items')
                                ->label('Các mục cần bổ sung / lý do từ chối')
                                ->rows(3)
                                ->placeholder('Liệt kê rõ từng mục chưa đạt yêu cầu...')
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('review_notes')
                                ->label('Ghi chú nội bộ')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])->columns(2),
                    ])
                    ->action(function (HotelPartnerProfile $record, array $data) {
                        $newStatus = match ($data['review_decision']) {
                            'APPROVE' => 'active',
                            'REJECT'  => 'rejected',
                            default   => 'pending',
                        };

                        $updateData = [
                            'review_checklist'    => [
                                'legal'   => $data['legal_items']   ?? [],
                                'hotel'   => $data['hotel_items']   ?? [],
                                'content' => $data['content_items'] ?? [],
                                'finance' => $data['finance_items'] ?? [],
                            ],
                            'review_decision'     => $data['review_decision'],
                            'review_summary'      => $data['review_summary'],
                            'review_missing_items'=> $data['review_missing_items'],
                            'proposed_commission' => $data['proposed_commission'],
                            'review_notes'        => $data['review_notes'],
                            'status'              => $newStatus,
                            'reviewed_by'         => Auth::id(),
                            'reviewed_at'         => now(),
                        ];

                        if ($data['review_decision'] === 'APPROVE') {
                            $updateData['commission_rate'] = $data['proposed_commission'] ?? $record->commission_rate;
                            $updateData['approved_by']    = Auth::id();
                            $updateData['approved_at']    = now();
                        } elseif ($data['review_decision'] === 'REJECT') {
                            $updateData['rejection_reason'] = $data['review_missing_items'];
                        }

                        $record->update($updateData);

                        // E-09: gửi email thông báo kết quả xét duyệt
                        if ($record->user?->email) {
                            try {
                                if ($data['review_decision'] === 'APPROVE') {
                                    $tempPassword = Str::password(12, symbols: false);
                                    $record->user->update(['password' => Hash::make($tempPassword)]);
                                    $record->loadMissing('hotel');
                                    Mail::to($record->user->email)->send(new PartnerApproved($record, $tempPassword));
                                } elseif ($data['review_decision'] === 'REJECT') {
                                    Mail::to($record->user->email)->send(
                                        new PartnerRejected($record, $data['review_missing_items'] ?? '')
                                    );
                                }
                            } catch (\Exception $e) {
                                Log::warning('E-09 email failed: ' . $e->getMessage());
                            }
                        }

                        $label = match ($data['review_decision']) {
                            'APPROVE'           => 'Đã duyệt đối tác',
                            'REJECT'            => 'Đã từ chối hồ sơ',
                            'REQUIRE_MORE_INFO' => 'Yêu cầu bổ sung thông tin',
                        };

                        Notification::make()
                            ->title($label . ': ' . $record->user?->full_name)
                            ->success()
                            ->send();
                    }),

                // ---- Quick approve ----
                Action::make('approve')
                    ->label('Duyệt nhanh')->icon('heroicon-o-check-circle')->color('success')->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'pending')
                    ->requiresConfirmation()->modalHeading('Duyệt nhanh đối tác này?')
                    ->modalDescription('Bỏ qua checklist và duyệt ngay. Dùng "Xét duyệt" để đánh giá đầy đủ.')
                    ->action(function (HotelPartnerProfile $record) {
                        $record->update([
                            'status'      => 'active',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);

                        // E-09: gửi email phê duyệt kèm mật khẩu tạm
                        if ($record->user?->email) {
                            try {
                                $tempPassword = Str::password(12, symbols: false);
                                $record->user->update(['password' => Hash::make($tempPassword)]);
                                $record->loadMissing('hotel');
                                Mail::to($record->user->email)->send(new PartnerApproved($record, $tempPassword));
                            } catch (\Exception $e) {
                                Log::warning('E-09 quick-approve email failed: ' . $e->getMessage());
                            }
                        }

                        Notification::make()->title('Đã duyệt: ' . $record->user?->full_name)->success()->send();
                    }),

                // ---- Suspend ----
                Action::make('suspend')
                    ->label('Đình chỉ')->icon('heroicon-o-pause-circle')->color('warning')->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'active')
                    ->requiresConfirmation()->modalHeading('Đình chỉ tài khoản đối tác?')
                    ->action(function (HotelPartnerProfile $record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()->title('Đã đình chỉ: ' . $record->user?->full_name)->warning()->send();
                    }),

                // ---- Reject ----
                Action::make('reject')
                    ->label('Từ chối')->icon('heroicon-o-x-circle')->color('danger')->button()
                    ->visible(fn(HotelPartnerProfile $r) => $r->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Lý do từ chối')->required()->rows(3),
                    ])
                    ->action(function (HotelPartnerProfile $record, array $data) {
                        $record->update(['status' => 'rejected', 'rejection_reason' => $data['reason']]);

                        // E-09: gửi email từ chối
                        if ($record->user?->email) {
                            try {
                                $record->loadMissing('hotel');
                                Mail::to($record->user->email)->send(new PartnerRejected($record, $data['reason']));
                            } catch (\Exception $e) {
                                Log::warning('E-09 reject email failed: ' . $e->getMessage());
                            }
                        }

                        Notification::make()->title('Đã từ chối hồ sơ')->danger()->send();
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
                            'approved_by'     => Auth::id(),
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
