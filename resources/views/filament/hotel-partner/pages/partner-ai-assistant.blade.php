<x-filament-panels::page>

{{-- KPI Bar --}}
<div class="grid grid-cols-2 gap-3 mb-5 sm:grid-cols-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Đặt phòng hôm nay</div>
        <div class="mt-1 text-2xl font-bold text-blue-600" id="pkpi-bookings-today">—</div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Doanh thu tháng này</div>
        <div class="mt-1 text-2xl font-bold text-emerald-600" id="pkpi-revenue-month">—</div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Chờ xác nhận</div>
        <div class="mt-1 text-2xl font-bold text-amber-500" id="pkpi-pending">—</div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Điểm đánh giá TB</div>
        <div class="mt-1 text-2xl font-bold text-indigo-600" id="pkpi-rating">—</div>
    </div>
</div>

{{-- Chat Window --}}
<div class="flex flex-col rounded-2xl border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-900"
     style="height: calc(100vh - 290px); min-height: 440px;">

    {{-- Messages --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4" id="pai-messages">
        <div class="flex gap-3">
            <div class="pai-avatar shrink-0 flex h-8 w-8 items-center justify-center rounded-full text-white text-sm font-bold"
                 style="background:#2563eb;">AI</div>
            <div class="pai-bubble rounded-2xl rounded-tl-none bg-gray-100 px-4 py-3 text-sm text-gray-800 dark:bg-gray-800 dark:text-gray-100 max-w-2xl">
                Xin chào! Tôi là trợ lý AI của StayGo Partner. Tôi có thể giúp bạn:
                <br>• Phân tích doanh thu, tỷ lệ lấp đầy phòng, ADR, RevPAR
                <br>• Tối ưu giá phòng theo mùa, dịp lễ, cuối tuần
                <br>• Đánh giá hiệu suất đặt phòng và xu hướng khách hàng
                <br>• Xử lý đánh giá thấp và cải thiện uy tín
                <br>• Gợi ý chính sách hủy phòng, chương trình khuyến mãi
                <br><br>Hỏi tôi bất cứ điều gì về khách sạn của bạn nhé!
            </div>
        </div>
    </div>

    {{-- Quick suggestions --}}
    <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2 flex gap-2 flex-wrap" id="pai-suggestions">
        @foreach([
            'Phân tích doanh thu tháng này',
            'Tỷ lệ lấp đầy phòng hiện tại bao nhiêu?',
            'Khách check-in trong 7 ngày tới',
            'Đánh giá thấp gần đây cần xử lý',
            'Gợi ý tối ưu giá cuối tuần',
            'Chỉ số RevPAR và ADR của tôi',
        ] as $s)
        <button onclick="paiSuggest('{{ $s }}')"
                class="rounded-full border px-3 py-1 text-xs transition-colors"
                style="border-color:#bfdbfe;color:#1d4ed8;"
                onmouseover="this.style.background='#eff6ff'"
                onmouseout="this.style.background=''"
        >{{ $s }}</button>
        @endforeach
    </div>

    {{-- Input area --}}
    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex gap-3 items-end">
        <textarea id="pai-input"
                  rows="1"
                  placeholder="Hỏi về khách sạn của bạn: doanh thu, đặt phòng, đánh giá, giá phòng..."
                  class="flex-1 resize-none rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 transition"
                  style="max-height:120px; --tw-ring-color:#2563eb;"
                  onkeydown="paiHandleKey(event)"></textarea>
        <button onclick="paiSend()"
                id="pai-send-btn"
                class="shrink-0 inline-flex items-center justify-center rounded-xl text-white px-4 py-2.5 text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                style="background:#2563eb;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
            </svg>
            <span class="ml-1.5">Gửi</span>
        </button>
    </div>
</div>

<style>
#pai-messages { scroll-behavior: smooth; }
.pai-bubble { line-height: 1.65; white-space: pre-wrap; word-break: break-word; }
.pai-bubble.user { background:#2563eb; color:white; border-radius:1rem 1rem 0 1rem; }
.pai-bubble.loading { background:#f3f4f6; }
.pai-dots span {
    display:inline-block; width:6px; height:6px; border-radius:50%;
    background:#9ca3af; animation:dotBounce .8s infinite;
}
.pai-dots span:nth-child(2) { animation-delay:.15s; }
.pai-dots span:nth-child(3) { animation-delay:.3s; }
@keyframes dotBounce {
    0%,80%,100% { transform:translateY(0); }
    40%          { transform:translateY(-6px); }
}
.dark .pai-bubble.loading { background:#1f2937; }
</style>

<script>
const paiHistory  = [];
const paiCsrf     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const paiChatUrl  = '{{ route("partner.ai.chat") }}';
const paiKpiUrl   = '{{ route("partner.ai.kpi") }}';

function paiAutoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

document.getElementById('pai-input').addEventListener('input', function () { paiAutoResize(this); });

function paiHandleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); paiSend(); }
}

function paiSuggest(text) {
    const el = document.getElementById('pai-input');
    el.value = text;
    paiAutoResize(el);
    paiSend();
}

function paiEsc(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function paiAppend(role, content) {
    const box  = document.getElementById('pai-messages');
    const wrap = document.createElement('div');
    wrap.className = 'flex gap-3' + (role === 'user' ? ' justify-end' : '');

    if (role === 'assistant') {
        wrap.innerHTML = `
            <div class="pai-avatar shrink-0 flex h-8 w-8 items-center justify-center rounded-full text-white text-sm font-bold" style="background:#2563eb;">AI</div>
            <div class="pai-bubble rounded-2xl rounded-tl-none bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm text-gray-800 dark:text-gray-100 max-w-2xl">${paiEsc(content)}</div>
        `;
    } else {
        wrap.innerHTML = `<div class="pai-bubble user px-4 py-2 text-sm max-w-xl">${paiEsc(content)}</div>`;
    }
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
    return wrap;
}

function paiAppendLoading() {
    const box  = document.getElementById('pai-messages');
    const wrap = document.createElement('div');
    wrap.className = 'flex gap-3';
    wrap.id = 'pai-loading';
    wrap.innerHTML = `
        <div class="pai-avatar shrink-0 flex h-8 w-8 items-center justify-center rounded-full text-white text-sm font-bold" style="background:#2563eb;">AI</div>
        <div class="pai-bubble loading rounded-2xl rounded-tl-none px-4 py-3">
            <div class="pai-dots"><span></span><span></span><span></span></div>
        </div>
    `;
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

async function paiSend() {
    const input = document.getElementById('pai-input');
    const btn   = document.getElementById('pai-send-btn');
    const msg   = input.value.trim();
    if (!msg || btn.disabled) return;

    input.value = '';
    paiAutoResize(input);
    btn.disabled = true;

    paiAppend('user', msg);
    paiAppendLoading();

    try {
        const res = await fetch(paiChatUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': paiCsrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: msg, history: paiHistory }),
        });

        document.getElementById('pai-loading')?.remove();
        const data  = await res.json();
        const reply = data.reply ?? 'Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại.';

        paiHistory.push({ role: 'user',      content: msg });
        paiHistory.push({ role: 'assistant', content: reply });
        if (paiHistory.length > 20) paiHistory.splice(0, 2);

        paiAppend('assistant', reply);
    } catch (err) {
        document.getElementById('pai-loading')?.remove();
        paiAppend('assistant', 'Lỗi kết nối. Vui lòng thử lại.');
    } finally {
        btn.disabled = false;
        input.focus();
    }
}

async function paiLoadKpi() {
    try {
        const res = await fetch(paiKpiUrl, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': paiCsrf }
        });
        if (!res.ok) return;
        const d = await res.json();
        document.getElementById('pkpi-bookings-today').textContent = d.bookings_today ?? '—';
        document.getElementById('pkpi-revenue-month').textContent  = d.revenue_month_fmt ?? '—';
        document.getElementById('pkpi-pending').textContent        = d.pending ?? '—';
        document.getElementById('pkpi-rating').textContent         = (d.avg_rating ?? '—') + ' / 5';
    } catch {}
}

paiLoadKpi();
</script>

</x-filament-panels::page>
