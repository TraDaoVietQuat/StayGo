<?php

namespace App\Filament\HotelPartner\Resources;

use App\Filament\HotelPartner\Resources\PartnerReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartnerReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon   = 'heroicon-o-star';
    protected static ?string $navigationLabel  = 'Đánh giá';
    protected static ?string $modelLabel       = 'Đánh giá';
    protected static ?string $pluralModelLabel = 'Đánh giá của khách';
    protected static ?string $navigationGroup  = 'Đánh giá';
    protected static ?int    $navigationSort   = 1;

    // -----------------------------------------------------------------------
    // Scope: chỉ hiển thị đánh giá của khách sạn đang đăng nhập
    // -----------------------------------------------------------------------

    public static function getEloquentQuery(): Builder
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        return parent::getEloquentQuery()
            ->where('hotel_id', $hotel?->id ?? 0)
            ->where('is_active', true)
            ->with(['user', 'booking'])
            ->latest('created_at');
    }

    // -----------------------------------------------------------------------
    // Badge cảnh báo đánh giá thấp chưa phản hồi
    // -----------------------------------------------------------------------

    public static function getNavigationBadge(): ?string
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        if (!$hotel) return null;

        try {
            $count = Review::where('hotel_id', $hotel->id)
                ->where('is_active', true)
                ->where('rating', '<=', 2)
                ->whereNull('partner_reply')
                ->count();

            return $count > 0 ? (string) $count : null;
        } catch (\Exception) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // -----------------------------------------------------------------------
    // Form
    // -----------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // -----------------------------------------------------------------------
    // Table
    // -----------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Khách hàng')
                    ->description(fn(Review $r) => $r->booking?->order_code ?? ''),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Điểm')
                    ->formatStateUsing(fn($s) => str_repeat('⭐', (int)$s) . " ($s/5)")
                    ->description(fn(Review $r) => collect([
                        $r->cleanliness    ? "🧹{$r->cleanliness}" : null,
                        $r->service_score  ? "🤝{$r->service_score}" : null,
                        $r->location_score ? "📍{$r->location_score}" : null,
                        $r->value_score    ? "💰{$r->value_score}" : null,
                    ])->filter()->implode(' | '))
                    ->badge()
                    ->color(fn($s) => (int)$s >= 4 ? 'success' : ((int)$s >= 3 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Nhận xét')->limit(80)->wrap(),

                Tables\Columns\TextColumn::make('partner_reply')
                    ->label('Phản hồi của bạn')->limit(60)->wrap()
                    ->placeholder('Chưa phản hồi'),

                Tables\Columns\TextColumn::make('partner_replied_at')
                    ->label('Ngày phản hồi')->date('d/m/Y')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đánh giá')->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Mức điểm')
                    ->options([
                        '5' => '⭐⭐⭐⭐⭐ 5 sao',
                        '4' => '⭐⭐⭐⭐ 4 sao',
                        '3' => '⭐⭐⭐ 3 sao',
                        '2' => '⭐⭐ 2 sao',
                        '1' => '⭐ 1 sao',
                    ]),
                Tables\Filters\Filter::make('unresponded')
                    ->label('Chưa phản hồi')
                    ->query(fn(Builder $q) => $q->whereNull('partner_reply')),
                Tables\Filters\Filter::make('low_rating')
                    ->label('Đánh giá thấp (1-2 sao)')
                    ->query(fn(Builder $q) => $q->where('rating', '<=', 2)),
            ])
            ->actions([
                // ── AI Reply: gọi AI gợi ý, cho phép chỉnh sửa trước khi lưu ──
                Action::make('ai_reply')
                    ->label('✨ Gợi ý AI')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->modalHeading(fn(Review $r) => 'Gợi ý phản hồi AI — ' . str_repeat('⭐', (int)$r->rating) . " ({$r->rating}/5)")
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Lưu phản hồi')
                    ->fillForm(function (Review $record): array {
                        $hotel = auth('hotel_partner')->user()?->managedHotel;
                        $draft = self::generateAiReply($record, $hotel?->name ?? 'khách sạn');
                        return ['reply' => $draft];
                    })
                    ->form([
                        Forms\Components\Placeholder::make('review_preview')
                            ->label('Đánh giá của khách')
                            ->content(fn(Review $r) => "\"{$r->comment}\""),

                        Forms\Components\Placeholder::make('sub_scores_ai')
                            ->label('Điểm chi tiết')
                            ->content(fn(Review $r) => collect([
                                $r->cleanliness    ? "🧹 Vệ sinh: {$r->cleanliness}/5" : null,
                                $r->service_score  ? "🤝 Dịch vụ: {$r->service_score}/5" : null,
                                $r->location_score ? "📍 Vị trí: {$r->location_score}/5" : null,
                                $r->value_score    ? "💰 Giá trị: {$r->value_score}/5" : null,
                            ])->filter()->implode('   ') ?: '—')
                            ->visible(fn(Review $r) => $r->cleanliness || $r->service_score || $r->location_score || $r->value_score),

                        Forms\Components\Textarea::make('reply')
                            ->label('Gợi ý phản hồi (có thể chỉnh sửa trước khi lưu)')
                            ->required()
                            ->rows(6)
                            ->helperText('AI đã soạn sẵn dựa trên điểm đánh giá và nội dung nhận xét. Bạn có thể chỉnh sửa tùy ý.'),
                    ])
                    ->action(function (Review $record, array $data): void {
                        $record->update([
                            'partner_reply'      => $data['reply'],
                            'partner_replied_at' => now(),
                        ]);
                        Notification::make()->title('Đã lưu phản hồi')->success()->send();
                    }),

                // ── Reply thủ công ──
                Action::make('reply')
                    ->label('Phản hồi')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->modalHeading('Viết phản hồi')
                    ->form([
                        Forms\Components\Placeholder::make('review_preview')
                            ->label('Đánh giá của khách')
                            ->content(fn(Review $r) => "\"{$r->comment}\""),

                        Forms\Components\Placeholder::make('sub_scores_reply')
                            ->label('Điểm chi tiết')
                            ->content(fn(Review $r) => collect([
                                $r->cleanliness    ? "🧹 Vệ sinh: {$r->cleanliness}/5" : null,
                                $r->service_score  ? "🤝 Dịch vụ: {$r->service_score}/5" : null,
                                $r->location_score ? "📍 Vị trí: {$r->location_score}/5" : null,
                                $r->value_score    ? "💰 Giá trị: {$r->value_score}/5" : null,
                            ])->filter()->implode('   ') ?: '—')
                            ->visible(fn(Review $r) => $r->cleanliness || $r->service_score || $r->location_score || $r->value_score),

                        Forms\Components\Textarea::make('reply')
                            ->label('Nội dung phản hồi')
                            ->required()->rows(5)
                            ->placeholder('Cảm ơn quý khách đã đánh giá...'),
                    ])
                    ->fillForm(fn(Review $r) => ['reply' => $r->partner_reply ?? ''])
                    ->action(function (Review $record, array $data): void {
                        $record->update([
                            'partner_reply'      => $data['reply'],
                            'partner_replied_at' => now(),
                        ]);
                        Notification::make()->title('Đã lưu phản hồi')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // -----------------------------------------------------------------------
    // AI helper: gọi OpenRouter → OpenAI fallback
    // -----------------------------------------------------------------------

    private static function callAi(string $prompt): string
    {
        // OpenRouter (primary)
        $orKey = config('services.openrouter.api_key');
        if ($orKey) {
            try {
                $res = Http::timeout(25)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $orKey,
                        'HTTP-Referer'  => config('app.url'),
                        'X-Title'       => 'StayGo Partner Reviews',
                    ])
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model'    => config('services.openrouter.model', 'google/gemma-3-12b-it:free'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

                if ($res->successful()) {
                    return trim($res->json('choices.0.message.content') ?? '');
                }
            } catch (\Exception $e) {
                Log::warning('PartnerReview OpenRouter failed: ' . $e->getMessage());
            }
        }

        // OpenAI (fallback)
        $oaKey = config('services.openai.api_key');
        if ($oaKey) {
            try {
                $res = Http::timeout(25)
                    ->withToken($oaKey)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model'    => config('services.openai.model', 'gpt-3.5-turbo'),
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

                if ($res->successful()) {
                    return trim($res->json('choices.0.message.content') ?? '');
                }
            } catch (\Exception $e) {
                Log::warning('PartnerReview OpenAI fallback failed: ' . $e->getMessage());
            }
        }

        return '';
    }

    private static function generateAiReply(Review $review, string $hotelName): string
    {
        $guestName  = $review->user?->full_name ?? 'Quý khách';
        $rating     = (int) $review->rating;
        $comment    = $review->comment ?? '';

        $ratingLabel = match(true) {
            $rating >= 4 => 'tốt (4-5 sao)',
            $rating === 3 => 'trung bình (3 sao)',
            default       => 'thấp (1-2 sao)',
        };

        $guidelines = match(true) {
            $rating >= 4 => <<<'TXT'
- Cảm ơn chân thành, đề cập điểm họ khen cụ thể
- Ngắn gọn 3-4 câu, không sáo rỗng
- Mời quay lại, gợi ý trải nghiệm khác
TXT,
            $rating === 3 => <<<'TXT'
- Cảm ơn phản hồi thành thật
- Ghi nhận điểm chưa hài lòng, không biện hộ
- Nêu hành động cải thiện cụ thể
- Mong được phục vụ lại
TXT,
            default => <<<'TXT'
- Xin lỗi chân thành, không phủ nhận sự việc
- Nêu rõ đã/đang khắc phục như thế nào
- Đề nghị liên hệ trực tiếp để giải quyết
- KHÔNG tranh luận, KHÔNG tấn công lại khách
TXT,
        };

        $prompt = <<<PROMPT
Bạn là quản lý khách sạn "{$hotelName}". Hãy viết phản hồi chuyên nghiệp cho đánh giá sau.

THÔNG TIN:
- Tên khách: {$guestName}
- Điểm đánh giá: {$rating}/5 ({$ratingLabel})
- Nội dung nhận xét: "{$comment}"

NGUYÊN TẮC:
{$guidelines}

Viết phản hồi bằng tiếng Việt, lịch sự, cá nhân hóa (gọi tên khách, đề cập điều họ nhắc cụ thể).
Chỉ trả về nội dung phản hồi, không thêm ghi chú hay giải thích.
PROMPT;

        $reply = self::callAi($prompt);

        if (empty($reply)) {
            // Template mặc định nếu AI không phản hồi
            return match(true) {
                $rating >= 4 => "Kính gửi {$guestName}, Cảm ơn bạn rất nhiều vì đã dành thời gian chia sẻ trải nghiệm tại {$hotelName}. Chúng tôi rất vui khi biết bạn hài lòng và sẽ tiếp tục nỗ lực để mang lại trải nghiệm tốt nhất. Hy vọng sớm được đón tiếp bạn trong chuyến đến tiếp theo!",
                $rating === 3 => "Kính gửi {$guestName}, Cảm ơn bạn đã chia sẻ phản hồi thành thật. Chúng tôi ghi nhận những điểm chưa làm bạn hài lòng và sẽ cải thiện ngay. Rất mong được phục vụ bạn trong lần tiếp theo với dịch vụ tốt hơn!",
                default       => "Kính gửi {$guestName}, Chúng tôi thành thật xin lỗi vì trải nghiệm chưa như kỳ vọng của bạn. Đây là điều chúng tôi coi trọng và đã ghi nhận để cải thiện ngay. Kính mời bạn liên hệ trực tiếp để chúng tôi hỗ trợ thêm.",
            };
        }

        return $reply;
    }

    // -----------------------------------------------------------------------
    // Pages
    // -----------------------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerReviews::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
