<x-filament-panels::page>
    {{-- KPI Summary --}}
    <div class="grid grid-cols-2 gap-3 mb-5 sm:grid-cols-4" id="ai-kpi-row">
        <div class="ai-kpi-card rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Đặt phòng hôm nay</div>
            <div class="mt-1 text-2xl font-bold text-rose-600" id="kpi-bookings-today">—</div>
        </div>
        <div class="ai-kpi-card rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Doanh thu tháng này</div>
            <div class="mt-1 text-2xl font-bold text-emerald-600" id="kpi-revenue-month">—</div>
        </div>
        <div class="ai-kpi-card rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Đơn chờ xử lý</div>
            <div class="mt-1 text-2xl font-bold text-amber-500" id="kpi-pending">—</div>
        </div>
        <div class="ai-kpi-card rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Yêu cầu hỗ trợ</div>
            <div class="mt-1 text-2xl font-bold text-blue-600" id="kpi-support">—</div>
        </div>
    </div>

    {{-- Chat Window --}}
    <div class="flex flex-col rounded-2xl border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-900"
         style="height: calc(100vh - 280px); min-height: 420px;">

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4" id="ai-messages">
            <div class="flex gap-3">
                <div class="ai-avatar shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white text-sm font-bold">AI</div>
                <div class="ai-bubble rounded-2xl rounded-tl-none bg-gray-100 px-4 py-2 text-sm text-gray-800 dark:bg-gray-800 dark:text-gray-100 max-w-2xl">
                    Xin chào! Tôi là trợ lý AI của StayGo Admin. Tôi có thể giúp bạn phân tích dữ liệu, tra cứu đơn đặt phòng, báo cáo doanh thu và nhiều hơn nữa. Hỏi tôi bất cứ điều gì về nghiệp vụ OTA nhé!
                </div>
            </div>
        </div>

        {{-- Quick suggestions --}}
        <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2 flex gap-2 flex-wrap" id="ai-suggestions">
            @foreach([
                'Tổng kết doanh thu hôm nay',
                'Có bao nhiêu đơn đang chờ xử lý?',
                'Khách sạn nào đang hoạt động tốt nhất?',
                'Yêu cầu hỗ trợ chưa giải quyết',
            ] as $s)
            <button onclick="aiSuggest('{{ $s }}')"
                    class="rounded-full border border-rose-200 px-3 py-1 text-xs text-rose-700 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-300 dark:hover:bg-rose-950 transition-colors">
                {{ $s }}
            </button>
            @endforeach
        </div>

        {{-- Input area --}}
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex gap-3 items-end">
            <textarea id="ai-input"
                      rows="1"
                      placeholder="Hỏi trợ lý AI về nghiệp vụ admin..."
                      class="flex-1 resize-none rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500 transition"
                      style="max-height: 120px;"
                      onkeydown="aiHandleKey(event)"></textarea>
            <button onclick="aiSend()"
                    id="ai-send-btn"
                    class="shrink-0 inline-flex items-center justify-center rounded-xl bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                </svg>
                <span class="ml-1.5">Gửi</span>
            </button>
        </div>
    </div>

    <style>
        #ai-messages { scroll-behavior: smooth; }
        .ai-bubble { line-height: 1.6; white-space: pre-wrap; word-break: break-word; }
        .ai-bubble.user { background: #f43f5e; color: white; border-radius: 1rem 1rem 0 1rem; }
        .ai-bubble.loading { background: #f3f4f6; }
        .typing-dots span {
            display: inline-block; width: 6px; height: 6px; border-radius: 50%;
            background: #9ca3af; animation: dotBounce .8s infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: .15s; }
        .typing-dots span:nth-child(3) { animation-delay: .3s; }
        @keyframes dotBounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-6px); }
        }
        .dark .ai-bubble.loading { background: #1f2937; }
    </style>

    <script>
    const aiHistory = [];
    const aiCsrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const aiChatUrl = '{{ route("admin.ai.chat") }}';
    const aiKpiUrl  = '{{ route("admin.ai.kpi") }}';

    function aiAutoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    document.getElementById('ai-input').addEventListener('input', function () {
        aiAutoResize(this);
    });

    function aiHandleKey(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); aiSend(); }
    }

    function aiSuggest(text) {
        document.getElementById('ai-input').value = text;
        aiAutoResize(document.getElementById('ai-input'));
        aiSend();
    }

    function aiAppend(role, content) {
        const box = document.getElementById('ai-messages');
        const wrap = document.createElement('div');
        wrap.className = 'flex gap-3' + (role === 'user' ? ' justify-end' : '');

        if (role === 'assistant') {
            wrap.innerHTML = `
                <div class="ai-avatar shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white text-sm font-bold">AI</div>
                <div class="ai-bubble rounded-2xl rounded-tl-none bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm text-gray-800 dark:text-gray-100 max-w-2xl">${escHtml(content)}</div>
            `;
        } else {
            wrap.innerHTML = `
                <div class="ai-bubble user px-4 py-2 text-sm max-w-xl">${escHtml(content)}</div>
            `;
        }
        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
        return wrap;
    }

    function aiAppendLoading() {
        const box = document.getElementById('ai-messages');
        const wrap = document.createElement('div');
        wrap.className = 'flex gap-3';
        wrap.id = 'ai-loading';
        wrap.innerHTML = `
            <div class="ai-avatar shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white text-sm font-bold">AI</div>
            <div class="ai-bubble loading rounded-2xl rounded-tl-none px-4 py-3">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    async function aiSend() {
        const input = document.getElementById('ai-input');
        const btn   = document.getElementById('ai-send-btn');
        const msg   = input.value.trim();
        if (!msg || btn.disabled) return;

        input.value = '';
        aiAutoResize(input);
        btn.disabled = true;

        aiAppend('user', msg);
        aiAppendLoading();

        try {
            const res = await fetch(aiChatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': aiCsrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: msg, history: aiHistory }),
            });

            document.getElementById('ai-loading')?.remove();

            const data = await res.json();
            const reply = data.reply ?? 'Xin lỗi, đã xảy ra lỗi.';

            aiHistory.push({ role: 'user', content: msg });
            aiHistory.push({ role: 'assistant', content: reply });
            if (aiHistory.length > 20) aiHistory.splice(0, 2);

            aiAppend('assistant', reply);
        } catch (err) {
            document.getElementById('ai-loading')?.remove();
            aiAppend('assistant', 'Lỗi kết nối. Vui lòng thử lại.');
        } finally {
            btn.disabled = false;
            input.focus();
        }
    }

    // Load KPI numbers
    async function loadKpi() {
        try {
            const res = await fetch(aiKpiUrl, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': aiCsrf }
            });
            if (!res.ok) return;
            const d = await res.json();
            document.getElementById('kpi-bookings-today').textContent = d.bookings_today ?? '—';
            document.getElementById('kpi-revenue-month').textContent  = d.revenue_month_fmt ?? '—';
            document.getElementById('kpi-pending').textContent        = d.pending ?? '—';
            document.getElementById('kpi-support').textContent        = d.open_tickets ?? '—';
        } catch {}
    }

    loadKpi();
    </script>
</x-filament-panels::page>
