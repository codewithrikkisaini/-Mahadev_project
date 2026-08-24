<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Committee
            ['key' => 'committee_name', 'value' => 'Shri Mahadev Mandir Seva Committee', 'group' => 'committee', 'type' => 'string', 'label' => 'Committee Name'],
            ['key' => 'temple_name', 'value' => 'Shri Mahadev Mandir', 'group' => 'committee', 'type' => 'string', 'label' => 'Temple Name'],
            ['key' => 'committee_address', 'value' => 'Main Temple Complex, Mandir Marg, Shiv Nagar', 'group' => 'committee', 'type' => 'text', 'label' => 'Address'],
            ['key' => 'contact_number', 'value' => '+91 98765 43210', 'group' => 'committee', 'type' => 'string', 'label' => 'Contact Number'],
            ['key' => 'contact_email', 'value' => 'committee@mandirseva.com', 'group' => 'committee', 'type' => 'string', 'label' => 'Contact Email'],
            ['key' => 'committee_tagline', 'value' => 'हर हर महादेव • सेवा ही संकल्प • धर्मो रक्षति रक्षितः', 'group' => 'committee', 'type' => 'string', 'label' => 'Tagline'],

            // Payment
            ['key' => 'monthly_amount', 'value' => '200', 'group' => 'payment', 'type' => 'number', 'label' => 'Monthly Committee Amount'],
            ['key' => 'upi_id', 'value' => 'templecommittee@upi', 'group' => 'payment', 'type' => 'string', 'label' => 'UPI ID'],
            ['key' => 'payee_name', 'value' => 'Shri Mahadev Mandir Committee', 'group' => 'payment', 'type' => 'string', 'label' => 'Payee Name'],
            ['key' => 'payment_instructions', 'value' => '1. Scan the dynamic QR code using Google Pay / PhonePe / Paytm / BHIM.\n2. Pay the exact monthly amount (₹200).\n3. Enter the 12-digit UTR/Ref number and upload payment screenshot.\n4. Admin will verify and issue the official receipt.', 'group' => 'payment', 'type' => 'text', 'label' => 'Payment Instructions'],
            ['key' => 'receipt_prefix', 'value' => 'MSC-PAY', 'group' => 'payment', 'type' => 'string', 'label' => 'Receipt Prefix'],

            // System
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'system', 'type' => 'string', 'label' => 'Currency Symbol'],
            ['key' => 'member_prefix', 'value' => 'MSC', 'group' => 'system', 'type' => 'string', 'label' => 'Member Code Prefix'],
            ['key' => 'allow_self_registration', 'value' => '1', 'group' => 'system', 'type' => 'boolean', 'label' => 'Allow Self Registration'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
