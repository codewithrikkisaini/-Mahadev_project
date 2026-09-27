@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Committee Financial Overview')

@section('content')
<!-- Filter & Action Bar -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-lg sacred-glow">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-200">Financial Period: {{ $stats['current_month_name'] }}</h3>
            <p class="text-xs text-slate-400">Real-time member contribution ledger & status</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
        <!-- Month & Year Filter Form -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 flex-1 sm:flex-initial">
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

        <!-- Send Payment Reminders Button -->
        <form method="POST" action="{{ route('admin.dashboard.sendReminders') }}" onsubmit="return confirm('Send WhatsApp/In-App monthly contribution reminders to all unpaid active members for {{ $stats['current_month_name'] }}?');">
            @csrf
            <button type="submit" class="px-3.5 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition">
                <i class="fa-solid fa-bell text-xs"></i>
                <span>Send Reminders</span>
            </button>
        </form>
    </div>
</div>

<!-- 4 Top KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Collected This Month -->
    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-emerald-500/30 relative overflow-hidden shadow-xl">
        <div class="absolute -right-4 -bottom-4 text-emerald-500/10 text-8xl pointer-events-none">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Collected ({{ \Carbon\Carbon::create($year, $month, 1)->format('M') }})</span>
            <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold font-cinzel text-emerald-400 tracking-tight">
            ₹{{ number_format($stats['collected_this_month'], 2) }}
        </div>
        <div class="mt-3 flex items-center justify-between text-xs">
            <span class="text-slate-400">Target: ₹{{ number_format($stats['expected_collection'], 0) }}</span>
            @php
                $collectedPct = $stats['expected_collection'] > 0 ? min(100, round(($stats['collected_this_month'] / $stats['expected_collection']) * 100)) : 0;
            @endphp
            <span class="font-bold text-emerald-300">{{ $collectedPct }}% Paid</span>
        </div>
        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-1.5 rounded-full" style="width: {{ $collectedPct }}%"></div>
        </div>
    </div>

    <!-- Pending Dues This Month -->
    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-amber-500/30 relative overflow-hidden shadow-xl">
        <div class="absolute -right-4 -bottom-4 text-amber-500/10 text-8xl pointer-events-none">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pending Dues</span>
            <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-clock"></i>
            </span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold font-cinzel text-amber-400 tracking-tight">
            ₹{{ number_format($stats['pending_this_month'], 2) }}
        </div>
        <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
            <span>Pending collection</span>
            <a href="{{ route('admin.reports.index', ['year' => $year, 'month' => $month]) }}" class="text-amber-400 hover:underline">View List &rarr;</a>
        </div>
        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-1.5 rounded-full" style="width: {{ 100 - $collectedPct }}%"></div>
        </div>
    </div>

    <!-- Active Members Count -->
    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-sky-500/30 relative overflow-hidden shadow-xl">
        <div class="absolute -right-4 -bottom-4 text-sky-500/10 text-8xl pointer-events-none">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Members</span>
            <span class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-id-card"></i>
            </span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold font-cinzel text-sky-300 tracking-tight">
            {{ $stats['active_members'] }} <span class="text-sm font-normal text-slate-400">/ {{ $stats['total_members'] }} Total</span>
        </div>
        <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
            <span>Fee per member: ₹{{ number_format(\App\Models\Setting::getMonthlyAmount(), 0) }}/mo</span>
            <a href="{{ route('admin.members.index') }}" class="text-sky-400 hover:underline">Manage &rarr;</a>
        </div>
        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
            <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ $stats['total_members'] > 0 ? round(($stats['active_members'] / $stats['total_members']) * 100) : 0 }}%"></div>
        </div>
    </div>

    <!-- Verification Queue -->
    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-900/60 border border-rose-500/30 relative overflow-hidden shadow-xl">
        <div class="absolute -right-4 -bottom-4 text-rose-500/10 text-8xl pointer-events-none">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Verification Queue</span>
            <span class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-file-invoice"></i>
            </span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold font-cinzel text-rose-400 tracking-tight flex items-center gap-2">
            <span>{{ $stats['pending_verifications'] }}</span>
            @if($stats['pending_verifications'] > 0)
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-600 text-white animate-pulse">Action Required</span>
            @endif
        </div>
        <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
            <span>Pending UPI receipts</span>
            <a href="{{ route('admin.payments.pending') }}" class="text-rose-400 hover:underline font-semibold">Review Queue &rarr;</a>
        </div>
        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
            <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $stats['pending_verifications'] > 0 ? '100%' : '0%' }}"></div>
        </div>
    </div>
</div>

<!-- Monthly Trend Chart & All-Time Summary Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart (2 Cols) -->
    <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-amber-400"></i>
                    <span>{{ $year }} Monthly Collection vs Target</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Track month-by-month financial progress</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Collected
                </span>
                <span class="flex items-center gap-1.5 text-amber-400 font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500/40"></span> Target
                </span>
            </div>
        </div>
        <div class="h-64 w-full">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>

    <!-- All-Time Lifetime Fund Card -->
    <div class="p-6 rounded-2xl bg-gradient-to-b from-slate-900/90 via-slate-900/70 to-slate-950 border border-amber-500/20 backdrop-blur-md shadow-xl flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2.5 text-amber-400 mb-2">
                <i class="fa-solid fa-vault text-lg"></i>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300">Total Lifetime Seva Fund</h4>
            </div>
            <div class="text-3xl font-extrabold font-cinzel text-amber-300 sacred-text-glow">
                ₹{{ number_format($stats['total_all_time'], 2) }}
            </div>
            <p class="text-xs text-slate-400 mt-1">Total approved contributions in committee ledger</p>
        </div>

        <div class="space-y-3 my-4 py-4 border-y border-slate-800">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">Active Committee Members:</span>
                <span class="font-bold text-slate-200">{{ $stats['active_members'] }} Members</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">Default Monthly Contribution:</span>
                <span class="font-bold text-amber-400">₹{{ number_format(\App\Models\Setting::getMonthlyAmount(), 2) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">Total Monthly Potential:</span>
                <span class="font-bold text-emerald-400">₹{{ number_format($stats['expected_collection'], 2) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('admin.reports.index') }}" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-center text-xs font-semibold text-slate-200 transition">
                <i class="fa-solid fa-file-csv mr-1 text-emerald-400"></i> Export CSV
            </a>
            <a href="{{ route('admin.members.create') }}" class="p-2.5 rounded-xl bg-amber-600/30 hover:bg-amber-600/40 border border-amber-500/40 text-center text-xs font-semibold text-amber-300 transition">
                <i class="fa-solid fa-user-plus mr-1"></i> Add Member
            </a>
        </div>
    </div>
</div>

<!-- Pending Approvals & Recent Activities Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="{ modalOpen: false, modalImg: '', approveId: null, rejectId: null, rejectReason: '' }">
    <!-- Pending Verification Table -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-200">Pending Verification Submissions</h3>
                </div>
                <a href="{{ route('admin.payments.pending') }}" class="text-xs text-amber-400 hover:underline">
                    View All ({{ $pendingPayments->count() }}) &rarr;
                </a>
            </div>

            @if($pendingPayments->isEmpty())
                <div class="py-10 text-center text-slate-500">
                    <i class="fa-solid fa-circle-check text-3xl text-emerald-500/40 mb-2"></i>
                    <p class="text-xs">All payment submissions have been verified!</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendingPayments->take(5) as $payment)
                        <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 hover:border-slate-700 transition flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                @if($payment->screenshot_path)
                                    <button
                                        type="button"
                                        @click="modalImg = '{{ asset('storage/' . $payment->screenshot_path) }}'; modalOpen = true"
                                        class="w-10 h-10 rounded-lg overflow-hidden bg-slate-900 border border-slate-700 flex-shrink-0 relative group"
                                        title="Click to zoom screenshot"
                                    >
                                        <img src="{{ asset('storage/' . $payment->screenshot_path) }}" alt="Receipt" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                            <i class="fa-solid fa-magnifying-glass-plus text-xs text-white"></i>
                                        </div>
                                    </button>
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs font-bold text-slate-200">
                                        {{ $payment->member->name }}
                                        <span class="text-[10px] text-amber-400 font-normal ml-1">({{ $payment->member->member_code }})</span>
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $payment->formatted_month }} • UTR: <span class="font-mono text-slate-300">{{ $payment->transaction_id }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="text-right mr-1">
                                    <div class="text-xs font-bold text-amber-300 font-cinzel">₹{{ number_format($payment->amount, 0) }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $payment->payment_date ? $payment->payment_date->format('d M') : '' }}</div>
                                </div>

                                <!-- Approve Form -->
                                <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Quick Approve & Generate Receipt" class="p-2 bg-emerald-600/20 hover:bg-emerald-600 border border-emerald-500/30 text-emerald-400 hover:text-white rounded-lg text-xs transition">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>

                                <!-- Reject Button Modal trigger -->
                                <button
                                    type="button"
                                    @click="rejectId = '{{ $payment->id }}'; $dispatch('open-reject-modal', { id: {{ $payment->id }}, name: '{{ $payment->member->name }}', month: '{{ $payment->formatted_month }}' })"
                                    title="Reject Payment"
                                    class="p-2 bg-rose-600/20 hover:bg-rose-600 border border-rose-500/30 text-rose-400 hover:text-white rounded-lg text-xs transition"
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Approved Payments Ledger Preview -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>Recently Approved Contributions</span>
                </h3>
                <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="text-xs text-amber-400 hover:underline">
                    All Ledger &rarr;
                </a>
            </div>

            @if($recentPayments->isEmpty())
                <div class="py-10 text-center text-slate-500">
                    <i class="fa-solid fa-receipt text-3xl text-slate-600 mb-2"></i>
                    <p class="text-xs">No approved payments recorded yet.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentPayments as $payment)
                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-200">{{ $payment->member->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $payment->formatted_month }} • <span class="font-mono text-emerald-400">{{ $payment->receipt_number }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold font-cinzel text-emerald-300">₹{{ number_format($payment->amount, 0) }}</span>
                                <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-amber-400 hover:bg-slate-800 rounded-lg transition" title="Print Official Receipt">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Screenshot Zoom Modal -->
    <div
        x-show="modalOpen"
        @click="modalOpen = false"
        x-transition
        class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="max-w-2xl w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 overflow-hidden" @click.stop>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-slate-200">UPI Payment Screenshot Verification</h4>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="max-h-[75vh] overflow-auto rounded-xl bg-black flex items-center justify-center">
                <img :src="modalImg" alt="Screenshot Full" class="max-w-full max-h-[70vh] object-contain">
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal (Alpine Dispatch) -->
<div
    x-data="{ showModal: false, paymentId: null, memberName: '', monthLabel: '' }"
    x-on:open-reject-modal.window="paymentId = $event.detail.id; memberName = $event.detail.name; monthLabel = $event.detail.month; showModal = true"
    x-show="showModal"
    class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm"
    style="display: none;"
>
    <div class="w-full max-w-md bg-slate-900 border border-rose-500/30 rounded-2xl p-6 shadow-2xl relative" @click.outside="showModal = false">
        <h4 class="text-base font-bold text-rose-400 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Reject Payment Submission</span>
        </h4>
        <p class="text-xs text-slate-400 mb-4">
            Rejecting submission for <strong class="text-slate-200" x-text="memberName"></strong> (<span x-text="monthLabel"></span>). A notification will be sent to the member with your reason.
        </p>

        <form :action="'/admin/payments/' + paymentId + '/reject'" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_reason" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Reason for Rejection *
                </label>
                <textarea
                    id="rejection_reason"
                    name="rejection_reason"
                    required
                    rows="3"
                    placeholder="e.g. Invalid UTR / Incorrect amount paid / Screenshot unreadable"
                    class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs focus:border-rose-500 outline-none"
                ></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-lg shadow-rose-950/50">
                    <i class="fa-solid fa-ban"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trendData = @json($stats['monthly_trends']);
        const labels = trendData.map(d => d.month_name);
        const collectedData = trendData.map(d => d.collected);
        const expectedData = trendData.map(d => d.expected);

        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Collected (₹)',
                        data: collectedData,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Target (₹)',
                        data: expectedData,
                        type: 'line',
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#f59e0b',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f1f5f9',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₹' + context.raw.toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 11 },
                            callback: function(value) {
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
