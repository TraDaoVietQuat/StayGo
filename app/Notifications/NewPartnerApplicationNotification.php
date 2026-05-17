<?php

namespace App\Notifications;

use App\Models\HotelPartnerProfile;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPartnerApplicationNotification extends Notification
{
    use Queueable;

    public function __construct(private HotelPartnerProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('🤝 Đơn đăng ký đối tác mới')
            ->body(($this->profile->contact_name ?? $this->profile->user?->full_name ?? '?')
                . ' — ' . ($this->profile->business_name ?? 'Chưa có tên doanh nghiệp'))
            ->icon('heroicon-o-building-storefront')
            ->iconColor('warning')
            ->actions([
                Action::make('review')
                    ->label('Xét duyệt hồ sơ')
                    ->url(url('/admin/hotel-partners/' . $this->profile->id . '/edit'))
                    ->button(),
            ])
            ->getDatabaseMessage();
    }
}
