<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            // Committee settings
            'committee_name' => Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee'),
            'temple_name' => Setting::get('temple_name', 'Shri Mahadev Mandir'),
            'committee_address' => Setting::get('committee_address', 'Near Main Temple Chowk, Shiv Nagar'),
            'contact_number' => Setting::get('contact_number', '+91 9876543210'),
            'contact_email' => Setting::get('contact_email', 'contact@mandirseva.com'),
            'committee_tagline' => Setting::get('committee_tagline', 'हर हर महादेव • सेवा ही संकल्प'),

            // Payment settings
            'monthly_amount' => Setting::get('monthly_amount', 200),
            'upi_id' => Setting::get('upi_id', 'templecommittee@upi'),
            'payee_name' => Setting::get('payee_name', 'Mandir Seva Committee'),
            'qr_code_image' => Setting::get('qr_code_image', null),
            'payment_instructions' => Setting::get('payment_instructions', 'Scan the QR code using any UPI App (Google Pay, PhonePe, Paytm) and pay the exact monthly amount. Enter the 12-digit UTR/Transaction ID and attach the screenshot for verification.'),
            'receipt_prefix' => Setting::get('receipt_prefix', 'MSC-PAY'),

            // System settings
            'currency_symbol' => Setting::get('currency_symbol', '₹'),
            'member_prefix' => Setting::get('member_prefix', 'MSC'),
            'allow_self_registration' => Setting::get('allow_self_registration', true),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Committee
            'committee_name' => ['required', 'string', 'max:255'],
            'temple_name' => ['required', 'string', 'max:255'],
            'committee_address' => ['nullable', 'string', 'max:500'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'committee_tagline' => ['nullable', 'string', 'max:255'],

            // Payment
            'monthly_amount' => ['required', 'numeric', 'min:1'],
            'upi_id' => ['required', 'string', 'max:100'],
            'payee_name' => ['required', 'string', 'max:255'],
            'qr_code_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'payment_instructions' => ['nullable', 'string'],
            'receipt_prefix' => ['required', 'string', 'max:20'],

            // System
            'currency_symbol' => ['required', 'string', 'max:10'],
            'member_prefix' => ['required', 'string', 'max:10'],
            'allow_self_registration' => ['nullable', 'boolean'],
        ]);

        // Save committee settings
        Setting::set('committee_name', $validated['committee_name'], 'committee', 'string', 'Committee Name');
        Setting::set('temple_name', $validated['temple_name'], 'committee', 'string', 'Temple Name');
        Setting::set('committee_address', $validated['committee_address'] ?? '', 'committee', 'text', 'Address');
        Setting::set('contact_number', $validated['contact_number'] ?? '', 'committee', 'string', 'Contact Number');
        Setting::set('contact_email', $validated['contact_email'] ?? '', 'committee', 'string', 'Contact Email');
        Setting::set('committee_tagline', $validated['committee_tagline'] ?? '', 'committee', 'string', 'Tagline');

        // Save payment settings
        Setting::set('monthly_amount', $validated['monthly_amount'], 'payment', 'number', 'Monthly Committee Amount');
        Setting::set('upi_id', $validated['upi_id'], 'payment', 'string', 'UPI ID');
        Setting::set('payee_name', $validated['payee_name'], 'payment', 'string', 'Payee Name');
        Setting::set('payment_instructions', $validated['payment_instructions'] ?? '', 'payment', 'text', 'Payment Instructions');
        Setting::set('receipt_prefix', strtoupper(trim($validated['receipt_prefix'])), 'payment', 'string', 'Receipt Prefix');

        // Handle QR image upload if present
        if ($request->hasFile('qr_code_image')) {
            $path = $request->file('qr_code_image')->store('settings/qr', 'public');
            Setting::set('qr_code_image', $path, 'payment', 'image', 'Custom QR Code Image');
        }

        // Save system settings
        Setting::set('currency_symbol', $validated['currency_symbol'], 'system', 'string', 'Currency Symbol');
        Setting::set('member_prefix', strtoupper(trim($validated['member_prefix'])), 'system', 'string', 'Member ID Prefix');
        Setting::set('allow_self_registration', $request->boolean('allow_self_registration'), 'system', 'boolean', 'Allow Self Registration');

        return back()->with('success', 'Settings updated successfully! Monthly committee amount is now ' . Setting::get('currency_symbol', '₹') . Setting::get('monthly_amount'));
    }
}
