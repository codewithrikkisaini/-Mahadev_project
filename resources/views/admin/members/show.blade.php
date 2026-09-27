@extends('layouts.admin')

@section('title', $member->name . ' (' . $member->member_code . ')')
@section('header_title', 'Member 360 & Contribution Matrix')

@section('content')
<div class="space-y-6" x-data="{ manualModal: false }">
    <!-- Top Action Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.members.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Members Directory
        </a>

        <div class="flex items-center gap-2">
            @if($member->status === 'pending')
                <form method="POST" action="{{ route('admin.members.approve', $member) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-950/50 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-check-circle"></i>
                        <span>Approve Member</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.members.reject', $member) }}" onsubmit="return confirm('Reject registration for {{ $member->name }}?');" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-xmark"></i> Reject
                    </button>
                </form>
            @endif

            <!-- Record Manual Payment Button -->
            <button
                type="button"
                @click="manualModal = true"
                class="px-3.5 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition"
            >
                <i class="fa-solid fa-plus-circle"></i>
                <span>Record Cash / Manual Entry</span>
            </button>

            <a href="{{ route('admin.members.edit', $member) }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-pen"></i> Edit Member
            </a>

            <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Are you sure you want to permanently delete member {{ $member->name }} ({{ $member->member_code }})? All related payments will also be deleted.');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-950/40 hover:bg-rose-900/70 border border-rose-500/30 text-rose-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if($member->status === 'pending')
        <!-- Pending Approval Alert Notification -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-950/80 via-orange-950/50 to-slate-900 border border-amber-500/40 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center text-xl font-bold animate-pulse">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-300 flex items-center gap-2">
                        <span>New Member Registration Pending Your Approval</span>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500 text-slate-950 font-bold uppercase">Pending</span>
                    </h4>
                    <p class="text-xs text-slate-300 mt-0.5">This member registered online and cannot log in until you approve the registration.</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <form method="POST" action="{{ route('admin.members.approve', $member) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-circle-check"></i> Approve & Activate
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.members.reject', $member) }}" onsubmit="return confirm('Reject registration for {{ $member->name }}?');" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-rose-950/80 text-rose-300 border border-slate-700 hover:border-rose-500/40 rounded-xl text-xs font-semibold transition">
                        <i class="fa-solid fa-xmark"></i> Reject
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Member Profile Overview Card & Summary Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-500 p-0.5 sacred-glow">
                            <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-amber-400 font-bold text-xl">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-100">{{ $member->name }}</h3>
                            <p class="font-mono text-xs font-semibold text-amber-400">{{ $member->member_code }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.members.toggleStatus', $member) }}">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold capitalize transition {{ $member->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                            {{ $member->status }}
                        </button>
                    </form>
                </div>

                <div class="space-y-2.5 text-xs pt-3 border-t border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 flex items-center gap-1.5"><i class="fa-solid fa-phone text-slate-500"></i> Mobile:</span>
                        <span class="font-semibold text-slate-200">{{ $member->mobile }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 flex items-center gap-1.5"><i class="fa-solid fa-envelope text-slate-500"></i> Email:</span>
                        <span class="font-semibold text-slate-200 truncate max-w-[170px]">{{ $member->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 flex items-center gap-1.5"><i class="fa-solid fa-calendar text-slate-500"></i> Joining Date:</span>
                        <span class="font-semibold text-slate-200">{{ $member->joining_date ? $member->joining_date->format('d M Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 flex items-center gap-1.5"><i class="fa-solid fa-indian-rupee-sign text-slate-500"></i> Monthly Seva:</span>
                        <span class="font-bold text-amber-300 font-cinzel">₹{{ number_format($member->monthly_amount, 2) }}</span>
                    </div>
                    @if($member->address)
                        <div class="pt-2 border-t border-slate-800/60">
                            <span class="text-slate-400 block mb-0.5">Address:</span>
                            <span class="text-slate-300">{{ $member->address }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($member->notes)
                <div class="mt-4 p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400">
                    <strong class="text-slate-300">Note:</strong> {{ $member->notes }}
                </div>
            @endif
        </div>

        <!-- Financial Summary Counters (2 Cols) -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Lifetime Contributed -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-emerald-500/30 shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Paid</span>
                    <div class="text-2xl sm:text-3xl font-extrabold font-cinzel text-emerald-400 mt-2">
                        ₹{{ number_format($summary['total_paid'], 2) }}
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-400">
                    <span class="font-bold text-emerald-300">{{ $summary['paid_months'] }}</span> of {{ $summary['total_months'] }} months cleared
                </div>
            </div>

            <!-- Total Pending Dues -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-amber-500/30 shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pending Dues</span>
                    <div class="text-2xl sm:text-3xl font-extrabold font-cinzel text-amber-400 mt-2">
                        ₹{{ number_format($summary['total_pending'], 2) }}
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-400">
                    <span class="font-bold text-amber-300">{{ $summary['total_pending_months'] }}</span> months unpaid / pending
                </div>
            </div>

            <!-- Compliance Rate -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-sky-500/30 shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contribution Rate</span>
                    @php
                        $rate = $summary['total_months'] > 0 ? round(($summary['paid_months'] / $summary['total_months']) * 100) : 100;
                    @endphp
                    <div class="text-2xl sm:text-3xl font-extrabold font-cinzel text-sky-300 mt-2">
                        {{ $rate }}%
                    </div>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-sky-500 h-1.5 rounded-full" style="width: {{ $rate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month-by-Month Contribution Matrix -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-table-cells text-amber-400"></i>
                    <span>All-Time Months Contribution Matrix (Since Joining)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Click any cleared month to view receipt or pending month to record payment</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-emerald-400"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span> Paid</span>
                <span class="flex items-center gap-1.5 text-amber-400"><span class="w-2.5 h-2.5 rounded bg-amber-500"></span> In Review</span>
                <span class="flex items-center gap-1.5 text-rose-400"><span class="w-2.5 h-2.5 rounded bg-rose-500"></span> Due</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($dueMonths as $dm)
                @php
                    $key = "{$dm['year']}-{$dm['month']}";
                    $payment = $paymentsByMonth->get($key);
                @endphp
                <div class="p-3 rounded-xl border transition flex flex-col justify-between {{ $payment && $payment->status === 'approved' ? 'bg-emerald-950/30 border-emerald-500/40 text-emerald-300' : ($payment && $payment->status === 'pending' ? 'bg-amber-950/30 border-amber-500/40 text-amber-300' : 'bg-rose-950/20 border-rose-500/30 text-rose-300') }}">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-xs">{{ $dm['short_label'] }}</span>
                        @if($payment && $payment->status === 'approved')
                            <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                        @elseif($payment && $payment->status === 'pending')
                            <i class="fa-solid fa-clock text-amber-400 text-xs"></i>
                        @else
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-xs"></i>
                        @endif
                    </div>

                    <div class="my-2">
                        @if($payment && $payment->status === 'approved')
                            <div class="font-cinzel font-bold text-sm text-emerald-400">₹{{ number_format($payment->amount, 0) }}</div>
                            <div class="font-mono text-[10px] text-slate-400 truncate">{{ $payment->receipt_number }}</div>
                        @elseif($payment && $payment->status === 'pending')
                            <div class="font-cinzel font-bold text-sm text-amber-400">₹{{ number_format($payment->amount, 0) }}</div>
                            <div class="text-[10px] text-amber-400/80 font-medium">Pending Review</div>
                        @else
                            <div class="font-cinzel font-bold text-sm text-rose-400">₹{{ number_format($member->monthly_amount, 0) }}</div>
                            <div class="text-[10px] text-rose-400/80 font-medium">Unpaid / Due</div>
                        @endif
                    </div>

                    <div class="pt-1.5 border-t border-slate-800 flex items-center justify-between text-[10px]">
                        @if($payment && $payment->status === 'approved')
                            <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1 font-semibold">
                                <i class="fa-solid fa-receipt"></i> Receipt
                            </a>
                        @elseif(!$payment)
                            <button
                                type="button"
                                @click="manualModal = true; document.getElementById('modal_month').value = {{ $dm['month'] }}; document.getElementById('modal_year').value = {{ $dm['year'] }};"
                                class="text-amber-400 hover:underline font-semibold"
                            >
                                + Record Pay
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Complete Payment History Ledger for this Member -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl overflow-hidden">
        <div class="p-4 bg-slate-950/60 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-amber-400"></i>
                <span>Payment History Ledger ({{ $member->payments->count() }} Entries)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800 font-semibold">
                    <tr>
                        <th class="py-3 px-4">Period</th>
                        <th class="py-3 px-4">Receipt No</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Method & UTR</th>
                        <th class="py-3 px-4">Payment Date</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($member->payments as $p)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 font-bold text-slate-200">
                                {{ $p->formatted_month }}
                            </td>
                            <td class="py-3 px-4 font-mono text-amber-400 text-xs">
                                {{ $p->receipt_number ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-cinzel font-bold text-slate-100">
                                ₹{{ number_format($p->amount, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="capitalize text-slate-300">{{ str_replace('_', ' ', $p->payment_method) }}</span>
                                <div class="font-mono text-[11px] text-slate-500">{{ $p->transaction_id ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-400">
                                {{ $p->payment_date ? $p->payment_date->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($p->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        Approved
                                    </span>
                                @elseif($p->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($p->status === 'approved')
                                    <a href="{{ route('admin.payments.receipt', $p) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-amber-400 hover:bg-slate-800 rounded-lg transition" title="View / Print Receipt">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 text-xs">
                                No payment records logged for this member yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Record Manual Payment Modal -->
    <div
        x-show="manualModal"
        @click="manualModal = false"
        x-transition
        class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="w-full max-w-lg bg-slate-900 border border-amber-500/30 rounded-2xl p-6 shadow-2xl relative" @click.stop>
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-100">Record Manual Payment</h4>
                        <p class="text-[11px] text-slate-400">For {{ $member->name }} ({{ $member->member_code }})</p>
                    </div>
                </div>
                <button @click="manualModal = false" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.members.manualPayment', $member) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="modal_month" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Month *
                        </label>
                        <select id="modal_month" name="payment_month" required class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="modal_year" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Year *
                        </label>
                        <select id="modal_year" name="payment_year" required class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="modal_amount" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Amount (₹) *
                        </label>
                        <input
                            type="number"
                            id="modal_amount"
                            name="amount"
                            value="{{ $member->monthly_amount }}"
                            required
                            min="1"
                            step="1"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-amber-400 font-cinzel font-bold text-xs outline-none focus:border-amber-500"
                        >
                    </div>

                    <div>
                        <label for="modal_date" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Payment Date *
                        </label>
                        <input
                            type="date"
                            id="modal_date"
                            name="payment_date"
                            value="{{ date('Y-m-d') }}"
                            required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="modal_method" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Payment Mode *
                        </label>
                        <select id="modal_method" name="payment_method" required class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500">
                            <option value="cash">Cash in Mandir Office</option>
                            <option value="upi_qr">Direct UPI / QR Transfer</option>
                            <option value="bank_transfer">Direct Bank NEFT / IMPS</option>
                        </select>
                    </div>

                    <div>
                        <label for="modal_utr" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Receipt / UTR Ref
                        </label>
                        <input
                            type="text"
                            id="modal_utr"
                            name="transaction_id"
                            placeholder="Optional UTR / Ref"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                        >
                    </div>
                </div>

                <div>
                    <label for="modal_notes" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        Admin Note
                    </label>
                    <input
                        type="text"
                        id="modal_notes"
                        name="notes"
                        placeholder="e.g. Received cash at Aarti time by Shastri ji"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                    >
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="manualModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Record & Generate Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
