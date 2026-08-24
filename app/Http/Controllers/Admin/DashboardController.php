<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CommitteeNotification;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $year = (int) $request->get('year', Carbon::now()->year);
        $month = (int) $request->get('month', Carbon::now()->month);

        $stats = $this->paymentService->getAdminDashboardStats($year, $month);

        // Pending payments needing verification
        $pendingPayments = Payment::with(['member.user'])
            ->where('status', 'pending')
            ->latest()
            ->take(8)
            ->get();

        // Recent approved payments
        $recentPayments = Payment::with(['member'])
            ->where('status', 'approved')
            ->latest('verified_at')
            ->take(6)
            ->get();

        // Recent members
        $recentMembers = Member::with('user')->latest()->take(6)->get();

        // Announcements
        $announcements = Announcement::latest()->take(4)->get();

        // Years for filter dropdown
        $availableYears = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('admin.dashboard', compact(
            'stats',
            'pendingPayments',
            'recentPayments',
            'recentMembers',
            'announcements',
            'year',
            'month',
            'availableYears'
        ));
    }

    /**
     * Send monthly payment reminders to all unpaid members for current month.
     */
    public function sendReminders(Request $request)
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $monthName = $now->format('F Y');

        $activeMembers = Member::where('status', 'active')->with('user')->get();
        $remindedCount = 0;

        foreach ($activeMembers as $member) {
            $isPaidOrPending = Payment::where('member_id', $member->id)
                ->where('payment_month', $month)
                ->where('payment_year', $year)
                ->whereIn('status', ['approved', 'pending'])
                ->exists();

            if (!$isPaidOrPending && $member->user) {
                CommitteeNotification::sendToUser(
                    $member->user->id,
                    'Monthly Seva Payment Reminder 🙏',
                    "Your monthly committee contribution of ₹{$member->monthly_amount} for {$monthName} is pending. Please pay via QR/UPI.",
                    'reminder',
                    route('member.payments.create')
                );
                $remindedCount++;
            }
        }

        return back()->with('success', "Reminders sent successfully to {$remindedCount} member(s) for {$monthName}.");
    }
}
