<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Setting::set('upi_id', 'rikkisaini4455-4@oksbi', 'payment', 'string', 'UPI ID');
            Setting::set('payee_name', 'Rikki Saini', 'payment', 'string', 'Payee Name');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
