<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of all payments with filters.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $month = $request->get('month');
        $year = $request->get('year');
        $search = $request->get('search');

        $query = Payment::with(['member.user', 'verifier']);

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($month && is_numeric($month)) {
            $query->where('payment_month', $month);
        }

        if ($year && is_numeric($year)) {
            $query->where('payment_year', $year);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($mq) use ($search) {
                        $mq->where('name', 'like', "%{$search}%")
                            ->orWhere('member_code', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $totalApprovedAmount = Payment::where('status', 'approved')->sum('amount');
        $pendingCount = Payment::where('status', 'pending')->count();
        $approvedCount = Payment::where('status', 'approved')->count();
        $rejectedCount = Payment::where('status', 'rejected')->count();

        $availableYears = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('admin.payments.index', compact(
            'payments',
            'status',
            'month',
            'year',
            'search',
            'totalApprovedAmount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'availableYears'
        ));
    }

    /**
     * Dedicated Pending Verifications view.
     */
    public function pending(Request $request)
    {
        $pendingPayments = Payment::with(['member.user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.payments.pending', compact('pendingPayments'));
    }

    /**
     * Display a specific payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['member.user', 'verifier']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Approve a payment.
     */
    public function approve(Request $request, Payment $payment)
    {
        $notes = $request->input('notes');
        $this->paymentService->approvePayment($payment, auth()->user(), $notes);

        return back()->with('success', "Payment for {$payment->member->name} ({$payment->formatted_month}) approved! Receipt No: {$payment->receipt_number}");
    }

    /**
     * Reject a payment.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $reason = $request->input('rejection_reason');
        $this->paymentService->rejectPayment($payment, auth()->user(), $reason);

        return back()->with('warning', "Payment for {$payment->member->name} ({$payment->formatted_month}) was rejected.");
    }

    /**
     * View / Print official receipt.
     */
    public function receipt(Payment $payment)
    {
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Receipt is only available for approved payments.');
        }

        $payment->load(['member.user', 'verifier']);
        $committeeName = Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee');
        $templeName = Setting::get('temple_name', 'Shri Mahadev Mandir');
        $committeeAddress = Setting::get('committee_address', '');
        $contactNumber = Setting::get('contact_number', '');

        return view('receipts.template', compact(
            'payment',
            'committeeName',
            'templeName',
            'committeeAddress',
            'contactNumber'
        ));
    }
}
