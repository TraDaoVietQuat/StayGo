<x-filament-panels::page>

<style>
.eas-tab { @apply cursor-pointer px-4 py-2.5 text-sm font-medium rounded-lg transition-colors; }
.eas-tab.active { @apply bg-rose-600 text-white shadow; }
.eas-tab:not(.active) { @apply text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800; }
.eas-card { @apply rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm; }
.eas-label { @apply block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5; }
.eas-input { @apply w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500 transition; }
.eas-btn { @apply inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed; }
.eas-btn-primary { @apply bg-rose-600 hover:bg-rose-700 text-white; }
.eas-btn-secondary { @apply bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200; }
.eas-output { @apply rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 p-4 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap font-mono leading-relaxed overflow-auto; min-height: 220px; max-height: 520px; }
.eas-badge { @apply inline-block rounded-full px-2.5 py-0.5 text-xs font-bold; }
</style>

<div x-data="{
    tab: 'generate',
    // E-11
    genTrigger: 'win_back',
    genUserData: '',
    genContext: '',
    genResult: '',
    genLoading: false,
    // E-12
    scoreContent: '',
    scoreResult: '',
    scoreLoading: false,
    // E-13
    analyzeData: '',
    analyzeResult: '',
    analyzeLoading: false,

    csrf: document.querySelector('meta[name=csrf-token]')?.content ?? '',

    async callApi(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!res.ok || data.error) throw new Error(data.error ?? 'Lỗi không xác định');
        return data.result ?? '';
    },

    async runGenerate() {
        if (!this.genUserData.trim()) return;
        this.genLoading = true; this.genResult = '';
        try {
            this.genResult = await this.callApi('{{ route('admin.email-ai.generate') }}', {
                trigger: this.genTrigger,
                user_data: this.genUserData,
                context: this.genContext,
            });
        } catch (e) { this.genResult = '❌ ' + e.message; }
        finally { this.genLoading = false; }
    },

    async runScore() {
        if (!this.scoreContent.trim()) return;
        this.scoreLoading = true; this.scoreResult = '';
        try {
            this.scoreResult = await this.callApi('{{ route('admin.email-ai.score') }}', {
                email_content: this.scoreContent,
            });
        } catch (e) { this.scoreResult = '❌ ' + e.message; }
        finally { this.scoreLoading = false; }
    },

    async runAnalyze() {
        if (!this.analyzeData.trim()) return;
        this.analyzeLoading = true; this.analyzeResult = '';
        try {
            this.analyzeResult = await this.callApi('{{ route('admin.email-ai.analyze') }}', {
                campaign_data: this.analyzeData,
            });
        } catch (e) { this.analyzeResult = '❌ ' + e.message; }
        finally { this.analyzeLoading = false; }
    },

    copyText(text) {
        navigator.clipboard?.writeText(text);
    },

    sampleUserData() {
        this.genUserData = JSON.stringify({
            user: {
                name: 'Nguyễn Văn An',
                email: 'an@example.com',
                member_since: '2023-03-15',
                tier: 'Silver',
                points_balance: 1240,
                preferred_city: 'Đà Lạt',
                avg_spend_per_night: 850000,
                last_booking_date: '2024-10-12',
                total_bookings: 4,
                favorite_hotel_types: ['resort', 'boutique']
            },
            trigger: this.genTrigger,
            context: {
                days_since_last_booking: 87,
                upcoming_holiday: 'Tết Nguyên Đán 2025',
                available_deals: ['10% off Đà Lạt resorts', 'Free breakfast at Ana Mandara']
            }
        }, null, 2);
    },

    sampleCampaign() {
        this.analyzeData = JSON.stringify({
            campaign_name: 'Win-back Q1 2025',
            sent_date: '2025-03-10',
            segment: 'Khách không đặt phòng >90 ngày, hạng Bronze-Silver',
            total_sent: 4820,
            delivered: 4756,
            bounced: 64,
            spam_complaints: 3,
            opened: 1021,
            clicked: 287,
            unsubscribed: 18,
            conversions: 43,
            revenue_attributed: 87500000,
            ab_test: {
                variant_a: { subject: 'Chúng tôi nhớ bạn! Voucher 15% đang chờ', opens: 512, clicks: 142 },
                variant_b: { subject: 'Món quà đặc biệt dành riêng cho bạn', opens: 509, clicks: 145 }
            },
            previous_campaign: { open_rate: 0.18, ctr: 0.052, conversion_rate: 0.008 }
        }, null, 2);
    }
}" class="space-y-5">

    {{-- ===== TAB NAV ===== --}}
    <div class="flex gap-2 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl w-fit">
        <button class="eas-tab" :class="{ active: tab === 'generate' }" @click="tab = 'generate'">
            ✍️ E-11 Sinh email
        </button>
        <button class="eas-tab" :class="{ active: tab === 'score' }" @click="tab = 'score'">
            📊 E-12 Chấm điểm
        </button>
        <button class="eas-tab" :class="{ active: tab === 'analyze' }" @click="tab = 'analyze'">
            📈 E-13 Phân tích KPI
        </button>
    </div>

    {{-- ===================================================================
         E-11 — PERSONALIZED EMAIL GENERATOR
    =================================================================== --}}
    <div x-show="tab === 'generate'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- LEFT: Form --}}
        <div class="eas-card p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800 dark:text-gray-100">Sinh email cá nhân hóa</h2>
                <span class="eas-badge bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300">E-11</span>
            </div>

            <div>
                <label class="eas-label">Trigger hành vi</label>
                <select class="eas-input" x-model="genTrigger">
                    <option value="win_back">🔄 win_back — Khách lâu không đặt (>60 ngày)</option>
                    <option value="upsell">⬆️ upsell — Sau booking xác nhận</option>
                    <option value="loyalty_upgrade">⭐ loyalty_upgrade — Sắp lên hạng thành viên</option>
                    <option value="cart_abandon">🛒 cart_abandon — Xem phòng chưa đặt</option>
                    <option value="seasonal">🎉 seasonal — Dịp lễ / mùa cao điểm</option>
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="eas-label" style="margin:0">Dữ liệu người dùng (JSON)</label>
                    <button class="text-xs text-rose-600 hover:underline" @click="sampleUserData()">Điền mẫu</button>
                </div>
                <textarea class="eas-input font-mono text-xs" rows="12" x-model="genUserData"
                    placeholder='{"user": {"name": "...", "tier": "Silver", ...}, "context": {...}}'></textarea>
            </div>

            <div>
                <label class="eas-label">Context bổ sung (tùy chọn)</label>
                <textarea class="eas-input" rows="3" x-model="genContext"
                    placeholder="VD: Người dùng đã hủy 1 booking gần đây. Voucher có giá trị 200,000đ. Deal Đà Lạt đang hot."></textarea>
            </div>

            <button class="eas-btn eas-btn-primary w-full justify-center" @click="runGenerate()" :disabled="genLoading || !genUserData.trim()">
                <span x-show="!genLoading">✨ Sinh email ngay</span>
                <span x-show="genLoading">⏳ Đang soạn...</span>
            </button>
        </div>

        {{-- RIGHT: Output --}}
        <div class="eas-card p-5 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Email được sinh</h3>
                <button x-show="genResult" class="eas-btn eas-btn-secondary text-xs py-1.5 px-3" @click="copyText(genResult)">
                    📋 Copy
                </button>
            </div>
            <div class="eas-output" x-text="genLoading ? '⏳ AI đang soạn email cá nhân hóa...' : (genResult || 'Kết quả sẽ hiển thị ở đây sau khi bạn nhấn Sinh email.\n\nEmail sẽ bao gồm:\n• Subject A & B cho A/B test\n• Preheader\n• Email body đầy đủ\n• Ghi chú personalization')"></div>
        </div>
    </div>

    {{-- ===================================================================
         E-12 — EMAIL QUALITY SCORER
    =================================================================== --}}
    <div x-show="tab === 'score'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- LEFT: Input --}}
        <div class="eas-card p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800 dark:text-gray-100">Chấm điểm & tối ưu email</h2>
                <span class="eas-badge bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">E-12</span>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-950 rounded-lg p-3 space-y-1">
                <p class="font-semibold text-blue-700 dark:text-blue-400">Thang điểm 100:</p>
                <p>📝 Subject Line — 25đ | 📄 Nội dung — 40đ | 🎯 CTA — 20đ | ⚙️ Kỹ thuật — 15đ</p>
                <p>Điểm ≥85: Xuất sắc | 70-84: Tốt | 50-69: TB | &lt;50: Cần cải thiện</p>
            </div>

            <div>
                <label class="eas-label">Dán email cần chấm điểm</label>
                <textarea class="eas-input" rows="16" x-model="scoreContent"
                    placeholder="Dán toàn bộ email vào đây, bao gồm:&#10;SUBJECT: ...&#10;PREHEADER: ...&#10;&#10;[Nội dung email body]&#10;&#10;[CTA]&#10;[Footer / unsubscribe]"></textarea>
            </div>

            <button class="eas-btn eas-btn-primary w-full justify-center" @click="runScore()" :disabled="scoreLoading || !scoreContent.trim()">
                <span x-show="!scoreLoading">📊 Chấm điểm ngay</span>
                <span x-show="scoreLoading">⏳ Đang phân tích...</span>
            </button>
        </div>

        {{-- RIGHT: Output --}}
        <div class="eas-card p-5 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Kết quả đánh giá</h3>
                <button x-show="scoreResult" class="eas-btn eas-btn-secondary text-xs py-1.5 px-3" @click="copyText(scoreResult)">
                    📋 Copy
                </button>
            </div>
            <div class="eas-output" x-text="scoreLoading ? '⏳ AI đang phân tích chất lượng email...' : (scoreResult || 'Kết quả sẽ hiển thị ở đây.\n\nBao gồm:\n• Điểm tổng XX/100\n• Bảng điểm 19 tiêu chí\n• Top 3 điểm mạnh\n• Top 3 cần cải thiện\n• Email đã tối ưu (rewrite nếu <70)\n• 2 variant subject A/B')"></div>
        </div>
    </div>

    {{-- ===================================================================
         E-13 — CAMPAIGN KPI ANALYZER
    =================================================================== --}}
    <div x-show="tab === 'analyze'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- LEFT: Input --}}
        <div class="eas-card p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800 dark:text-gray-100">Phân tích KPI chiến dịch</h2>
                <span class="eas-badge bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">E-13</span>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 bg-emerald-50 dark:bg-emerald-950 rounded-lg p-3 space-y-1">
                <p class="font-semibold text-emerald-700 dark:text-emerald-400">Phân tích theo OTA benchmark:</p>
                <p>📦 Delivery >98% | 📬 Open >25% (marketing) | 👆 CTR >3% | 🛒 Conversion >2%</p>
                <p>Kết quả: 6 sections + Action Plan 3-5 items cụ thể</p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="eas-label" style="margin:0">Dữ liệu chiến dịch (JSON hoặc text)</label>
                    <button class="text-xs text-emerald-600 hover:underline" @click="sampleCampaign()">Điền mẫu</button>
                </div>
                <textarea class="eas-input font-mono text-xs" rows="16" x-model="analyzeData"
                    placeholder="Nhập dữ liệu chiến dịch dạng JSON hoặc text:&#10;{&#10;  &quot;campaign_name&quot;: &quot;Win-back Q1&quot;,&#10;  &quot;total_sent&quot;: 5000,&#10;  &quot;opened&quot;: 1200,&#10;  &quot;clicked&quot;: 340,&#10;  &quot;conversions&quot;: 48,&#10;  &quot;revenue_attributed&quot;: 96000000,&#10;  &quot;ab_test&quot;: {...}&#10;}"></textarea>
            </div>

            <button class="eas-btn eas-btn-primary w-full justify-center" @click="runAnalyze()" :disabled="analyzeLoading || !analyzeData.trim()">
                <span x-show="!analyzeLoading">📈 Phân tích ngay</span>
                <span x-show="analyzeLoading">⏳ Đang phân tích...</span>
            </button>
        </div>

        {{-- RIGHT: Output --}}
        <div class="eas-card p-5 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Kết quả phân tích</h3>
                <button x-show="analyzeResult" class="eas-btn eas-btn-secondary text-xs py-1.5 px-3" @click="copyText(analyzeResult)">
                    📋 Copy
                </button>
            </div>
            <div class="eas-output" x-text="analyzeLoading ? '⏳ AI đang phân tích chiến dịch...' : (analyzeResult || 'Kết quả sẽ hiển thị ở đây.\n\nBao gồm 6 sections:\n1. Tổng quan chiến dịch\n2. Hiệu suất vs OTA benchmark\n3. Phân tích theo segment\n4. Kết quả A/B test\n5. Anomaly & Insights\n6. Action Plan 3-5 items')"></div>
        </div>
    </div>

</div>
</x-filament-panels::page>
