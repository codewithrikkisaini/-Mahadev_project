<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'amount',
        'payment_month',
        'payment_year',
        'payment_date',
        'transaction_id',
        'payment_method',
        'screenshot_path',
        'receipt_number',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'verified_at' => 'datetime',
            'amount' => 'decimal:2',
            'payment_month' => 'integer',
            'payment_year' => 'integer',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Month name attribute (e.g. "August").
     */
    public function getMonthNameAttribute(): string
    {
        return Carbon::createFromDate($this->payment_year, $this->payment_month, 1)->format('F');
    }

    /**
     * Formatted month with year (e.g. "August 2026").
     */
    public function getFormattedMonthAttribute(): string
    {
        return Carbon::createFromDate($this->payment_year, $this->payment_month, 1)->format('F Y');
    }

    /**
     * Generate unique sequential receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = Setting::get('receipt_prefix', 'MSC-PAY');
        $lastPayment = static::whereNotNull('receipt_number')->orderBy('id', 'desc')->first();
        $nextNumber = $lastPayment ? ($lastPayment->id + 1) : 1;

        return sprintf('%s-%05d', $prefix, $nextNumber);
    }
}
