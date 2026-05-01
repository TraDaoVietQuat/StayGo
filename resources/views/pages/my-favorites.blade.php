@extends('layouts.app')
@section('title', 'Khách sạn yêu thích')

@section('content')
<div class="up-wrap">
    <div class="up-layout">
        @include('components.user-sidebar')

        <div class="up-main">
            <div class="up-card">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e91e8c" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Khách sạn yêu thích
                </div>

                @if($hotels->isEmpty())
                <div style="text-align:center;padding:60px 20px;">
                    <div style="font-size:52px;margin-bottom:16px;">🏨</div>
                    <h3 style="font-size:18px;font-weight:700;margin:0 0 8px;color:#333;">Chưa có khách sạn yêu thích</h3>
                    <p style="color:#888;margin:0 0 20px;font-size:14px;">Nhấn vào biểu tượng ❤️ trên thẻ khách sạn để lưu lại.</p>
                    <a href="{{ route('hotels.index') }}" class="up-btn up-btn-primary" style="display:inline-block;text-decoration:none;">
                        Khám phá khách sạn
                    </a>
                </div>
                @else
                <div class="fav-hotels-grid">
                    @foreach($hotels as $hotel)
                        @include('components.hotel-card', ['hotel' => $hotel, 'favHotelIds' => $hotels->pluck('id')->toArray()])
                    @endforeach
                </div>

                <div style="margin-top:24px;">
                    {{ $hotels->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.fav-hotels-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(min(260px,100%),1fr)); gap:16px; padding:4px 0; }
</style>
@endpush
