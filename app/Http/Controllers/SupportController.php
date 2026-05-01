<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SupportController extends Controller
{
    public function create()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        if ($request->filled('_hp_name') || $request->filled('_hp_email')) {
            return redirect()->back()->with('success', 'Yêu cầu đã được gửi.');
        }

        $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone'     => ['required', 'string', 'max:20'],
            'email'     => ['nullable', 'email'],
            'subject'   => ['nullable', 'string', 'max:255'],
            'note'      => ['nullable', 'string'],
        ]);

        SupportRequest::create([
            'user_id'   => Auth::id(),
            'full_name' => $request->full_name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'subject'   => $request->subject,
            'note'      => $request->note,
            'status'    => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Yêu cầu đã được gửi! Chúng tôi sẽ liên hệ trong vòng 15 phút.']);
        }

        return back()->with('success', 'Yêu cầu hỗ trợ đã được gửi! Chúng tôi sẽ liên hệ trong vòng 24 giờ.');
    }

    public function show(SupportRequest $supportRequest)
    {
        // Chỉ chủ ticket hoặc admin mới xem được
        if (Auth::id() !== $supportRequest->user_id) {
            abort(403);
        }

        $supportRequest->load('replies.user');

        return view('pages.support-thread', compact('supportRequest'));
    }
}
