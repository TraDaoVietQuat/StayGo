@extends('layouts.app')

@section('title', 'Gửi khiếu nại — StayGo')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="mx-auto max-w-2xl">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-orange-100">
                <svg class="h-7 w-7 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Gửi khiếu nại / Tranh chấp</h1>
            <p class="mt-1 text-sm text-gray-500">StayGo cam kết xử lý trong vòng <strong>24 giờ</strong> (4 giờ với trường hợp khẩn cấp)</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">
                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('dispute.store') }}" id="disputeForm"
              class="space-y-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @csrf

            {{-- Booking reference --}}
            @if ($booking)
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm">
                    <p class="font-semibold text-blue-800 mb-1">Đặt phòng liên quan</p>
                    <p class="text-blue-700">
                        Mã: <strong>{{ $booking->order_code }}</strong> &nbsp;·&nbsp;
                        {{ $booking->full_name }} &nbsp;·&nbsp;
                        {{ $booking->check_in?->format('d/m/Y') }} → {{ $booking->check_out?->format('d/m/Y') }}
                    </p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã đặt phòng (nếu có)</label>
                    <input type="text" name="booking_code_hint" value="{{ old('booking_code_hint') }}"
                           placeholder="VD: ORD-2024-00123"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Nếu không nhớ mã, vui lòng để trống — chúng tôi sẽ tìm theo email.</p>
                </div>
            @endif

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại khiếu nại *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($typeLabels as $value => $label)
                        <label class="flex items-start gap-3 cursor-pointer rounded-xl border p-3 transition
                                      {{ old('type') === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                            <input type="radio" name="type" value="{{ $value }}" required
                                   class="mt-0.5 accent-blue-600" {{ old('type') === $value ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề khiếu nại *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="VD: Bị tính phí hủy dù đã hủy trước 48h"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết sự việc *</label>
                <textarea name="description" required rows="5"
                          placeholder="Mô tả đầy đủ sự việc: bạn đã đặt gì, điều gì xảy ra, thiệt hại của bạn là gì..."
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Timeline --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Timeline sự việc (theo thứ tự thời gian)</label>
                <textarea name="timeline" rows="4"
                          placeholder="VD:&#10;- 10/05: Đặt phòng, nhận email xác nhận&#10;- 15/05 10:00: Đến check-in, nhân viên báo hết phòng&#10;- 15/05 10:30: Gọi hotline không được giải quyết"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('timeline') }}</textarea>
            </div>

            {{-- Evidence --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bằng chứng đính kèm</label>
                <div id="evidenceList" class="space-y-3">
                    {{-- JS will add items here --}}
                </div>
                <button type="button" id="addEvidence"
                        class="mt-3 flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm bằng chứng (tối đa 6)
                </button>
                <p class="mt-1 text-xs text-gray-400">Mô tả bằng chứng bạn có: ảnh, video, email, lịch sử chat, biên lai...</p>
            </div>

            {{-- Notice --}}
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                <p class="font-semibold mb-1">📋 Lưu ý quan trọng</p>
                <ul class="list-disc list-inside space-y-1 text-amber-700">
                    <li>Đội ngũ StayGo sẽ liên hệ bạn qua email trong vòng 24 giờ</li>
                    <li>Trường hợp khẩn cấp (overbooking, hành vi không chuyên nghiệp): xử lý trong 4 giờ</li>
                    <li>Mọi quyết định hoàn tiền trên 5.000.000 VND cần xác nhận cấp trên</li>
                    <li>Vui lòng cung cấp bằng chứng đầy đủ để được xử lý nhanh nhất</li>
                </ul>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 text-sm transition-colors">
                Gửi khiếu nại
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Cần hỗ trợ ngay? &nbsp;
            <a href="{{ route('support.create') }}" class="text-blue-600 underline">Liên hệ hotline</a>
            &nbsp;hoặc chat với nhân viên hỗ trợ.
        </p>
    </div>
</div>

<script>
const evidenceTypes = {
    photo: '📷 Ảnh chụp',
    video: '🎥 Video',
    email: '📧 Email',
    chat:  '💬 Lịch sử chat',
    receipt: '🧾 Biên lai / hoá đơn',
    other: '📎 Khác',
};
let count = 0;
const MAX = 6;

document.getElementById('addEvidence').addEventListener('click', function () {
    if (count >= MAX) return;
    const idx = count++;
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-start';
    div.innerHTML = `
        <select name="evidence[${idx}][type]" required
                class="rounded-lg border border-gray-300 px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-40 shrink-0">
            ${Object.entries(evidenceTypes).map(([v,l]) => `<option value="${v}">${l}</option>`).join('')}
        </select>
        <input type="text" name="evidence[${idx}][description]" required
               placeholder="Mô tả nội dung bằng chứng..."
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="this.closest('div').remove(); count--;"
                class="shrink-0 text-gray-400 hover:text-red-500 mt-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    document.getElementById('evidenceList').appendChild(div);
    if (count >= MAX) document.getElementById('addEvidence').classList.add('opacity-40', 'pointer-events-none');
});

// Highlight selected radio
document.querySelectorAll('input[name="type"]').forEach(r => {
    r.addEventListener('change', () => {
        document.querySelectorAll('input[name="type"]').forEach(x => {
            x.closest('label').classList.remove('border-blue-500', 'bg-blue-50');
            x.closest('label').classList.add('border-gray-200');
        });
        r.closest('label').classList.add('border-blue-500', 'bg-blue-50');
        r.closest('label').classList.remove('border-gray-200');
    });
});
</script>
@endsection
