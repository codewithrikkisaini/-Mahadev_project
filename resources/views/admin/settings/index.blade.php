@extends('layouts.admin')

@section('title', 'Committee Settings')
@section('header_title', 'System & Committee Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-950/40 via-slate-900/80 to-slate-900/60 border border-amber-500/30 backdrop-blur-md flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
            <i class="fa-solid fa-sliders"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-100 font-cinzel">Committee & UPI Gateway Configuration</h3>
            <p class="text-xs text-slate-400">Configure monthly seva fees, UPI payment receiver details, receipt numbering & temple profile.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. Committee Profile Section -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl p-6 sm:p-8 space-y-4">
            <h4 class="text-sm font-bold text-amber-300 font-cinzel uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-om text-amber-400"></i>
                <span>Temple & Committee Identity</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="temple_name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Temple Name *
                    </label>
                    <input
                        type="text"
                        id="temple_name"
                        name="temple_name"
                        value="{{ old('temple_name', $settings['temple_name']) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>

                <div>
                    <label for="committee_name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Committee / Trust Name *
                    </label>
                    <input
                        type="text"
                        id="committee_name"
                        name="committee_name"
                        value="{{ old('committee_name', $settings['committee_name']) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="committee_tagline" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Tagline / Sacred Motto
                    </label>
                    <input
                        type="text"
                        id="committee_tagline"
                        name="committee_tagline"
                        value="{{ old('committee_tagline', $settings['committee_tagline']) }}"
                        placeholder="e.g. हर हर महादेव • सेवा ही संकल्प"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>

                <div>
                    <label for="contact_number" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Contact Mobile / Phone
                    </label>
                    <input
                        type="text"
                        id="contact_number"
                        name="contact_number"
                        value="{{ old('contact_number', $settings['contact_number']) }}"
                        placeholder="+91 9876543210"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>

                <div>
                    <label for="contact_email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Official Contact Email
                    </label>
                    <input
                        type="email"
                        id="contact_email"
                        name="contact_email"
                        value="{{ old('contact_email', $settings['contact_email']) }}"
                        placeholder="contact@mandirseva.com"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <div>
                <label for="committee_address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Temple & Office Address (Printed on Receipts)
                </label>
                <textarea
                    id="committee_address"
                    name="committee_address"
                    rows="2"
                    placeholder="Temple complex location, Pin code, City"
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none"
                >{{ old('committee_address', $settings['committee_address']) }}</textarea>
            </div>
        </div>

        <!-- 2. UPI & Payment Settings -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl p-6 sm:p-8 space-y-4">
            <h4 class="text-sm font-bold text-amber-300 font-cinzel uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-qrcode text-amber-400"></i>
                <span>UPI & Monthly Seva Fee Settings</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Monthly Amount -->
                <div>
                    <label for="monthly_amount" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Monthly Contribution (₹) *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-amber-400 font-bold font-cinzel">₹</span>
                        <input
                            type="number"
                            id="monthly_amount"
                            name="monthly_amount"
                            value="{{ old('monthly_amount', $settings['monthly_amount']) }}"
                            required
                            min="1"
                            step="1"
                            class="w-full pl-8 pr-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-amber-400 font-cinzel font-bold text-sm outline-none focus:border-amber-500"
                        >
                    </div>
                </div>

                <!-- UPI ID -->
                <div>
                    <label for="upi_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Mandir UPI ID (VPA) *
                    </label>
                    <input
                        type="text"
                        id="upi_id"
                        name="upi_id"
                        value="{{ old('upi_id', $settings['upi_id']) }}"
                        required
                        placeholder="e.g. mandir@upi or 9876543210@paytm"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 font-mono"
                    >
                </div>

                <!-- Payee Name -->
                <div>
                    <label for="payee_name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Account / Payee Name *
                    </label>
                    <input
                        type="text"
                        id="payee_name"
                        name="payee_name"
                        value="{{ old('payee_name', $settings['payee_name']) }}"
                        required
                        placeholder="Shri Mahadev Mandir"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Receipt Prefix -->
                <div>
                    <label for="receipt_prefix" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Receipt Serial Prefix *
                    </label>
                    <input
                        type="text"
                        id="receipt_prefix"
                        name="receipt_prefix"
                        value="{{ old('receipt_prefix', $settings['receipt_prefix']) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 font-mono text-sm outline-none focus:border-amber-500"
                    >
                    <p class="text-[11px] text-slate-500 mt-1">e.g. MSC-PAY generates MSC-PAY-00001</p>
                </div>

                <!-- Custom QR Code Image Upload -->
                <div>
                    <label for="qr_code_image" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Upload Custom Bank QR Code Image (Optional)
                    </label>
                    <input
                        type="file"
                        id="qr_code_image"
                        name="qr_code_image"
                        accept="image/*"
                        class="w-full px-4 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-300 text-xs outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-amber-600 file:text-white"
                    >
                    @if(!empty($settings['qr_code_image']))
                        <div class="mt-2 flex items-center gap-2 text-xs text-emerald-400">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Current Custom QR active</span>
                            <a href="{{ asset('storage/' . $settings['qr_code_image']) }}" target="_blank" class="underline text-amber-400 ml-2">Preview &nearr;</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Instructions -->
            <div>
                <label for="payment_instructions" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Member Payment Instructions (Shown on Payment Page)
                </label>
                <textarea
                    id="payment_instructions"
                    name="payment_instructions"
                    rows="3"
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none"
                >{{ old('payment_instructions', $settings['payment_instructions']) }}</textarea>
            </div>
        </div>

        <!-- 3. System Preferences -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl p-6 sm:p-8 space-y-4">
            <h4 class="text-sm font-bold text-amber-300 font-cinzel uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-gear text-amber-400"></i>
                <span>System Identification</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="member_prefix" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Member ID Code Prefix *
                    </label>
                    <input
                        type="text"
                        id="member_prefix"
                        name="member_prefix"
                        value="{{ old('member_prefix', $settings['member_prefix']) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 font-mono text-sm outline-none focus:border-amber-500"
                    >
                    <p class="text-[11px] text-slate-500 mt-1">e.g. MSC generates MSC00001</p>
                </div>

                <div>
                    <label for="currency_symbol" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Currency Symbol *
                    </label>
                    <input
                        type="text"
                        id="currency_symbol"
                        name="currency_symbol"
                        value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_self_registration" value="1" {{ old('allow_self_registration', $settings['allow_self_registration']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-amber-600 focus:ring-amber-500/30">
                    <div>
                        <span class="text-xs font-bold text-slate-200 block">Allow Public Online Member Self-Registration</span>
                        <span class="text-[11px] text-slate-400">When enabled, devotees can register directly from the website registration page</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end">
            <button
                type="submit"
                class="px-8 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-orange-950/50 flex items-center gap-2 transition transform active:scale-95"
            >
                <i class="fa-solid fa-floppy-disk"></i> Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
