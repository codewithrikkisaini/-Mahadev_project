<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('login')->with('error', 'Member profile not found.');
        }

        $member->load(['payments.verifier', 'user']);

        $summary = $member->payment_summary;
        $currentMonthStatus = $member->current_month_status;
        $monthlyAmount = $member->monthly_amount;

        // Recent 5 payments
        $recentPayments = $member->payments()->latest('created_at')->take(5)->get();

        // Recent published announcements
        $announcements = Announcement::published()->take(4)->get();

        // Notifications
        $notifications = $user->notifications()->take(5)->get();

        $currentMonthName = Carbon::now()->format('F Y');
        $nextDueDate = Carbon::now()->endOfMonth()->format('d M Y');

        $committeeName = Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee');
        $templeName = Setting::get('temple_name', 'Shri Mahadev Mandir');

        return view('member.dashboard', compact(
            'member',
            'summary',
            'currentMonthStatus',
            'monthlyAmount',
            'recentPayments',
            'announcements',
            'notifications',
            'currentMonthName',
            'nextDueDate',
            'committeeName',
            'templeName'
        ));
    }
}
