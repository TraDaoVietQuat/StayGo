<?php

namespace App\Notifications;

use App\Models\Dispute;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDisputeNotification extends Notification
{
    use Queueable;

    public function __construct(private Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isUrgent = $this->dispute->priority === 'urgent';
        $typeLabels = Dispute::typeLabels();

        return FilamentNotification::make()
            ->title($isUrgent ? '🔴 Khiếu nại khẩn cấp mới!' : '⚖️ Khiếu nại mới tiếp nhận')
            ->body(($typeLabels[$this->dispute->type] ?? $this->dispute->type)
                . ' | ' . $this->dispute->title)
            ->icon('heroicon-o-scale')
            ->iconColor($isUrgent ? 'danger' : 'warning')
            ->actions([
                Action::make('view')
                    ->label('Xem & xử lý')
                    ->url(url('/admin/disputes/' . $this->dispute->id . '/edit'))
                    ->button(),
            ])
            ->getDatabaseMessage();
    }
}
