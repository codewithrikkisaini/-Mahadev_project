@extends('layouts.member')

@section('title', 'Pay Monthly Seva via UPI / QR')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    copied: false,
    selectedAmount: {{ $monthlyAmount }},
    selectedMonth: {{ $defaultMonth }},
    selectedYear: {{ $defaultYear }},
    upiId: '{{ $upiId }}',
    payeeName: '{{ $payeeName }}',
    memberCode: '{{ $member->member_code }}',
    imgPreview: null,
    getUpiUri() {
        return `upi://pay?pa=${this.upiId}&pn=${encodeURIComponent(this.payeeName)}&am=${this.selectedAmount}&cu=INR&tn=${encodeURIComponent('Seva ' + this.memberCode + ' M' + this.selectedMonth + '-' + this.selectedYear)}`;
    },
    copyUpi() {
        navigator.clipboard.writeText(this.upiId);
        this.copied = true;
        setTimeout(() => this.copied = false, 2500);
    },
    previewImage(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (ev) => { this.imgPreview = ev.target.result; };
            reader.readAsDataURL(file);
        } else {
            this.imgPreview = null;
        }
    }
}">
    <!-- Top Devotional Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-950/60 via-slate-900/90 to-orange-950/60 border border-amber-500/30 backdrop-blur-md flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-100 font-cinzel sacred-text-glow">Monthly Seva Contribution Gateway</h3>
                <p class="text-xs text-slate-400">Scan QR or transfer via any UPI App & submit transaction ID with payment proof.</p>
            </div>
        </div>

        <a href="{{ route('member.payments.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
            <i class="fa-solid fa-receipt"></i> Payment History
        </a>
    </div>

    <!-- 2 Column Payment Flow: Left QR Code & UPI Details | Right Submission Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: QR Code & UPI Transfer Card (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="p-6 rounded-3xl bg-slate-900/90 border border-amber-500/30 shadow-2xl backdrop-blur-xl flex flex-col items-center text-center relative overflow-hidden sacred-glow">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600"></div>

                <div class="mb-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-950/80 border border-amber-500/30 text-amber-400 text-[11px] font-bold">
                        <i class="fa-brands fa-google-pay text-base text-sky-400"></i>
                        <span>UPI QR Payment</span>
                    </div>
                    <h4 class="text-base font-bold font-cinzel text-slate-100 mt-2 flex items-center justify-center gap-2">
                        <span>{{ $payeeName }}</span>
                    </h4>
                    <p class="font-mono text-[11px] text-amber-400/90 font-semibold">{{ $upiId }}</p>
                </div>

                <!-- QR Code Display Box -->
                <div class="p-3 bg-white rounded-2xl shadow-2xl border-4 border-amber-500/40 my-2 relative group">
                    @if(!empty($qrCodeImage))
                        <img src="{{ asset('storage/' . $qrCodeImage) }}" alt="Mandir QR" class="w-48 h-48 sm:w-52 sm:h-52 object-contain rounded-lg">
                    @else
                        <!-- Dynamic QR Generated with Rikki Saini UPI URI -->
                        <img
                            :src="'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=4&data=' + encodeURIComponent(getUpiUri())"
                            alt="Scan UPI QR"
                            class="w-48 h-48 sm:w-52 sm:h-52 object-contain rounded-lg"
                        >
                    @endif
                    <div class="mt-1 flex items-center justify-center gap-1.5 text-[10px] text-slate-900 font-bold">
                        <i class="fa-brands fa-google-pay text-sm text-blue-600"></i>
                        <span>Scan with GPay / PhonePe / Paytm</span>
                    </div>
                </div>

                <!-- Amount Pill -->
                <div class="my-3 px-4 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300">
                    <span class="text-xs text-slate-400 font-medium mr-1">Paying:</span>
                    <span class="text-lg font-extrabold font-cinzel" x-text="'₹' + selectedAmount"></span>
                </div>

                <!-- UPI ID & Copy Action -->
                <div class="w-full bg-slate-950/80 p-3 rounded-2xl border border-slate-800 mt-1 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400 text-[11px]">UPI ID:</span>
                        <button
                            type="button"
                            @click="copyUpi()"
                            class="text-amber-400 hover:text-amber-300 text-[11px] font-semibold flex items-center gap-1"
                        >
                            <i class="fa-solid" :class="copied ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                            <span x-text="copied ? 'Copied!' : 'Copy UPI'"></span>
                        </button>
                    </div>
                    <div class="font-mono text-xs text-amber-300 font-bold bg-slate-900 py-1.5 px-3 rounded-xl border border-slate-800 truncate">
                        {{ $upiId }}
                    </div>
                </div>

                <!-- Direct UPI App Launch for Mobile Devices -->
                <div class="w-full mt-4">
                    <a
                        :href="getUpiUri()"
                        class="w-full py-2.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-xs transition shadow-md flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-mobile-screen-button"></i> Open in UPI App (GPay / PhonePe / Paytm)
                    </a>
                </div>
            </div>

            <!-- Instructions Card -->
            @if($paymentInstructions)
                <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 text-xs text-slate-300 space-y-1.5">
                    <div class="font-bold text-amber-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info"></i> Instructions:
                    </div>
                    <p class="text-slate-400 text-[11px] leading-relaxed">{{ $paymentInstructions }}</p>
                </div>
            @endif
        </div>

        <!-- Right: Payment Submission Verification Form (7 Cols) -->
        <div class="lg:col-span-7">
            <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl backdrop-blur-xl relative">
                <div class="pb-4 mb-5 border-b border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold font-cinzel text-amber-300">Submit Payment Proof</h4>
                        <p class="text-xs text-slate-400">Step 2: Enter transaction details after payment</p>
                    </div>
                    <span class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-sm">
                        2
                    </span>
                </div>

                <form method="POST" action="{{ route('member.payments.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Month and Year Selection -->
                    <div>
                        <label for="payment_month_select" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Contribution Month & Year *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <select
                                id="payment_month_select"
                                name="payment_month"
                                x-model="selectedMonth"
                                required
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500"
                            >
                                @foreach($dueMonths as $dm)
                                    @php
                                        $key = "{$dm['year']}-{$dm['month']}";
                                        $isApproved = in_array($key, $approvedMonths);
                                        $isPending = in_array($key, $pendingMonths);
                                    @endphp
                                    <option
                                        value="{{ $dm['month'] }}"
                                        {{ $isApproved ? 'disabled' : '' }}
                                        class="{{ $isApproved ? 'text-slate-600' : ($isPending ? 'text-amber-400' : 'text-slate-200') }}"
                                    >
                                        {{ $dm['label'] }} {{ $isApproved ? '(Paid ✓)' : ($isPending ? '(Under Verification ~)' : '') }}
                                    </option>
                                @endforeach
                            </select>

                            <select
                                name="payment_year"
                                x-model="selectedYear"
                                required
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500"
                            >
                                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Amount & Payment Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="amount" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                                Amount Paid (₹) *
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-amber-400 font-bold font-cinzel">₹</span>
                                <input
                                    type="number"
                                    id="amount"
                                    name="amount"
                                    x-model="selectedAmount"
                                    value="{{ old('amount', $monthlyAmount) }}"
                                    required
                                    min="1"
                                    step="1"
                                    class="w-full pl-8 pr-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-amber-400 font-cinzel font-bold text-sm outline-none focus:border-amber-500"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="payment_date" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                                Payment Date *
                            </label>
                            <input
                                type="date"
                                id="payment_date"
                                name="payment_date"
                                value="{{ old('payment_date', date('Y-m-d')) }}"
                                max="{{ date('Y-m-d') }}"
                                required
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500"
                            >
                        </div>
                    </div>

                    <!-- 12-Digit UTR / Transaction ID -->
                    <div>
                        <label for="transaction_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            12-Digit UTR / Transaction ID / Reference No *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <i class="fa-solid fa-receipt text-amber-500/70 text-sm"></i>
                            </div>
                            <input
                                type="text"
                                id="transaction_id"
                                name="transaction_id"
                                value="{{ old('transaction_id') }}"
                                required
                                minlength="6"
                                placeholder="e.g. 423589123456 or UPI Ref ID"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 font-mono text-sm uppercase outline-none focus:border-amber-500"
                            >
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Look for UPI Reference No. / UTR in your GPay, PhonePe, or Paytm receipt</p>
                    </div>

                    <!-- Screenshot File Upload with Live Preview -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Upload Payment Proof Screenshot *
                        </label>
                        <div class="border-2 border-dashed border-slate-700 hover:border-amber-500/70 rounded-2xl p-4 bg-slate-950/60 transition text-center relative cursor-pointer">
                            <input
                                type="file"
                                name="screenshot"
                                accept="image/*"
                                required
                                @change="previewImage($event)"
                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10"
                            >
                            <template x-if="!imgPreview">
                                <div class="py-4">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-amber-400/80 mb-2"></i>
                                    <p class="text-xs font-semibold text-slate-200">Tap or Drag Screenshot here</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">PNG, JPG, JPEG or WEBP (Max 4MB)</p>
                                </div>
                            </template>
                            <template x-if="imgPreview">
                                <div class="relative inline-block">
                                    <img :src="imgPreview" alt="Preview" class="max-h-40 rounded-xl object-contain border border-amber-500/40">
                                    <span class="block text-[11px] text-emerald-400 font-semibold mt-1">✓ Screenshot Selected (Tap to change)</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Note (Optional)
                        </label>
                        <input
                            type="text"
                            id="notes"
                            name="notes"
                            value="{{ old('notes') }}"
                            placeholder="Any message for committee admin"
                            class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:via-orange-500 hover:to-amber-600 text-white font-bold rounded-2xl text-sm transition transform active:scale-[0.98] shadow-xl shadow-orange-950/50 flex items-center justify-center gap-2 sacred-glow mt-2"
                    >
                        <i class="fa-solid fa-paper-plane"></i> Submit Seva Contribution for Verification
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
