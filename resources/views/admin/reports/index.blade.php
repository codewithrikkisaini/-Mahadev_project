@extends('layouts.admin')

@section('title', 'Monthly Collection Reports')
@section('header_title', 'Monthly Seva Collection Reports')

@section('content')
<div class="space-y-6">
    <!-- Filter Bar & Export Buttons -->
    <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-lg sacred-glow">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-200">
                    Collection Report: <span class="text-amber-400 font-cinzel">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</span>
                </h3>
                <p class="text-xs text-slate-400">Total member contribution status & audit trail</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Period Selector -->
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>

                <select name="year" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Export to CSV Button -->
            <a
                href="{{ route('admin.reports.exportMonthlyCsv', ['year' => $year, 'month' => $month]) }}"
                class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition"
            >
                <i class="fa-solid fa-file-csv text-sm"></i> Download Monthly CSV
            </a>
        </div>
    </div>

    <!-- Monthly Summary KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Collected (Approved)</span>
            <div class="text-xl font-bold font-cinzel text-emerald-400 mt-1">₹{{ number_format($stats['collected_this_month'], 2) }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pending Balance</span>
            <div class="text-xl font-bold font-cinzel text-amber-400 mt-1">₹{{ number_format($stats['pending_this_month'], 2) }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-sky-500/30">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Expected Target</span>
            <div class="text-xl font-bold font-cinzel text-sky-400 mt-1">₹{{ number_format($stats['expected_collection'], 2) }}</div>
        </div>
    </div>

    <!-- Member Collection Status Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl overflow-hidden">
        <div class="p-4 bg-slate-950/60 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-xs font-semibold text-slate-300 uppercase tracking-wider">
                Member Payment Status for {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} ({{ $reportData->count() }} Members)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800 font-semibold">
                    <tr>
                        <th class="py-3 px-4">Member ID & Name</th>
                        <th class="py-3 px-4">Mobile Number</th>
                        <th class="py-3 px-4">Expected Fee</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Receipt / UTR</th>
                        <th class="py-3 px-4">Paid Date</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($reportData as $row)
                        @php
                            $member = $row['member'];
                            $payment = $row['payment'];
                            $st = $row['status'];
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.members.show', $member) }}" class="font-bold text-slate-200 hover:text-amber-400 transition">
                                    {{ $member->name }}
                                </a>
                                <div class="font-mono text-[11px] text-amber-400/90">{{ $member->member_code }}</div>
                            </td>

                            <td class="py-3 px-4 text-slate-300">
                                {{ $member->mobile }}
                            </td>

                            <td class="py-3 px-4 font-cinzel font-bold text-slate-200">
                                ₹{{ number_format($row['monthly_amount'], 2) }}
                            </td>

                            <td class="py-3 px-4">
                                @if($st === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Paid
                                    </span>
                                @elseif($st === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        <i class="fa-solid fa-clock text-[9px]"></i> In Review
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Unpaid
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 font-mono text-[11px]">
                                @if($payment && $payment->receipt_number)
                                    <span class="text-emerald-400 font-bold">{{ $payment->receipt_number }}</span>
                                @elseif($payment && $payment->transaction_id)
                                    <span class="text-amber-400">{{ $payment->transaction_id }}</span>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-slate-400">
                                {{ $payment && $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}
                            </td>

                            <td class="py-3 px-4 text-right">
                                @if($payment && $payment->status === 'approved')
                                    <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-emerald-400 hover:bg-slate-800 rounded-lg transition" title="Print Official Receipt">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.members.show', $member) }}" class="text-xs text-amber-400 hover:underline">
                                        View &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                No active member records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
