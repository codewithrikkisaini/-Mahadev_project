<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Reports index / Overview.
     */
    public function index(Request $request)
    {
        $year = (int) $request->get('year', Carbon::now()->year);
        $month = (int) $request->get('month', Carbon::now()->month);

        $stats = $this->paymentService->getAdminDashboardStats($year, $month);

        // Fetch all active members and their payment status for this month
        $members = Member::where('status', 'active')->orderBy('member_code', 'asc')->get();

        $monthPayments = Payment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->get()
            ->keyBy('member_id');

        $reportData = $members->map(function ($member) use ($monthPayments, $month, $year) {
            $payment = $monthPayments->get($member->id);
            return [
                'member' => $member,
                'monthly_amount' => $member->monthly_amount,
                'status' => $payment ? $payment->status : 'unpaid',
                'payment' => $payment,
            ];
        });

        $availableYears = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('admin.reports.index', compact(
            'stats',
            'reportData',
            'year',
            'month',
            'availableYears'
        ));
    }

    /**
     * Member-wise multi-month matrix report.
     */
    public function memberReport(Request $request)
    {
        $year = (int) $request->get('year', Carbon::now()->year);
        $selectedMemberId = $request->get('member_id');

        $membersQuery = Member::with(['approvedPayments'])->orderBy('member_code', 'asc');
        if ($selectedMemberId) {
            $membersQuery->where('id', $selectedMemberId);
        }
        $members = $membersQuery->get();

        // Get all approved and pending payments for the year
        $payments = Payment::where('payment_year', $year)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->groupBy('member_id');

        $availableYears = range(Carbon::now()->year - 2, Carbon::now()->year + 1);
        $allMembersList = Member::orderBy('name', 'asc')->get();

        return view('admin.reports.member', compact(
            'members',
            'payments',
            'year',
            'selectedMemberId',
            'availableYears',
            'allMembersList'
        ));
    }

    /**
     * Export Month-Wise Collection Report to CSV.
     */
    public function exportMonthlyCsv(Request $request): StreamedResponse
    {
        $year = (int) $request->get('year', Carbon::now()->year);
        $month = (int) $request->get('month', Carbon::now()->month);
        $monthName = Carbon::create($year, $month, 1)->format('F_Y');

        $members = Member::where('status', 'active')->orderBy('member_code', 'asc')->get();
        $payments = Payment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->get()
            ->keyBy('member_id');

        $filename = "MSCMS_Monthly_Report_{$monthName}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($members, $payments, $year, $month) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Member ID', 'Member Name', 'Mobile Number', 'Month & Year', 'Expected Amount (INR)', 'Paid Amount (INR)', 'Payment Status', 'Transaction ID / UTR', 'Receipt Number', 'Payment Date']);

            foreach ($members as $member) {
                $payment = $payments->get($member->id);
                $status = $payment ? strtoupper($payment->status) : 'UNPAID';
                $paidAmount = ($payment && $payment->status === 'approved') ? $payment->amount : '0.00';
                $utr = $payment ? $payment->transaction_id : '-';
                $receipt = $payment ? $payment->receipt_number : '-';
                $date = $payment ? $payment->payment_date->format('d-m-Y') : '-';

                fputcsv($handle, [
                    $member->member_code,
                    $member->name,
                    $member->mobile,
                    Carbon::create($year, $month, 1)->format('F Y'),
                    $member->monthly_amount,
                    $paidAmount,
                    $status,
                    $utr,
                    $receipt,
                    $date,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Member Matrix Report to CSV.
     */
    public function exportMemberMatrixCsv(Request $request): StreamedResponse
    {
        $year = (int) $request->get('year', Carbon::now()->year);
        $members = Member::orderBy('member_code', 'asc')->get();

        $payments = Payment::where('payment_year', $year)
            ->where('status', 'approved')
            ->get()
            ->groupBy('member_id');

        $filename = "MSCMS_Member_Matrix_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($members, $payments, $year) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            $headerRow = ['Member ID', 'Name', 'Mobile'];
            for ($m = 1; $m <= 12; $m++) {
                $headerRow[] = Carbon::create($year, $m, 1)->format('M');
            }
            $headerRow[] = 'Total Paid (INR)';

            fputcsv($handle, $headerRow);

            foreach ($members as $member) {
                $memberPayments = ($payments->get($member->id) ?? collect())->keyBy('payment_month');
                $row = [
                    $member->member_code,
                    $member->name,
                    $member->mobile,
                ];

                $totalMemberPaid = 0;
                for ($m = 1; $m <= 12; $m++) {
                    if ($memberPayments->has($m)) {
                        $amt = $memberPayments->get($m)->amount;
                        $row[] = "Paid (₹{$amt})";
                        $totalMemberPaid += $amt;
                    } else {
                        $row[] = 'Pending';
                    }
                }
                $row[] = $totalMemberPaid;

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
