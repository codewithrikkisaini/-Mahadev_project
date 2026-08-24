<?php

namespace App\Services;

use App\Models\CommitteeNotification;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Get admin dashboard summary statistics.
     */
    public function getAdminDashboardStats(int $year = null, int $month = null): array
    {
        $now = Carbon::now();
        $year = $year ?? $now->year;
        $month = $month ?? $now->month;

        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $pendingPaymentsCount = Payment::where('status', 'pending')->count();

        // Calculate expected collection for this month
        $activeMembersList = Member::where('status', 'active')->get();
        $expectedCollection = $activeMembersList->sum(function ($member) {
            return $member->monthly_amount;
        });

        // Actual approved collection for this month
        $collectedThisMonth = Payment::where('status', 'approved')
            ->where('payment_year', $year)
            ->where('payment_month', $month)
            ->sum('amount');

        $pendingCollectionThisMonth = max(0, $expectedCollection - $collectedThisMonth);

        // All time collected
        $totalAllTimeCollection = Payment::where('status', 'approved')->sum('amount');

        // Recent 6-12 months collection trend for chart
        $monthlyTrends = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create($year, $m, 1)->format('M');
            $collected = (float) Payment::where('status', 'approved')
                ->where('payment_year', $year)
                ->where('payment_month', $m)
                ->sum('amount');

            $expected = (float) $expectedCollection;

            $monthlyTrends[] = [
                'month_num' => $m,
                'month_name' => $monthName,
                'collected' => $collected,
                'expected' => $expected,
                'pending' => max(0, $expected - $collected),
            ];
        }

        return [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'pending_verifications' => $pendingPaymentsCount,
            'current_month_name' => Carbon::create($year, $month, 1)->format('F Y'),
            'expected_collection' => (float) $expectedCollection,
            'collected_this_month' => (float) $collectedThisMonth,
            'pending_this_month' => (float) $pendingCollectionThisMonth,
            'total_all_time' => (float) $totalAllTimeCollection,
            'monthly_trends' => $monthlyTrends,
        ];
    }

    /**
     * Approve a payment and generate receipt.
     */
    public function approvePayment(Payment $payment, User $admin, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $notes) {
            $payment->update([
                'status' => 'approved',
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'receipt_number' => $payment->receipt_number ?? Payment::generateReceiptNumber(),
                'notes' => $notes ?? $payment->notes,
                'rejection_reason' => null,
            ]);

            // Send notification to member
            if ($payment->member && $payment->member->user_id) {
                $monthName = $payment->formatted_month;
                $amount = number_format($payment->amount, 2);
                CommitteeNotification::sendToUser(
                    $payment->member->user_id,
                    'Payment Approved! 🙏',
                    "Your payment of ₹{$amount} for {$monthName} has been approved. Receipt No: {$payment->receipt_number}",
                    'payment_approved',
                    route('member.payments.receipt', $payment->id)
                );
            }

            return $payment;
        });
    }

    /**
     * Reject a payment with a reason.
     */
    public function rejectPayment(Payment $payment, User $admin, string $reason): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $reason) {
            $payment->update([
                'status' => 'rejected',
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Send notification to member
            if ($payment->member && $payment->member->user_id) {
                $monthName = $payment->formatted_month;
                CommitteeNotification::sendToUser(
                    $payment->member->user_id,
                    'Payment Submission Rejected',
                    "Your payment submission for {$monthName} was rejected. Reason: {$reason}. Please re-submit with valid details.",
                    'payment_rejected',
                    route('member.payments.create')
                );
            }

            return $payment;
        });
    }

    /**
     * Check if a member already has a pending or approved payment for a month.
     */
    public function hasDuplicatePayment(int $memberId, int $month, int $year, ?int $excludePaymentId = null): bool
    {
        $query = Payment::where('member_id', $memberId)
            ->where('payment_month', $month)
            ->where('payment_year', $year)
            ->whereIn('status', ['approved', 'pending']);

        if ($excludePaymentId) {
            $query->where('id', '!=', $excludePaymentId);
        }

        return $query->exists();
    }
}
