@extends('layouts.member')

@section('title', 'My Payment History')

@section('content')
<div class="space-y-6">
    <!-- Header with Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-100 font-cinzel">My Seva Contributions & Receipts</h3>
                <p class="text-xs text-slate-400">View official receipts, verify pending submissions, and audit all past seva records.</p>
            </div>
        </div>

        <a
            href="{{ route('member.payments.create') }}"
            class="px-5 py-2.5 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:to-orange-500 text-white font-semibold rounded-xl text-xs shadow-lg shadow-orange-950/50 flex items-center gap-2 transition self-start sm:self-center sacred-glow"
        >
            <i class="fa-solid fa-qrcode"></i> Pay Monthly Seva (QR / UPI)
        </a>
    </div>

    <!-- Summary Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Paid</span>
            <div class="text-xl font-bold font-cinzel text-emerald-400 mt-1">₹{{ number_format($summary['total_paid'], 2) }}</div>
            <span class="text-[10px] text-slate-500 mt-0.5 block">{{ $summary['paid_months'] }} months cleared</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pending Dues</span>
            <div class="text-xl font-bold font-cinzel text-amber-400 mt-1">₹{{ number_format($summary['total_pending'], 2) }}</div>
            <span class="text-[10px] text-slate-500 mt-0.5 block">{{ $summary['total_pending_months'] }} months due / in review</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-sky-500/30">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Monthly Seva Amount</span>
            <div class="text-xl font-bold font-cinzel text-sky-400 mt-1">₹{{ number_format($member->monthly_amount, 2) }}</div>
            <span class="text-[10px] text-slate-500 mt-0.5 block">Contribution per month</span>
        </div>
    </div>

    <!-- Payment Ledger Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl overflow-hidden">
        <div class="p-4 bg-slate-950/60 border-b border-slate-800 flex items-center justify-between">
            <h4 class="text-xs font-semibold text-slate-300 uppercase tracking-wider">
                Contribution Records Ledger ({{ $payments->total() }} Submissions)
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Period</th>
                        <th class="py-3.5 px-4">Receipt No</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Payment Mode & UTR</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Official Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-200">
                                {{ $payment->formatted_month }}
                            </td>

                            <td class="py-3.5 px-4 font-mono font-bold text-amber-400 text-xs">
                                {{ $payment->receipt_number ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4 font-cinzel font-bold text-emerald-400 text-sm">
                                ₹{{ number_format($payment->amount, 2) }}
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="capitalize text-slate-300">{{ str_replace('_', ' ', $payment->payment_method) }}</div>
                                <div class="font-mono text-[11px] text-slate-500">{{ $payment->transaction_id ?? '-' }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-slate-400">
                                {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($payment->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Approved
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 animate-pulse">
                                        <i class="fa-solid fa-clock text-[9px]"></i> In Review
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30" title="{{ $payment->rejection_reason }}">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Rejected
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                @if($payment->status === 'approved')
                                    <a
                                        href="{{ route('member.payments.receipt', $payment) }}"
                                        target="_blank"
                                        class="px-3 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600 border border-emerald-500/30 text-emerald-300 hover:text-white transition font-semibold text-xs inline-flex items-center gap-1.5"
                                    >
                                        <i class="fa-solid fa-print"></i> Receipt
                                    </a>
                                @elseif($payment->status === 'rejected')
                                    <a
                                        href="{{ route('member.payments.create') }}"
                                        class="text-rose-400 hover:underline text-[11px] font-semibold"
                                    >
                                        Re-submit &rarr;
                                    </a>
                                @else
                                    <span class="text-slate-500 text-[11px]">Under Review</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-600"></i>
                                <p class="text-sm">You have not submitted any payments yet.</p>
                                <a href="{{ route('member.payments.create') }}" class="mt-2 inline-block text-amber-400 font-semibold text-xs hover:underline">Pay Seva Now &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/50 flex items-center justify-between">
                <div class="text-xs text-slate-400">
                    Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} records
                </div>
                <div>{{ $payments->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
