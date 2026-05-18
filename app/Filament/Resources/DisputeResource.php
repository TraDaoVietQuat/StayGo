<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisputeResource\Pages;
use App\Models\Booking;
use App\Models\Dispute;
use App\Models\Hotel;
use App\Models\User;
use App\Notifications\DisputeVerdictCustomerNotification;
use App\Notifications\DisputeVerdictHotelNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    protected static ?string $navigationIcon   = 'heroicon-o-scale';
    protected static ?string $navigationLabel  = 'Tranh chấp & Khiếu nại';
    protected static ?string $modelLabel       = 'Tranh chấp';
    protected static ?string $pluralModelLabel = 'Tranh chấp & Khiếu nại';
    protected static ?string $navigationGroup  = 'Hỗ trợ & Tranh chấp';
    protected static ?int    $navigationSort   = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Cache::remember('badge.disputes.open', 60, fn () => Dispute::whereIn('status', ['open', 'investigating', 'escalated'])->count());
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $urgent = Cache::remember('badge.disputes.urgent', 60, fn () => Dispute::where('priority', 'urgent')->whereIn('status', ['open', 'investigating'])->count());
        return $urgent > 0 ? 'danger' : 'warning';
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
                    ->timeout(60)
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemini-flash-1.5'),
                        'messages' => $messages,
                    ]);
                if ($res->successful() && isset($res['choices'][0]['message']['content'])) {
                    return $res['choices'][0]['message']['content'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenRouter failed in DisputeResource: ' . $e->getMessage());
        }

        try {
            $key2 = config('services.openai.api_key');
            if ($key2) {
                $res2 = Http::withToken($key2)
                    ->timeout(60)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => $messages,
                    ]);
                if ($res2->successful() && isset($res2['choices'][0]['message']['content'])) {
                    return $res2['choices'][0]['message']['content'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI failed in DisputeResource: ' . $e->getMessage());
        }

        return 'Không thể kết nối AI. Vui lòng phân tích và xử lý thủ công.';
    }

    private static function buildDisputeContext(Dispute $d): string
    {
        $booking = $d->booking;
        $lines   = [
            '=== THÔNG TIN TRANH CHẤP ===',
            'ID          : #' . $d->id,
            'Loại        : ' . (Dispute::typeLabels()[$d->type] ?? $d->type),
            'Mức độ ưu tiên: ' . strtoupper($d->priority),
            'Tiêu đề     : ' . $d->title,
            '',
            '=== NỘI DUNG KHIẾU NẠI ===',
            $d->description,
        ];

        if ($d->timeline) {
            $lines[] = '';
            $lines[] = '=== TIMELINE SỰ VIỆC ===';
            $lines[] = $d->timeline;
        }

        if ($booking) {
            $lines[] = '';
            $lines[] = '=== THÔNG TIN ĐẶT PHÒNG ===';
            $lines[] = 'Mã booking   : ' . $booking->order_code;
            $lines[] = 'Khách hàng   : ' . $booking->full_name . ' (' . $booking->email . ')';
            $lines[] = 'Khách sạn    : ' . ($d->hotel?->name ?? 'N/A');
            $lines[] = 'Check-in     : ' . ($booking->check_in?->format('d/m/Y') ?? 'N/A');
            $lines[] = 'Check-out    : ' . ($booking->check_out?->format('d/m/Y') ?? 'N/A');
            $lines[] = 'Tổng tiền    : ' . number_format($booking->total_price) . ' VND';
            $lines[] = 'Trạng thái   : ' . $booking->status;
        }

        if ($d->hotel_response) {
            $lines[] = '';
            $lines[] = '=== PHẢN HỒI CỦA KHÁCH SẠN ===';
            $lines[] = $d->hotel_response;
        }

        $evidence = $d->evidence ?? [];
        if (!empty($evidence)) {
            $lines[] = '';
            $lines[] = '=== BẰNG CHỨNG ===';
            foreach ($evidence as $i => $e) {
                $lines[] = ($i + 1) . '. [' . strtoupper($e['type'] ?? 'other') . '] ' . ($e['description'] ?? '');
            }
        }

        return implode("\n", $lines);
    }

    private static function buildAnalysisPrompt(Dispute $d): string
    {
        $context = self::buildDisputeContext($d);

        return "Bạn là chuyên viên xử lý tranh chấp cấp cao của nền tảng OTA StayGo (đặt phòng khách sạn & resort Việt Nam).\n\n"
            . $context . "\n\n"
            . "Phân tích tranh chấp này theo framework 4 bước:\n\n"
            . "BƯỚC 1 — XÁC NHẬN THÔNG TIN\n"
            . "Tóm tắt các thông tin đã có và xác định những gì còn thiếu.\n\n"
            . "BƯỚC 2 — ĐÁNH GIÁ TRÁCH NHIỆM\n"
            . "Phân tích trách nhiệm của từng bên:\n"
            . "- Lỗi của khách sạn (vi phạm SLA, thông tin sai, overbooking)?\n"
            . "- Lỗi của khách hàng (không đọc chính sách, no-show không báo)?\n"
            . "- Lỗi của nền tảng (hiển thị sai, lỗi hệ thống)?\n"
            . "- Lỗi bên thứ ba (cổng thanh toán, bất khả kháng)?\n\n"
            . "BƯỚC 3 — ĐỀ XUẤT PHÁN QUYẾT\n"
            . "Đề xuất một trong các phương án:\n"
            . "- REFUND_100: Hoàn tiền 100% cho khách\n"
            . "- REFUND_PARTIAL: Hoàn tiền một phần (ghi rõ % đề xuất)\n"
            . "- VOUCHER: Cấp voucher bù đắp (ghi rõ giá trị đề xuất)\n"
            . "- REJECT: Bác khiếu nại (kèm lý do rõ ràng)\n"
            . "- HOTEL_COMPENSATE: Yêu cầu khách sạn bồi thường trực tiếp\n"
            . "- NEED_MORE_INFO: Cần thu thập thêm bằng chứng trước khi quyết định\n\n"
            . "BƯỚC 4 — HÀNH ĐỘNG THEO DÕI ĐỀ XUẤT\n"
            . "Liệt kê các bước cần thực hiện sau phán quyết.\n\n"
            . "Trả lời theo format CHÍNH XÁC:\n\n"
            . "ĐỀ XUẤT PHÁN QUYẾT: [REFUND_100 / REFUND_PARTIAL / VOUCHER / REJECT / HOTEL_COMPENSATE / NEED_MORE_INFO]\n\n"
            . "BÊN CHỊU TRÁCH NHIỆM: [hotel / customer / platform / third_party / mixed]\n\n"
            . "BƯỚC 1 — XÁC NHẬN THÔNG TIN:\n"
            . "(tóm tắt + những gì còn thiếu)\n\n"
            . "BƯỚC 2 — PHÂN TÍCH TRÁCH NHIỆM:\n"
            . "(phân tích từng bên)\n\n"
            . "BƯỚC 3 — PHÁN QUYẾT ĐỀ XUẤT:\n"
            . "(chi tiết phương án, % hoàn tiền hoặc giá trị voucher nếu có)\n\n"
            . "BƯỚC 4 — HÀNH ĐỘNG TIẾP THEO:\n"
            . "(danh sách hành động theo dõi)\n\n"
            . "LƯU Ý ĐẶC BIỆT: (nếu có — ví dụ: cần xác nhận cấp trên, pattern vi phạm lặp lại, rủi ro pháp lý...)";
    }

    private static function extractAiRecommendation(string $aiText): string
    {
        if (preg_match('/ĐỀ XUẤT PHÁN QUYẾT\s*:\s*\[?(REFUND_100|REFUND_PARTIAL|VOUCHER|REJECT|HOTEL_COMPENSATE|NEED_MORE_INFO)\]?/u', $aiText, $m)) {
            return $m[1];
        }
        return 'NEED_MORE_INFO';
    }

    private static function extractAiFaultParty(string $aiText): string
    {
        if (preg_match('/BÊN CHỊU TRÁCH NHIỆM\s*:\s*\[?(hotel|customer|platform|third_party|mixed)\]?/u', $aiText, $m)) {
            return $m[1];
        }
        return 'mixed';
    }

    // -----------------------------------------------------------------------
    // Form
    // -----------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([
            // ---- Step 1: Information ----
            Forms\Components\Section::make('BƯỚC 1 — THÔNG TIN TRANH CHẤP')
                ->description('Mã booking, timeline sự việc, bằng chứng')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Select::make('booking_id')
                        ->label('Mã đặt phòng')
                        ->searchable()
                        ->getSearchResultsUsing(fn(string $s) => Booking::where('order_code', 'like', "%{$s}%")
                            ->orWhere('full_name', 'like', "%{$s}%")
                            ->limit(20)
                            ->get()
                            ->mapWithKeys(fn($b) => [$b->id => $b->order_code . ' — ' . $b->full_name]))
                        ->getOptionLabelUsing(fn($v) => optional(Booking::find($v))->order_code . ' — ' . optional(Booking::find($v))->full_name)
                        ->nullable(),

                    Forms\Components\Select::make('hotel_id')
                        ->label('Khách sạn liên quan')
                        ->searchable()
                        ->options(Hotel::orderBy('name')->pluck('name', 'id'))
                        ->nullable(),

                    Forms\Components\Select::make('type')
                        ->label('Loại tranh chấp')
                        ->options(Dispute::typeLabels())
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('priority')
                        ->label('Mức độ ưu tiên')
                        ->options(['normal' => '🔵 Thường (xử lý trong 24h)', 'urgent' => '🔴 Khẩn cấp (xử lý trong 4h)'])
                        ->default('normal')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Trạng thái xử lý')
                        ->options(Dispute::statusLabels())
                        ->default('open')
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->label('Tiêu đề khiếu nại')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label('Nội dung khiếu nại (theo lời khách)')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('timeline')
                        ->label('Timeline sự việc (theo thứ tự thời gian)')
                        ->rows(4)
                        ->placeholder("VD:\n- 14/05: Khách đặt phòng, nhận email xác nhận\n- 15/05: Khách đến check-in, được báo hết phòng\n- 15/05: Khách liên hệ StayGo khiếu nại")
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('hotel_response')
                        ->label('Phản hồi của khách sạn (nếu có)')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('evidence')
                        ->label('Bằng chứng đính kèm')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Loại bằng chứng')
                                ->options([
                                    'photo'   => '📷 Ảnh chụp',
                                    'video'   => '🎥 Video',
                                    'email'   => '📧 Email',
                                    'chat'    => '💬 Lịch sử chat',
                                    'receipt' => '🧾 Biên lai / hoá đơn',
                                    'other'   => '📎 Khác',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('description')
                                ->label('Mô tả / đường dẫn')
                                ->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Thêm bằng chứng')
                        ->columnSpanFull(),
                ])->columns(2),

            // ---- Step 2: Responsibility ----
            Forms\Components\Section::make('BƯỚC 2 — ĐÁNH GIÁ TRÁCH NHIỆM')
                ->description('Xác định bên chịu lỗi dựa trên bằng chứng')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    Forms\Components\Select::make('fault_party')
                        ->label('Bên chịu trách nhiệm')
                        ->options(Dispute::faultLabels())
                        ->nullable(),
                    Forms\Components\Textarea::make('fault_details')
                        ->label('Chi tiết phân tích trách nhiệm')
                        ->rows(4)
                        ->placeholder("Ghi rõ lý do xác định trách nhiệm của từng bên:\n- Khách sạn: ...\n- Khách hàng: ...\n- Nền tảng: ...\n- Bên thứ ba: ...")
                        ->columnSpanFull(),
                ])->columns(2),

            // ---- AI Analysis ----
            Forms\Components\Section::make('KẾT QUẢ PHÂN TÍCH AI')
                ->description('Nhấn "AI Phân tích" từ danh sách để tự động điền kết quả')
                ->icon('heroicon-o-sparkles')
                ->schema([
                    Forms\Components\Select::make('ai_recommendation')
                        ->label('Đề xuất của AI')
                        ->options(Dispute::aiRecommendationLabels())
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('ai_analyzed_at')
                        ->label('Thời gian phân tích')
                        ->disabled(),
                    Forms\Components\Textarea::make('ai_analysis')
                        ->label('Nội dung phân tích AI (4 bước)')
                        ->rows(8)
                        ->disabled()
                        ->columnSpanFull(),
                ])->columns(2)->collapsible(),

            // ---- Step 3: Verdict ----
            Forms\Components\Section::make('BƯỚC 3 — PHÁN QUYẾT')
                ->description('Quyết định cuối cùng của chuyên viên xử lý tranh chấp')
                ->icon('heroicon-o-scale')
                ->schema([
                    Forms\Components\Select::make('verdict')
                        ->label('Phán quyết')
                        ->options(Dispute::verdictLabels())
                        ->reactive()
                        ->nullable(),

                    Forms\Components\TextInput::make('refund_percentage')
                        ->label('% hoàn tiền')
                        ->numeric()->suffix('%')
                        ->minValue(0)->maxValue(100)
                        ->visible(fn(Forms\Get $get) => $get('verdict') === 'refund_partial'),

                    Forms\Components\TextInput::make('refund_amount')
                        ->label('Số tiền hoàn (VND)')
                        ->numeric()
                        ->visible(fn(Forms\Get $get) => in_array($get('verdict'), ['refund_full', 'refund_partial'])),

                    Forms\Components\TextInput::make('voucher_amount')
                        ->label('Giá trị voucher (VND)')
                        ->numeric()
                        ->visible(fn(Forms\Get $get) => $get('verdict') === 'voucher'),

                    Forms\Components\Toggle::make('requires_supervisor')
                        ->label('Cần xác nhận cấp trên (> 5,000,000 VND)')
                        ->inline(false),

                    Forms\Components\Textarea::make('verdict_details')
                        ->label('Lý do & Chi tiết phán quyết')
                        ->rows(5)
                        ->placeholder("Ghi rõ:\n1. Căn cứ pháp lý / chính sách áp dụng\n2. Lý do quyết định\n3. Thông báo gửi đến các bên")
                        ->columnSpanFull(),
                ])->columns(2),

            // ---- Step 4: Follow-up ----
            Forms\Components\Section::make('BƯỚC 4 — HÀNH ĐỘNG THEO DÕI')
                ->icon('heroicon-o-arrow-path')
                ->schema([
                    Forms\Components\Toggle::make('penalty_applied')
                        ->label('Đã ghi vi phạm cho khách sạn')
                        ->inline(false),
                    Forms\Components\Toggle::make('faq_updated')
                        ->label('Đã cập nhật FAQ/knowledge base')
                        ->inline(false),
                    Forms\Components\DateTimePicker::make('customer_notified_at')
                        ->label('Đã thông báo khách hàng lúc'),
                    Forms\Components\DateTimePicker::make('hotel_notified_at')
                        ->label('Đã thông báo khách sạn lúc'),
                    Forms\Components\Textarea::make('penalty_details')
                        ->label('Chi tiết vi phạm / penalty')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2)->collapsible(),
        ]);
    }

    // -----------------------------------------------------------------------
    // Table
    // -----------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $q) => $q->with(['booking', 'user', 'hotel']))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')->sortable()->width(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề khiếu nại')
                    ->description(fn(Dispute $r) => $r->booking?->order_code
                        ? 'Booking: ' . $r->booking->order_code . ' | ' . $r->booking->full_name
                        : ($r->user?->full_name ?? '—'))
                    ->searchable()
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Khách sạn')
                    ->placeholder('—')
                    ->limit(25),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->formatStateUsing(fn($s) => Dispute::typeLabels()[$s] ?? $s)
                    ->badge()
                    ->color(fn($s) => match($s) {
                        'no_show', 'slow_refund'    => 'gray',
                        'quality', 'hidden_fees'    => 'warning',
                        'overbooking', 'misconduct' => 'danger',
                        default                     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Ưu tiên')
                    ->formatStateUsing(fn($s) => $s === 'urgent' ? '🔴 Khẩn' : '🔵 Thường')
                    ->badge()
                    ->color(fn($s) => $s === 'urgent' ? 'danger' : 'info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($s) => Dispute::statusLabels()[$s] ?? $s)
                    ->badge()
                    ->color(fn($s) => match($s) {
                        'closed'                                         => 'gray',
                        'open', 'pending_hotel', 'pending_customer'      => 'warning',
                        'investigating'                                  => 'info',
                        'resolved'                                       => 'success',
                        'escalated'                                      => 'danger',
                        default                                          => 'gray',
                    }),

                Tables\Columns\TextColumn::make('ai_recommendation')
                    ->label('AI đề xuất')
                    ->placeholder('—')
                    ->formatStateUsing(fn($s) => Dispute::aiRecommendationLabels()[$s] ?? $s)
                    ->badge()
                    ->color(fn($s) => match($s) {
                        'REFUND_100', 'REFUND_PARTIAL'  => 'success',
                        'VOUCHER', 'HOTEL_COMPENSATE'   => 'info',
                        'REJECT'                        => 'danger',
                        'NEED_MORE_INFO'                => 'gray',
                        default                         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('verdict')
                    ->label('Phán quyết')
                    ->placeholder('Chưa có')
                    ->formatStateUsing(fn($s) => match ($s) {
                        'refund_full'      => 'Hoàn 100%',
                        'refund_partial'   => 'Hoàn một phần',
                        'voucher'          => 'Voucher',
                        'rejected'         => 'Bác KN',
                        'hotel_compensate' => 'KS bồi thường',
                        default            => '—',
                    })
                    ->badge()
                    ->color(fn($s) => match($s) {
                        'refund_full', 'refund_partial' => 'success',
                        'voucher', 'hotel_compensate'   => 'info',
                        'rejected'                      => 'danger',
                        default                         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('deadline_at')
                    ->label('Hạn xử lý')
                    ->formatStateUsing(function (Dispute $r) {
                        if (!$r->deadline_at) return '—';
                        if ($r->isOverdue()) {
                            return '⚠️ Quá hạn ' . $r->deadline_at->diffForHumans();
                        }
                        return $r->deadline_at->format('d/m H:i');
                    })
                    ->color(fn(Dispute $r) => $r->isOverdue() ? 'danger' : null)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tiếp nhận')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Loại tranh chấp')
                    ->options(Dispute::typeLabels()),
                SelectFilter::make('priority')
                    ->label('Ưu tiên')
                    ->options(['normal' => 'Thường', 'urgent' => 'Khẩn cấp']),
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Dispute::statusLabels()),
                SelectFilter::make('verdict')
                    ->label('Phán quyết')
                    ->options(Dispute::verdictLabels()),
            ])
            ->actions([
                // ---- AI 4-step analysis ----
                Action::make('ai_analyze')
                    ->label('AI Phân tích')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->button()
                    ->visible(fn(Dispute $r) => !$r->isResolved())
                    ->requiresConfirmation()
                    ->modalHeading('Phân tích tranh chấp bằng AI?')
                    ->modalDescription('AI sẽ đọc toàn bộ thông tin, áp dụng framework 4 bước và đưa ra đề xuất phán quyết. Quá trình mất 15-30 giây.')
                    ->modalSubmitActionLabel('Bắt đầu phân tích')
                    ->action(function (Dispute $record) {
                        $prompt = self::buildAnalysisPrompt($record);
                        $reply  = self::callAi($prompt);

                        $record->update([
                            'ai_analysis'       => $reply,
                            'ai_recommendation' => self::extractAiRecommendation($reply),
                            'fault_party'       => $record->fault_party ?? self::extractAiFaultParty($reply),
                            'ai_analyzed_at'    => now(),
                            'status'            => $record->status === 'open' ? 'investigating' : $record->status,
                        ]);

                        Notification::make()
                            ->title('AI đã phân tích xong tranh chấp #' . $record->id)
                            ->body('Mở "Phán quyết" để hoàn tất quyết định.')
                            ->success()
                            ->duration(8000)
                            ->send();
                    }),

                // ---- Verdict action ----
                Action::make('make_verdict')
                    ->label('Phán quyết')
                    ->icon('heroicon-o-scale')
                    ->color('primary')
                    ->button()
                    ->visible(fn(Dispute $r) => !$r->isResolved())
                    ->modalWidth('3xl')
                    ->fillForm(fn(Dispute $record) => [
                        'fault_party'    => $record->fault_party,
                        'fault_details'  => $record->fault_details ?? $record->ai_analysis,
                        'verdict'        => $record->verdict,
                        'verdict_details'=> $record->verdict_details,
                        'refund_amount'  => $record->refund_amount,
                        'refund_percentage' => $record->refund_percentage,
                        'voucher_amount' => $record->voucher_amount,
                        'requires_supervisor' => $record->requires_supervisor,
                        'penalty_applied'=> $record->penalty_applied,
                        'penalty_details'=> $record->penalty_details,
                    ])
                    ->form([
                        Forms\Components\Placeholder::make('ai_hint')
                            ->label('Đề xuất AI')
                            ->content(fn($record) => $record?->ai_recommendation
                                ? '🤖 AI đề xuất: ' . (Dispute::aiRecommendationLabels()[$record->ai_recommendation] ?? $record->ai_recommendation)
                                    . ' | Bên lỗi: ' . (Dispute::faultLabels()[$record->fault_party ?? ''] ?? 'Chưa xác định')
                                : '⚠️ Chưa có phân tích AI. Nhấn "AI Phân tích" để lấy đề xuất trước.')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('fault_party')
                            ->label('Bên chịu trách nhiệm')
                            ->options(Dispute::faultLabels())
                            ->required(),

                        Forms\Components\Select::make('verdict')
                            ->label('Phán quyết')
                            ->options(Dispute::verdictLabels())
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('refund_percentage')
                            ->label('% hoàn tiền')
                            ->numeric()->suffix('%')
                            ->minValue(1)->maxValue(99)
                            ->visible(fn(Forms\Get $get) => $get('verdict') === 'refund_partial'),

                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Số tiền hoàn (VND)')
                            ->numeric()
                            ->visible(fn(Forms\Get $get) => in_array($get('verdict'), ['refund_full', 'refund_partial'])),

                        Forms\Components\TextInput::make('voucher_amount')
                            ->label('Giá trị voucher (VND)')
                            ->numeric()
                            ->visible(fn(Forms\Get $get) => $get('verdict') === 'voucher'),

                        Forms\Components\Toggle::make('requires_supervisor')
                            ->label('⚠️ Cần xác nhận cấp trên (hoàn tiền > 5,000,000 VND)')
                            ->inline(false),

                        Forms\Components\Textarea::make('fault_details')
                            ->label('Phân tích trách nhiệm chi tiết')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('verdict_details')
                            ->label('Lý do & chi tiết phán quyết')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('penalty_applied')
                            ->label('Ghi vi phạm vào hồ sơ khách sạn')
                            ->inline(false),

                        Forms\Components\Textarea::make('penalty_details')
                            ->label('Chi tiết vi phạm')
                            ->rows(2)
                            ->visible(fn(Forms\Get $get) => (bool) $get('penalty_applied'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->action(function (Dispute $record, array $data) {
                        $refundAmount = $data['refund_amount'] ?? null;

                        // Force supervisor flag if refund > 5M VND
                        if ($refundAmount && $refundAmount > 5_000_000) {
                            $data['requires_supervisor'] = true;
                        }

                        $record->update([
                            'fault_party'    => $data['fault_party'],
                            'fault_details'  => $data['fault_details'],
                            'verdict'        => $data['verdict'],
                            'verdict_details'=> $data['verdict_details'],
                            'refund_amount'  => $refundAmount,
                            'refund_percentage' => $data['refund_percentage'] ?? null,
                            'voucher_amount' => $data['voucher_amount'] ?? null,
                            'requires_supervisor' => $data['requires_supervisor'] ?? false,
                            'penalty_applied'=> $data['penalty_applied'] ?? false,
                            'penalty_details'=> $data['penalty_details'] ?? null,
                            'status'         => $data['requires_supervisor'] ? 'escalated' : 'resolved',
                            'resolved_by'    => Auth::id(),
                            'resolved_at'    => $data['requires_supervisor'] ? null : now(),
                        ]);

                        if ($data['penalty_applied'] ?? false) {
                            // Record hotel violation in partner profile notes
                            $partnerProfile = \App\Models\HotelPartnerProfile::whereHas(
                                'hotel', fn($q) => $q->where('id', $record->hotel_id)
                            )->first();
                            if ($partnerProfile) {
                                $note = "\n[" . now()->format('d/m/Y') . "] ⚠️ Vi phạm #" . $record->id . ': ' . $record->title;
                                $partnerProfile->update(['notes' => ($partnerProfile->notes ?? '') . $note]);
                            }
                        }

                        // Gửi email kết quả cho khách hàng (nếu không cần supervisor)
                        if (!($data['requires_supervisor'] ?? false)) {
                            $record->load(['booking', 'user', 'hotel']);

                            // Email + DB notification cho khách
                            try {
                                $customer = $record->user
                                    ?? ($record->booking?->user_id ? User::find($record->booking->user_id) : null);
                                if ($customer) {
                                    // Có tài khoản → gửi qua Notification (cả mail lẫn DB nếu cần)
                                    $customer->notify(new DisputeVerdictCustomerNotification($record));
                                } elseif ($record->booking?->email) {
                                    // Khách vãng lai — on-demand notification
                                    \Illuminate\Support\Facades\Notification::route(
                                        'mail', $record->booking->email
                                    )->notify(new DisputeVerdictCustomerNotification($record));
                                }
                            } catch (\Exception $e) {
                                Log::warning('DisputeVerdict customer email failed: ' . $e->getMessage());
                            }

                            // Email cho hotel partner nếu có lỗi liên quan đến khách sạn
                            try {
                                if (in_array($data['fault_party'] ?? '', ['hotel', 'mixed'])
                                    || ($data['penalty_applied'] ?? false)) {
                                    $partnerUserId = $record->hotel?->partner_user_id;
                                    if ($partnerUserId) {
                                        $partner = User::find($partnerUserId);
                                        $partner?->notify(new DisputeVerdictHotelNotification($record));
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::warning('DisputeVerdict hotel email failed: ' . $e->getMessage());
                            }

                            // Cập nhật mốc thời gian thông báo
                            $record->update(['customer_notified_at' => now()]);
                        }

                        $label = $data['requires_supervisor']
                            ? 'Đã leo thang lên cấp trên để phê duyệt'
                            : 'Phán quyết ghi nhận — Email thông báo đã gửi cho các bên';

                        Notification::make()->title($label)->success()->send();
                    }),

                // ---- Mark notified ----
                Action::make('notify_customer')
                    ->label('Đã thông báo KH')
                    ->icon('heroicon-o-bell')
                    ->color('success')
                    ->iconButton()
                    ->visible(fn(Dispute $r) => $r->verdict && !$r->customer_notified_at)
                    ->action(function (Dispute $record) {
                        $record->update(['customer_notified_at' => now()]);
                        Notification::make()->title('Đã đánh dấu thông báo khách hàng')->success()->send();
                    }),

                Action::make('notify_hotel')
                    ->label('Đã thông báo KS')
                    ->icon('heroicon-o-building-office')
                    ->color('info')
                    ->iconButton()
                    ->visible(fn(Dispute $r) => $r->penalty_applied && !$r->hotel_notified_at)
                    ->action(function (Dispute $record) {
                        $record->update(['hotel_notified_at' => now()]);
                        Notification::make()->title('Đã đánh dấu thông báo khách sạn')->success()->send();
                    }),

                Tables\Actions\EditAction::make()->label('Chi tiết')->iconButton(),
            ])
            ->headerActions([
                Action::make('urgent_filter')
                    ->label('Xem khẩn cấp')
                    ->icon('heroicon-o-fire')
                    ->color('danger')
                    ->url(fn() => static::getUrl('index') . '?tableFilters[priority][value]=urgent'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDisputes::route('/'),
            'create' => Pages\CreateDispute::route('/create'),
            'edit'   => Pages\EditDispute::route('/{record}/edit'),
        ];
    }
}
