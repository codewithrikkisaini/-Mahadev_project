<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->unsignedTinyInteger('payment_month'); // 1-12
            $table->unsignedSmallInteger('payment_year'); // e.g. 2026
            $table->date('payment_date');
            $table->string('transaction_id')->nullable(); // UTR / Ref No
            $table->string('payment_method')->default('upi_qr'); // upi_qr, cash, bank_transfer
            $table->string('screenshot_path')->nullable();
            $table->string('receipt_number')->nullable()->unique(); // e.g. MSC-PAY-00001
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'payment_year', 'payment_month']);
            $table->index('status');
            $table->index('receipt_number');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
