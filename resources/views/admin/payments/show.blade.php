@extends('layouts.admin')

@section('title', 'Payment Detail: ' . ($payment->receipt_number ?? $payment->transaction_id))
@section('header_title', 'Payment Transaction Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.payments.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Payments Ledger
        </a>

        @if($payment->status === 'approved')
            <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank" class="px-3.5 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition">
                <i class="fa-solid fa-print"></i> Print Official Receipt
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Transaction Information Card -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contribution Month</span>
                        <h3 class="text-xl font-bold font-cinzel text-amber-300">{{ $payment->formatted_month }}</h3>
                    </div>
                    <div>
                        @if($payment->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <i class="fa-solid fa-circle-check"></i> Approved
                            </span>
                        @elseif($payment->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse">
                                <i class="fa-solid fa-clock"></i> Pending Verification
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                <i class="fa-solid fa-ban"></i> Rejected
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Amount Paid:</span>
                        <span class="font-cinzel font-extrabold text-lg text-emerald-400">₹{{ number_format($payment->amount, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Member Name:</span>
                        <a href="{{ route('admin.members.show', $payment->member) }}" class="font-bold text-slate-200 hover:text-amber-400 transition">
                            {{ $payment->member->name }}
                        </a>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Member ID:</span>
                        <span class="font-mono text-amber-400 font-bold">{{ $payment->member->member_code }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Mobile Number:</span>
                        <span class="text-slate-200">{{ $payment->member->mobile }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Payment Date:</span>
                        <span class="text-slate-200">{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Payment Mode:</span>
                        <span class="capitalize text-slate-200">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">UTR / Transaction ID:</span>
                        <span class="font-mono font-bold text-amber-300">{{ $payment->transaction_id ?? '-' }}</span>
                    </div>

                    @if($payment->receipt_number)
                        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                            <span class="text-slate-400">Official Receipt No:</span>
                            <span class="font-mono font-bold text-emerald-400">{{ $payment->receipt_number }}</span>
                        </div>
                    @endif

                    @if($payment->verifier)
                        <div class="flex items-center justify-between text-[11px] text-slate-500">
                            <span>Verified By:</span>
                            <span>{{ $payment->verifier->name }} ({{ $payment->verified_at ? $payment->verified_at->format('d M Y, h:i A') : '' }})</span>
                        </div>
                    @endif

                    @if($payment->rejection_reason)
                        <div class="p-3 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 mt-2">
                            <strong class="block text-rose-400 mb-1">Rejection Reason:</strong>
                            <p>{{ $payment->rejection_reason }}</p>
                        </div>
                    @endif

                    @if($payment->notes)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-400 mt-2">
                            <strong class="block text-slate-300 mb-1">Admin Notes:</strong>
                            <p>{{ $payment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approve/Reject controls if pending -->
            @if($payment->status === 'pending')
                <div class="pt-4 mt-4 border-t border-slate-800 grid grid-cols-2 gap-2">
                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" onsubmit="const r = prompt('Reason for rejection:'); if(!r) return false; this.rejection_reason.value = r;">
                        @csrf
                        <input type="hidden" name="rejection_reason" value="">
                        <button type="submit" class="w-full py-2 bg-rose-600/20 hover:bg-rose-600 border border-rose-500/30 text-rose-300 hover:text-white rounded-xl text-xs font-semibold transition">
                            <i class="fa-solid fa-xmark"></i> Reject
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-md transition">
                            <i class="fa-solid fa-check"></i> Approve
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Payment Screenshot Preview Card -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
            <div>
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span>UPI Transfer Proof Screenshot</span>
                    @if($payment->screenshot_path)
                        <a href="{{ asset('storage/' . $payment->screenshot_path) }}" target="_blank" class="text-amber-400 hover:underline normal-case text-xs font-normal">
                            Open Full Tab &nearr;
                        </a>
                    @endif
                </h4>

                @if($payment->screenshot_path)
                    <div class="rounded-xl overflow-hidden bg-black/60 border border-slate-800 flex items-center justify-center p-2 min-h-[300px]">
                        <img src="{{ asset('storage/' . $payment->screenshot_path) }}" alt="Receipt Screenshot" class="max-h-[420px] max-w-full object-contain rounded-lg">
                    </div>
                @else
                    <div class="h-64 rounded-xl bg-slate-950 border border-slate-800 flex flex-col items-center justify-center text-slate-600 text-xs">
                        <i class="fa-solid fa-image text-3xl mb-2"></i>
                        <span>No image submitted (Recorded manually by admin)</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
