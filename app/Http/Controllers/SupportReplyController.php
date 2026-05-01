<?php

namespace App\Http\Controllers;

use App\Models\SupportReply;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportReplyController extends Controller
{
    public function store(Request $request, SupportRequest $supportRequest)
    {
        // Chỉ chủ ticket mới được reply (user side)
        abort_if($supportRequest->user_id !== Auth::id(), 403);
        abort_if($supportRequest->status === 'closed', 400, 'Ticket này đã đóng.');

        $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'message.required' => 'Vui lòng nhập nội dung trả lời.',
            'message.min'      => 'Nội dung quá ngắn (tối thiểu 5 ký tự).',
        ]);

        SupportReply::create([
            'support_request_id' => $supportRequest->id,
            'user_id'            => Auth::id(),
            'message'            => $request->message,
            'is_admin'           => false,
        ]);

        // Cập nhật trạng thái sang pending nếu đang resolved
        if ($supportRequest->status === 'resolved') {
            $supportRequest->update(['status' => 'pending']);
        }

        return back()->with('success', 'Đã gửi trả lời.');
    }
}
