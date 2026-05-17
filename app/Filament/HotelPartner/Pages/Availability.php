<?php

namespace App\Filament\HotelPartner\Pages;

use App\Models\Room;
use App\Models\RoomUnavailableDate;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Availability extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Tình trạng phòng';
    protected static ?string $title           = 'Quản lý tình trạng phòng';
    protected static ?string $navigationGroup = 'Khách sạn';
    protected static ?int    $navigationSort  = 4;

    protected static string $view = 'filament.hotel-partner.pages.availability';

    public ?int $selectedRoomId = null;
    public string $viewMonth;

    public function mount(): void
    {
        $this->viewMonth = now()->format('Y-m');
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        if ($hotel) {
            $this->selectedRoomId = $hotel->rooms()->value('id');
        }
    }

    public function getRooms(): \Illuminate\Support\Collection
    {
        $hotel = auth('hotel_partner')->user()?->managedHotel;
        return $hotel ? Room::where('hotel_id', $hotel->id)->get(['id', 'room_name']) : collect();
    }

    public function getCalendarData(): array
    {
        if (!$this->selectedRoomId) return [];

        $month   = \Carbon\Carbon::createFromFormat('Y-m', $this->viewMonth);
        $start   = $month->copy()->startOfMonth();
        $end     = $month->copy()->endOfMonth();
        $days    = [];

        $blocked = RoomUnavailableDate::where('room_id', $this->selectedRoomId)
            ->whereBetween('date', [$start, $end])
            ->get()->keyBy(fn($d) => $d->date->format('Y-m-d'));

        $bookedDates = \App\Models\Booking::where('room_id', $this->selectedRoomId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<=', $end)
            ->where('check_out', '>', $start)
            ->get();

        $bookedSet = [];
        foreach ($bookedDates as $b) {
            $cur = \Carbon\Carbon::parse($b->check_in);
            $out = \Carbon\Carbon::parse($b->check_out);
            while ($cur < $out && $cur <= $end) {
                $bookedSet[$cur->format('Y-m-d')] = true;
                $cur->addDay();
            }
        }

        $cur = $start->copy();
        while ($cur <= $end) {
            $key = $cur->format('Y-m-d');
            $days[] = [
                'date'      => $key,
                'day'       => $cur->day,
                'weekday'   => $cur->dayOfWeekIso,
                'blocked'   => isset($blocked[$key]),
                'booked'    => isset($bookedSet[$key]),
                'reason'    => $blocked[$key]?->reason ?? '',
            ];
            $cur->addDay();
        }

        return $days;
    }

    public function blockDate(string $date): void
    {
        if (!$this->selectedRoomId) return;

        $exists = RoomUnavailableDate::where('room_id', $this->selectedRoomId)
            ->where('date', $date)->exists();

        if ($exists) {
            RoomUnavailableDate::where('room_id', $this->selectedRoomId)->where('date', $date)->delete();
            Notification::make()->title('Đã mở khóa ngày ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))->success()->send();
        } else {
            RoomUnavailableDate::create([
                'room_id' => $this->selectedRoomId,
                'date'    => $date,
                'reason'  => 'Bảo trì',
            ]);
            Notification::make()->title('Đã khóa ngày ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))->warning()->send();
        }
    }

    public function prevMonth(): void
    {
        $this->viewMonth = \Carbon\Carbon::createFromFormat('Y-m', $this->viewMonth)->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->viewMonth = \Carbon\Carbon::createFromFormat('Y-m', $this->viewMonth)->addMonth()->format('Y-m');
    }
}
