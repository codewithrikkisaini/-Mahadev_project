@extends('layouts.admin')

@section('title', 'Payments Ledger')
@section('header_title', 'All Contributions & Payment Ledger')

@section('content')
<!-- KPI Row for Payments -->
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Approved Fund</span>
        <div class="text-xl font-bold font-cinzel text-emerald-400 mt-1">₹{{ number_format($totalApprovedAmount, 2) }}</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pending Verification</span>
        <div class="text-xl font-bold font-cinzel text-amber-400 mt-1">{{ $pendingCount }} Requests</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-sky-500/30">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Approved Entries</span>
        <div class="text-xl font-bold font-cinzel text-sky-400 mt-1">{{ $approvedCount }} Paid</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-rose-500/30">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Rejected Entries</span>
        <div class="text-xl font-bold font-cinzel text-rose-400 mt-1">{{ $rejectedCount }} Rejected</div>
    </div>
</div>

<!-- Filter and Search Row -->
<div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-center gap-2.5">
        <div class="relative flex-1 min-w-[200px]">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search UTR, Receipt No, Member..."
                class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl outline-none focus:border-amber-500"
            >
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-500 text-xs"></i>
        </div>

        <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
            <option value="">All Statuses</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <select name="month" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
            <option value="">All Months</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                </option>
            @endfor
        </select>

        <select name="year" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
            <option value="">All Years</option>
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        @if($search || $status || $month || $year)
            <a href="{{ route('admin.payments.index') }}" class="p-2 text-slate-400 hover:text-white bg-slate-800 rounded-xl text-xs" title="Clear Filters">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        @endif
    </form>
</div>

<!-- Payments Ledger Table Card -->
<div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl overflow-hidden" x-data="{ modalOpen: false, modalImg: '' }">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800 font-semibold">
                <tr>
                    <th class="py-3.5 px-4">Receipt & Date</th>
                    <th class="py-3.5 px-4">Member Name & ID</th>
                    <th class="py-3.5 px-4">Period</th>
                    <th class="py-3.5 px-4">Amount</th>
                    <th class="py-3.5 px-4">Method & UTR</th>
                    <th class="py-3.5 px-4">Screenshot</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-medium">
                @forelse($payments as $payment)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4">
                            <div class="font-mono font-bold text-amber-400">{{ $payment->receipt_number ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</div>
                        </td>

                        <td class="py-3.5 px-4">
                            <a href="{{ route('admin.members.show', $payment->member) }}" class="font-bold text-slate-200 hover:text-amber-400 transition">
                                {{ $payment->member->name }}
                            </a>
                            <div class="font-mono text-[11px] text-slate-500">{{ $payment->member->member_code }}</div>
                        </td>

                        <td class="py-3.5 px-4 font-bold text-slate-300">
                            {{ $payment->formatted_month }}
                        </td>

                        <td class="py-3.5 px-4 font-cinzel font-bold text-slate-100 text-sm">
                            ₹{{ number_format($payment->amount, 2) }}
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="capitalize text-slate-300">{{ str_replace('_', ' ', $payment->payment_method) }}</div>
                            <div class="font-mono text-[11px] text-slate-500 truncate max-w-[140px]">{{ $payment->transaction_id ?? '-' }}</div>
                        </td>

                        <td class="py-3.5 px-4">
                            @if($payment->screenshot_path)
                                <button
                                    type="button"
                                    @click="modalImg = '{{ asset('storage/' . $payment->screenshot_path) }}'; modalOpen = true"
                                    class="w-9 h-9 rounded-lg overflow-hidden bg-slate-950 border border-slate-700 flex items-center justify-center hover:border-amber-500 transition group"
                                    title="View Screenshot"
                                >
                                    <img src="{{ asset('storage/' . $payment->screenshot_path) }}" alt="Slip" class="w-full h-full object-cover">
                                </button>
                            @else
                                <span class="text-slate-600 text-[11px]">N/A</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4">
                            @if($payment->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Approved
                                </span>
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 animate-pulse">
                                    <i class="fa-solid fa-clock text-[9px]"></i> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30" title="{{ $payment->rejection_reason }}">
                                    <i class="fa-solid fa-circle-xmark text-[9px]"></i> Rejected
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="p-2 text-slate-400 hover:text-amber-400 hover:bg-slate-800 rounded-lg transition" title="Inspect Payment">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if($payment->status === 'approved')
                                    <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank" class="p-2 text-slate-400 hover:text-emerald-400 hover:bg-slate-800 rounded-lg transition" title="Print Receipt">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-receipt text-3xl mb-2"></i>
                            <p class="text-sm">No payment records found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div class="p-4 border-t border-slate-800 bg-slate-950/50 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries
            </div>
            <div>
                {{ $payments->links() }}
            </div>
        </div>
    @endif

    <!-- Screenshot Modal -->
    <div
        x-show="modalOpen"
        @click="modalOpen = false"
        x-transition
        class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="max-w-2xl w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 overflow-hidden" @click.stop>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-slate-200">UPI Payment Screenshot</h4>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="max-h-[75vh] overflow-auto rounded-xl bg-black flex items-center justify-center">
                <img :src="modalImg" alt="Screenshot" class="max-w-full max-h-[70vh] object-contain">
            </div>
        </div>
    </div>
</div>
@endsection
