@extends('layouts.app')
@section('title', 'Đổi mật khẩu')

@section('content')
<div class="up-wrap">
    <div class="up-layout">
        @include('components.user-sidebar')

        <div class="up-main">
            @if(session('success'))
            <div class="up-alert up-alert-success">✅ {{ session('success') }}</div>
            @endif

            <div class="up-card">
                <div class="up-card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Đổi mật khẩu
                </div>
                <form method="POST" action="{{ route('profile.change-password') }}" class="up-form">
                    @csrf @method('PUT')
                    <div class="up-field">
                        <label>Mật khẩu hiện tại *</label>
                        <input type="password" name="current_password" required>
                        @error('current_password')<div class="up-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="up-grid-2">
                        <div class="up-field">
                            <label>Mật khẩu mới *</label>
                            <input type="password" name="password" required>
                            @error('password')<div class="up-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="up-field">
                            <label>Xác nhận mật khẩu *</label>
                            <input type="password" name="password_confirmation" required>
                        </div>
                    </div>
                    <button type="submit" class="up-btn up-btn-green">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
