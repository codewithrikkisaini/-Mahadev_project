<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;
        $members = Member::all();

        $receiptIndex = 1;

        // Current month & year: August 2026
        $currentYear = 2026;
        $currentMonth = 8;

        foreach ($members as $member) {
            $joiningDate = Carbon::parse($member->joining_date);
            $startMonth = $joiningDate->month;
            $startYear = $joiningDate->year;

            // Past months (Jan to July 2026)
            for ($m = $startMonth; $m < $currentMonth; $m++) {
                // Almost all members paid past months
                $paymentDate = Carbon::create($currentYear, $m, rand(2, 10));
                $receiptNumber = sprintf('MSC-PAY-%05d', $receiptIndex++);
                $utr = 'UPI' . rand(100000000000, 999999999999);

                Payment::updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'payment_month' => $m,
                        'payment_year' => $currentYear,
                    ],
                    [
                        'amount' => $member->monthly_amount,
                        'payment_date' => $paymentDate->format('Y-m-d'),
                        'transaction_id' => $utr,
                        'payment_method' => 'upi_qr',
                        'receipt_number' => $receiptNumber,
                        'status' => 'approved',
                        'verified_by' => $adminId,
                        'verified_at' => $paymentDate->copy()->addHours(3),
                        'notes' => 'Monthly Seva contribution verified via UPI UTR',
                    ]
                );
            }

            // Current Month (August 2026)
            // Member 1 to 10: Approved
            // Member 11 and 12: Pending verification
            // Member 13, 14, 15: Unpaid
            if ($member->id <= 10) {
                $paymentDate = Carbon::create($currentYear, $currentMonth, rand(1, 8));
                $receiptNumber = sprintf('MSC-PAY-%05d', $receiptIndex++);
                $utr = 'UPI' . rand(100000000000, 999999999999);

                Payment::updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'payment_month' => $currentMonth,
                        'payment_year' => $currentYear,
                    ],
                    [
                        'amount' => $member->monthly_amount,
                        'payment_date' => $paymentDate->format('Y-m-d'),
                        'transaction_id' => $utr,
                        'payment_method' => 'upi_qr',
                        'receipt_number' => $receiptNumber,
                        'status' => 'approved',
                        'verified_by' => $adminId,
                        'verified_at' => $paymentDate->copy()->addHours(2),
                        'notes' => 'August Seva approved',
                    ]
                );
            } elseif ($member->id == 11 || $member->id == 12) {
                $paymentDate = Carbon::create($currentYear, $currentMonth, 12);
                $utr = 'UPI' . rand(100000000000, 999999999999);

                Payment::updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'payment_month' => $currentMonth,
                        'payment_year' => $currentYear,
                    ],
                    [
                        'amount' => $member->monthly_amount,
                        'payment_date' => $paymentDate->format('Y-m-d'),
                        'transaction_id' => $utr,
                        'payment_method' => 'upi_qr',
                        'status' => 'pending',
                        'notes' => 'Member submitted UPI payment with UTR screenshot',
                    ]
                );
            }
            // Member 13-15 remain unpaid for August
        }
    }
}
