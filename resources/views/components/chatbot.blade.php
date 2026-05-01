{{-- ===== CHATBOT WIDGET ===== --}}
<div id="chatbot-container" class="chatbot-container">
    <div class="chatbot-box">
        {{-- Header --}}
        <div class="chatbot-header">
            <div class="chatbot-header-left">
                <div class="chatbot-avatar">
                    <img src="{{ asset('assets/images/chatbot.png') }}" alt="StayGo AI">
                </div>
                <div class="chatbot-header-info">
                    <div class="chatbot-agent-name">StayGo AI</div>
                    <div class="chatbot-status"><span class="chatbot-online-dot"></span> Online</div>
                </div>
            </div>
            <button onclick="toggleChatbot()" class="chatbot-close-btn" aria-label="Đóng">✕</button>
        </div>

        {{-- Messages --}}
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chatbot-bubble chatbot-bubble--bot">
                Xin chào! Tôi là trợ lý AI của StayGo. Tôi có thể giúp gì cho bạn? 😊
            </div>
        </div>

        {{-- Quick questions --}}
        <div class="chatbot-quick-questions">
            <button class="chatbot-quick-btn" onclick="sendQuickChat('Còn phòng trống không?')">Còn phòng trống?</button>
            <button class="chatbot-quick-btn" onclick="sendQuickChat('Giá phòng bao nhiêu?')">Giá phòng?</button>
            <button class="chatbot-quick-btn" onclick="sendQuickChat('Chính sách hủy phòng?')">Chính sách hủy?</button>
            <button class="chatbot-quick-btn" onclick="sendQuickChat('Cần hỗ trợ đặt phòng')">Hỗ trợ đặt phòng</button>
        </div>

        {{-- Input --}}
        <div class="chatbot-input-wrap">
            <input id="chatbot-input" type="text" placeholder="Nhập câu hỏi..."
                class="chatbot-input"
                onkeydown="if(event.key==='Enter') sendChat()">
            <button onclick="sendChat()" class="chatbot-send-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- ===== QR PROMO POPUP ===== --}}
<div class="qrp-wrap" id="qrpWrap">
    <div class="qrp-box" id="qrpBox">
        <button class="qrp-close" onclick="closeQrPromo()" aria-label="Đóng">✕</button>
        <div class="qrp-head">
            <div class="qrp-title">Giảm 10% cho đơn đặt phòng<br>đầu tiên trên StayGo!</div>
            <div class="qrp-sub">Chỉ cần quét mã QR để được giảm giá ngay</div>
        </div>
        <div class="qrp-phone-frame">
            <div class="qrp-notch"></div>
            <div class="qrp-screen">
                <div class="qrp-brand">
                    <img src="{{ asset('assets/images/StayGo.png') }}" alt="StayGo" style="height:22px;object-fit:contain;">
                </div>
                <div id="qrpCanvas"></div>
            </div>
        </div>
        <div class="qrp-footer">
            <span class="qrp-tag">🔐 Chỉ dành cho tài khoản mới</span>
            <a href="{{ route('promo.new_user') }}" class="qrp-btn-link">Nhấn để nhận ngay →</a>
        </div>
    </div>
    <button class="qrp-toggle-btn" id="qrpToggleBtn" onclick="toggleQrPromo()" title="Ưu đãi 10% cho khách mới">
        <span class="qrp-pct">%</span>
        <span class="qrp-badge-dot"></span>
    </button>
</div>

{{-- Toggle button --}}
<button onclick="toggleChatbot()" id="chatbot-toggle-btn" class="chatbot-toggle-btn" aria-label="Chat với chúng tôi">
    <img id="chatbot-icon-open" src="{{ asset('assets/images/chatbot.png') }}" alt="Chat" style="width:38px;height:38px;object-fit:contain;">
    <svg id="chatbot-icon-close" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
    </svg>
</button>

<script>
function toggleChatbot() {
    const container = document.getElementById('chatbot-container');
    const iconOpen  = document.getElementById('chatbot-icon-open');
    const iconClose = document.getElementById('chatbot-icon-close');
    const isOpen    = container.classList.toggle('chatbot-container--open');
    iconOpen.style.display  = isOpen ? 'none'  : 'block';
    iconClose.style.display = isOpen ? 'block' : 'none';
}

async function sendChat() {
    const input = document.getElementById('chatbot-input');
    const msg   = input.value.trim();
    if (!msg) return;
    appendMessage('user', msg);
    input.value = '';
    await fetchBotReply(msg);
}

async function sendQuickChat(msg) {
    appendMessage('user', msg);
    await fetchBotReply(msg);
}

async function fetchBotReply(msg) {
    const typingId = showTyping();
    try {
        const res  = await fetch('{{ route("chatbot") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ message: msg })
        });
        const data = await res.json();
        removeTyping(typingId);
        appendMessage('bot', data.reply ?? 'Xin lỗi, có lỗi xảy ra.');
    } catch {
        removeTyping(typingId);
        appendMessage('bot', 'Không thể kết nối. Vui lòng thử lại.');
    }
}

function showTyping() {
    const box = document.getElementById('chatbot-messages');
    const id  = 'typing-' + Date.now();
    const div = document.createElement('div');
    div.id = id;
    div.className = 'chatbot-bubble chatbot-bubble--bot chatbot-typing';
    div.innerHTML = '<span></span><span></span><span></span>';
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
    return id;
}

function removeTyping(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

function appendMessage(role, text) {
    const box = document.getElementById('chatbot-messages');
    const div = document.createElement('div');
    div.className = 'chatbot-bubble chatbot-bubble--' + role;
    if (role === 'bot') {
        div.innerHTML = formatBotText(text);
    } else {
        div.textContent = text;
    }
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function formatBotText(text) {
    // Escape HTML trước
    const escaped = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const lines = escaped.split('\n');
    let html = '';
    let inList = false;

    for (let i = 0; i < lines.length; i++) {
        let line = lines[i].trim();
        if (!line) {
            if (inList) { html += '</ul>'; inList = false; }
            continue;
        }

        // Bullet point: dòng bắt đầu bằng -, •, *, +
        if (/^[-•*+]\s+/.test(line)) {
            if (!inList) { html += '<ul class="cb-list">'; inList = true; }
            line = line.replace(/^[-•*+]\s+/, '');
            line = applyInline(line);
            html += `<li>${line}</li>`;
        }
        // Số thứ tự: 1. 2. 3.
        else if (/^\d+\.\s+/.test(line)) {
            if (!inList) { html += '<ol class="cb-list">'; inList = true; }
            line = line.replace(/^\d+\.\s+/, '');
            line = applyInline(line);
            html += `<li>${line}</li>`;
        }
        else {
            if (inList) { html += '</ul>'; inList = false; }
            line = applyInline(line);
            html += `<p class="cb-p">${line}</p>`;
        }
    }
    if (inList) html += '</ul>';
    return html;
}

function applyInline(text) {
    // **bold**
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    // *italic*
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    return text;
}
</script>

<script>
// ===== QR Promo Widget =====
(function () {
    const PROMO_CLOSED_KEY = 'qrp_closed_at';

    function toggleQrPromo() {
        document.getElementById('qrpWrap').classList.toggle('qrp-open');
    }
    function closeQrPromo() {
        document.getElementById('qrpWrap').classList.remove('qrp-open');
        sessionStorage.setItem(PROMO_CLOSED_KEY, Date.now());
    }

    // Expose to inline onclick
    window.toggleQrPromo = toggleQrPromo;
    window.closeQrPromo  = closeQrPromo;

    // Preload thư viện QR ngay khi trang load (không block render)
    function loadQRLib(cb) {
        if (typeof QRCode !== 'undefined') { cb && cb(); return; }
        const s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
        s.onload = function () { cb && cb(); };
        document.head.appendChild(s);
    }

    // Auto-open after 4s (only once per session, if not manually closed)
    if (!sessionStorage.getItem(PROMO_CLOSED_KEY)) {
        setTimeout(function () {
            const wrap = document.getElementById('qrpWrap');
            if (wrap && !wrap.classList.contains('qrp-open')) {
                loadQRLib(function () {
                    wrap.classList.add('qrp-open');
                    generateQR();
                });
            }
        }, 4000);
    } else {
        // Dù không tự mở, vẫn preload thư viện để click nhanh hơn
        loadQRLib();
    }

    // Generate QR when opened manually
    document.getElementById('qrpToggleBtn').addEventListener('click', function () {
        loadQRLib(function () { setTimeout(generateQR, 50); });
    });

    let qrGenerated = false;
    function generateQR() {
        if (qrGenerated) return;
        if (typeof QRCode === 'undefined') return; // chờ script load xong
        qrGenerated = true;
        const container = document.getElementById('qrpCanvas');
        container.innerHTML = ''; // xóa cũ nếu có
        new QRCode(container, {
            text:         '{{ route("promo.new_user") }}',
            width:        130,
            height:       130,
            colorDark:    '#1a202c',
            colorLight:   '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

})();
</script>
