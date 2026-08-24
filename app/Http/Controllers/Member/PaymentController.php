<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\CommitteeNotification;
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
     * Complete Member Payment History.
     */
    public function index()
    {
        $member = auth()->user()->member;
        $member->load(['payments']);

        $payments = $member->payments()->paginate(15);
        $summary = $member->payment_summary;
        $dueMonths = $member->getDueMonthsList();

        return view('member.payments.index', compact('member', 'payments', 'summary', 'dueMonths'));
    }

    /**
     * Dedicated QR / UPI Payment Page.
     */
    public function create(Request $request)
    {
        $member = auth()->user()->member;
        $monthlyAmount = $member->monthly_amount;

        $committeeName = Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee');
        $templeName = Setting::get('temple_name', 'Shri Mahadev Mandir');
        $upiId = Setting::get('upi_id', 'templecommittee@upi');
        $payeeName = Setting::get('payee_name', 'Mandir Seva Committee');
        $qrCodeImage = Setting::get('qr_code_image', null);
        $paymentInstructions = Setting::get('payment_instructions', '');

        // List of all months from joining date to current month + next month
        $dueMonths = $member->getDueMonthsList();

        // Get approved and pending payment keys
        $approvedMonths = $member->approvedPayments()->get()->map(fn ($p) => "{$p->payment_year}-{$p->payment_month}")->toArray();
        $pendingMonths = $member->pendingPayments()->get()->map(fn ($p) => "{$p->payment_year}-{$p->payment_month}")->toArray();

        // Standard UPI Pay URI
        $upiUri = "upi://pay?pa={$upiId}&pn=" . urlencode($payeeName) . "&am={$monthlyAmount}&cu=INR&tn=" . urlencode("Seva {$member->member_code}");

        // Preselect the first unpaid month if available
        $defaultMonth = Carbon::now()->month;
        $defaultYear = Carbon::now()->year;

        foreach ($dueMonths as $dm) {
            $key = "{$dm['year']}-{$dm['month']}";
            if (!in_array($key, $approvedMonths) && !in_array($key, $pendingMonths)) {
                $defaultMonth = $dm['month'];
                $defaultYear = $dm['year'];
                break;
            }
        }

        return view('member.payments.create', compact(
            'member',
            'monthlyAmount',
            'committeeName',
            'templeName',
            'upiId',
            'payeeName',
            'qrCodeImage',
            'paymentInstructions',
            'dueMonths',
            'approvedMonths',
            'pendingMonths',
            'upiUri',
            'defaultMonth',
            'defaultYear'
        ));
    }

    /**
     * Handle payment submission.
     */
    public function store(Request $request)
    {
        $member = auth()->user()->member;

        $validated = $request->validate([
            'payment_month' => ['required', 'integer', 'between:1,12'],
            'payment_year' => ['required', 'integer', 'between:2020,2035'],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_id' => ['required', 'string', 'min:6', 'max:100'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'screenshot' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $monthName = Carbon::create($validated['payment_year'], $validated['payment_month'], 1)->format('F Y');

        // Prevent duplicate payment for same month
        if ($this->paymentService->hasDuplicatePayment($member->id, $validated['payment_month'], $validated['payment_year'])) {
            return back()->withInput()->withErrors([
                'payment_month' => "You have already submitted a payment for {$monthName}. Please check your payment history.",
            ]);
        }

        // Upload screenshot
        $screenshotPath = $request->file('screenshot')->store('payments/screenshots', 'public');

        $payment = Payment::create([
            'member_id' => $member->id,
            'amount' => $validated['amount'],
            'payment_month' => $validated['payment_month'],
            'payment_year' => $validated['payment_year'],
            'payment_date' => $validated['payment_date'],
            'transaction_id' => strtoupper(trim($validated['transaction_id'])),
            'payment_method' => 'upi_qr',
            'screenshot_path' => $screenshotPath,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // In-app confirmation notification
        CommitteeNotification::sendToUser(
            auth()->id(),
            'Payment Submitted for Verification',
            "Your payment submission of ₹{$payment->amount} for {$monthName} (UTR: {$payment->transaction_id}) is under review by the committee admin.",
            'payment_pending',
            route('member.payments.index')
        );

        return redirect()->route('member.payments.index')->with('success', "Payment for {$monthName} submitted successfully! Your submission is pending admin verification.");
    }

    /**
     * View / Download receipt for approved payment.
     */
    public function receipt(Payment $payment)
    {
        $member = auth()->user()->member;

        if ($payment->member_id !== $member->id) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        if ($payment->status !== 'approved') {
            return back()->with('error', 'Receipt is only available once payment is verified and approved.');
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
