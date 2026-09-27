<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_code',
        'name',
        'mobile',
        'email',
        'address',
        'joining_date',
        'status',
        'custom_monthly_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'custom_monthly_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_year', 'desc')->orderBy('payment_month', 'desc');
    }

    public function approvedPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'approved');
    }

    public function pendingPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'pending');
    }

    /**
     * Get effective monthly fee for this member.
     */
    public function getMonthlyAmountAttribute(): float
    {
        return $this->custom_monthly_amount ? (float) $this->custom_monthly_amount : Setting::getMonthlyAmount();
    }

    /**
     * Calculate total paid by this member.
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->approvedPayments()->sum('amount');
    }

    /**
     * Calculate total months due from joining date to current month.
     */
    public function getDueMonthsList(): array
    {
        $joining = $this->joining_date ? Carbon::parse($this->joining_date) : Carbon::create(2026, 1, 1);
        $current = Carbon::now();

        $startYear = $joining->year;
        $startMonth = $joining->month;

        $endYear = $current->year;
        $endMonth = $current->month;

        $months = [];
        $cursor = Carbon::create($startYear, $startMonth, 1);
        $endCursor = Carbon::create($endYear, $endMonth, 1);

        while ($cursor->lessThanOrEqualTo($endCursor)) {
            $months[] = [
                'month' => $cursor->month,
                'year' => $cursor->year,
                'label' => $cursor->format('F Y'),
                'short_label' => $cursor->format('M Y'),
            ];
            $cursor->addMonth();
        }

        return array_reverse($months);
    }

    /**
     * Get summary of paid vs pending months for this member.
     */
    public function getPaymentSummaryAttribute(): array
    {
        $dueMonths = $this->getDueMonthsList();
        $approvedPayments = $this->approvedPayments()->get()->keyBy(function ($item) {
            return "{$item->payment_year}-{$item->payment_month}";
        });
        $pendingPayments = $this->pendingPayments()->get()->keyBy(function ($item) {
            return "{$item->payment_year}-{$item->payment_month}";
        });

        $paidCount = 0;
        $pendingCount = 0;
        $unpaidCount = 0;

        $monthlyAmount = $this->monthly_amount;

        foreach ($dueMonths as $dm) {
            $key = "{$dm['year']}-{$dm['month']}";
            if (isset($approvedPayments[$key])) {
                $paidCount++;
            } elseif (isset($pendingPayments[$key])) {
                $pendingCount++;
            } else {
                $unpaidCount++;
            }
        }

        $totalDueMonths = count($dueMonths);
        $totalPaidAmount = $this->total_paid;
        $totalPendingAmount = ($unpaidCount + $pendingCount) * $monthlyAmount;

        return [
            'total_months' => $totalDueMonths,
            'paid_months' => $paidCount,
            'pending_verification_months' => $pendingCount,
            'unpaid_months' => $unpaidCount,
            'total_pending_months' => $unpaidCount + $pendingCount,
            'total_paid' => $totalPaidAmount,
            'total_pending' => $totalPendingAmount,
        ];
    }

    /**
     * Check if a specific month is paid.
     */
    public function isMonthPaid(int $month, int $year): bool
    {
        return $this->approvedPayments()
            ->where('payment_month', $month)
            ->where('payment_year', $year)
            ->exists();
    }

    /**
     * Check current month payment status.
     */
    public function getCurrentMonthStatusAttribute(): string
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $payment = $this->payments()
            ->where('payment_month', $month)
            ->where('payment_year', $year)
            ->first();

        if ($payment) {
            return $payment->status; // 'approved', 'pending', 'rejected'
        }

        return 'unpaid';
    }

    /**
     * Generate sequential member code like MSC00001.
     */
    public static function generateNextCode(): string
    {
        $prefix = Setting::get('member_prefix', 'MSC');
        
        $existingCodes = static::whereNotNull('member_code')->pluck('member_code');
        $maxNumber = 0;

        foreach ($existingCodes as $code) {
            if (preg_match('/(\d+)$/', $code, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;
        $candidate = sprintf('%s%05d', $prefix, $nextNumber);

        while (static::where('member_code', $candidate)->exists()) {
            $nextNumber++;
            $candidate = sprintf('%s%05d', $prefix, $nextNumber);
        }

        return $candidate;
    }
}
