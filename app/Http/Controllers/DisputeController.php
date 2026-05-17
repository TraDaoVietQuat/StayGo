<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    public function create(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $booking   = $bookingId ? Booking::find($bookingId) : null;

        // Only allow the booking owner or guest (email match) to open a dispute
        if ($booking && Auth::check() && $booking->user_id && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('disputes.create', [
            'booking'    => $booking,
            'typeLabels' => Dispute::typeLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id'  => 'nullable|exists:bookings,id',
            'type'        => 'required|in:no_show,overbooking,quality,hidden_fees,slow_refund,misconduct',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|min:20|max:5000',
            'timeline'    => 'nullable|string|max:3000',
            'evidence'    => 'nullable|array|max:6',
            'evidence.*.type'        => 'required_with:evidence|in:photo,video,email,chat,receipt,other',
            'evidence.*.description' => 'required_with:evidence|string|max:500',
        ], [
            'type.required'        => 'Vui lòng chọn loại tranh chấp.',
            'title.required'       => 'Vui lòng nhập tiêu đề khiếu nại.',
            'description.required' => 'Vui lòng mô tả chi tiết sự việc.',
            'description.min'      => 'Mô tả cần ít nhất 20 ký tự.',
        ]);

        $booking  = $data['booking_id'] ? Booking::with('room.hotel')->find($data['booking_id']) : null;
        $priority = in_array($data['type'], ['overbooking', 'misconduct']) ? 'urgent' : 'normal';

        Dispute::create([
            'booking_id'  => $data['booking_id'] ?? null,
            'user_id'     => Auth::id() ?? null,
            'hotel_id'    => $booking?->room?->hotel?->id,
            'type'        => $data['type'],
            'priority'    => $priority,
            'status'      => 'open',
            'title'       => $data['title'],
            'description' => $data['description'],
            'timeline'    => $data['timeline'] ?? null,
            'evidence'    => !empty($data['evidence']) ? $data['evidence'] : null,
            'deadline_at' => Carbon::now()->addHours($priority === 'urgent' ? 4 : 24),
        ]);

        return redirect()->route('dispute.success');
    }

    public function success()
    {
        return view('disputes.success');
    }
}
