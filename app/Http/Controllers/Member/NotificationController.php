<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CommitteeNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('member.notifications', compact('notifications'));
    }

    public function markAsRead(CommitteeNotification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
        }

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
