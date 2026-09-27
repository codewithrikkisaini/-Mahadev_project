@extends('layouts.member')

@section('title', 'Member Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Devotional Hero Welcome Banner -->
    <div class="relative rounded-3xl bg-gradient-to-r from-amber-950/80 via-slate-900/90 to-orange-950/80 border border-amber-500/30 p-6 sm:p-8 shadow-2xl overflow-hidden sacred-glow">
        <div class="absolute -right-10 -bottom-10 text-amber-500/10 text-9xl pointer-events-none font-sacred">
            ॐ
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-500 p-0.5 sacred-glow flex-shrink-0">
                    <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-amber-400 font-sacred text-2xl font-bold">
                        ॐ
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs uppercase font-semibold tracking-wider text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">
                            हर हर महादेव 🙏
                        </span>
                        <span class="text-xs text-slate-400">Welcome, {{ $member->name }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold font-cinzel text-amber-300 sacred-text-glow mt-1">
                        {{ $templeName }}
                    </h2>
                    <p class="text-xs text-amber-200/70 font-medium tracking-wide">
                        Member ID: <span class="font-mono text-amber-300 font-bold">{{ $member->member_code }}</span> • {{ $committeeName }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('member.payments.create') }}"
                    class="px-5 py-3 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:to-orange-500 text-white font-semibold rounded-2xl text-xs sm:text-sm shadow-xl shadow-orange-950/50 flex items-center gap-2 transition transform active:scale-95 sacred-glow whitespace-nowrap"
                >
                    <i class="fa-solid fa-qrcode text-base"></i> Pay Monthly Seva (₹{{ number_format($monthlyAmount, 0) }})
                </a>
            </div>
        </div>
    </div>

    <!-- Current Month Status Hero Card -->
    <div class="rounded-3xl p-6 sm:p-7 border shadow-xl relative overflow-hidden transition {{ $currentMonthStatus === 'approved' ? 'bg-gradient-to-br from-emerald-950/60 via-slate-900/90 to-slate-900/80 border-emerald-500/40' : ($currentMonthStatus === 'pending' ? 'bg-gradient-to-br from-amber-950/60 via-slate-900/90 to-slate-900/80 border-amber-500/40' : 'bg-gradient-to-br from-rose-950/60 via-slate-900/90 to-slate-900/80 border-rose-500/40') }}">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 {{ $currentMonthStatus === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : ($currentMonthStatus === 'pending' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/40 animate-pulse' : 'bg-rose-500/20 text-rose-400 border border-rose-500/40') }}">
                    @if($currentMonthStatus === 'approved')
                        <i class="fa-solid fa-circle-check"></i>
                    @elseif($currentMonthStatus === 'pending')
                        <i class="fa-solid fa-hourglass-half"></i>
                    @else
                        <i class="fa-solid fa-bell"></i>
                    @endif
                </div>

                <div>
                    <span class="text-xs uppercase font-semibold tracking-wider text-slate-400">
                        Monthly Contribution Status for <strong class="text-slate-200">{{ $currentMonthName }}</strong>
                    </span>
                    <div class="flex items-center gap-2 mt-0.5">
                        @if($currentMonthStatus === 'approved')
                            <h3 class="text-lg sm:text-xl font-bold font-cinzel text-emerald-400">
                                Seva Completed for {{ $currentMonthName }} 🙏
                            </h3>
                        @elseif($currentMonthStatus === 'pending')
                            <h3 class="text-lg sm:text-xl font-bold font-cinzel text-amber-400">
                                Payment Under Verification
                            </h3>
                        @else
                            <h3 class="text-lg sm:text-xl font-bold font-cinzel text-rose-400">
                                Contribution Due: ₹{{ number_format($monthlyAmount, 0) }}
                            </h3>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-1">
                        @if($currentMonthStatus === 'approved')
                            Thank you for your pious monthly contribution to the temple.
                        @elseif($currentMonthStatus === 'pending')
                            Your UPI submission is being cross-checked by committee admin. Receipt will generate once approved.
                        @else
                            Due before {{ $nextDueDate }}. Pay effortlessly via UPI QR scan.
                        @endif
                    </p>
                </div>
            </div>

            <div class="self-start sm:self-center">
                @if($currentMonthStatus === 'approved')
                    @php
                        $curPayment = $member->approvedPayments()->where('payment_month', date('n'))->where('payment_year', date('Y'))->first();
                    @endphp
                    @if($curPayment)
                        <a
                            href="{{ route('member.payments.receipt', $curPayment) }}"
                            target="_blank"
                            class="px-4 py-2.5 rounded-xl bg-emerald-600/30 hover:bg-emerald-600/50 border border-emerald-500/40 text-emerald-300 font-semibold text-xs flex items-center gap-2 transition"
                        >
                            <i class="fa-solid fa-receipt"></i> View Official Receipt
                        </a>
                    @endif
                @elseif($currentMonthStatus === 'pending')
                    <a
                        href="{{ route('member.payments.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-amber-600/30 hover:bg-amber-600/50 border border-amber-500/40 text-amber-300 font-semibold text-xs flex items-center gap-2 transition"
                    >
                        <i class="fa-solid fa-clock"></i> Track Verification
                    </a>
                @else
                    <a
                        href="{{ route('member.payments.create') }}"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-semibold text-xs shadow-lg shadow-orange-950/50 flex items-center gap-2 transition"
                    >
                        <i class="fa-solid fa-qrcode"></i> Pay Now (₹{{ number_format($monthlyAmount, 0) }})
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 3 Member Stats Summary KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- Total Paid -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-emerald-500/30 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Seva Paid</span>
                <div class="text-2xl font-bold font-cinzel text-emerald-400 mt-1">
                    ₹{{ number_format($summary['total_paid'], 2) }}
                </div>
                <span class="text-[11px] text-slate-500 mt-1 block">{{ $summary['paid_months'] }} months cleared</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
        </div>

        <!-- Pending Dues -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-amber-500/30 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pending Dues</span>
                <div class="text-2xl font-bold font-cinzel text-amber-400 mt-1">
                    ₹{{ number_format($summary['total_pending'], 2) }}
                </div>
                <span class="text-[11px] text-slate-500 mt-1 block">{{ $summary['total_pending_months'] }} months pending</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Monthly Amount -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-sky-500/30 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Monthly Seva Rate</span>
                <div class="text-2xl font-bold font-cinzel text-sky-400 mt-1">
                    ₹{{ number_format($monthlyAmount, 2) }}
                </div>
                <span class="text-[11px] text-slate-500 mt-1 block">Fixed monthly seva</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-500/15 border border-sky-500/30 text-sky-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-id-card"></i>
            </div>
        </div>
    </div>

    <!-- Recent Payments & Notices Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments History -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-amber-400"></i>
                        <span>Recent Contributions</span>
                    </h3>
                    <a href="{{ route('member.payments.index') }}" class="text-xs text-amber-400 hover:underline">
                        All History &rarr;
                    </a>
                </div>

                @if($recentPayments->isEmpty())
                    <div class="py-10 text-center text-slate-500">
                        <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-600"></i>
                        <p class="text-xs">No payments recorded yet.</p>
                        <a href="{{ route('member.payments.create') }}" class="mt-2 inline-block text-amber-400 font-semibold text-xs hover:underline">Make your first Seva Contribution &rarr;</a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentPayments as $p)
                            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $p->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($p->status === 'pending' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30') }}">
                                        @if($p->status === 'approved')
                                            ✓
                                        @elseif($p->status === 'pending')
                                            ~
                                        @else
                                            ✕
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-200">{{ $p->formatted_month }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono">{{ $p->receipt_number ?? ($p->transaction_id ?? 'Recorded') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="font-bold font-cinzel text-slate-100 text-sm">₹{{ number_format($p->amount, 0) }}</span>
                                    @if($p->status === 'approved')
                                        <a href="{{ route('member.payments.receipt', $p) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-emerald-400 hover:bg-slate-800 rounded-lg transition" title="Print Receipt">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Mandir Notices & Announcements -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-amber-400"></i>
                        <span>Mandir Notice Board</span>
                    </h3>
                    <a href="{{ route('member.announcements') }}" class="text-xs text-amber-400 hover:underline">
                        View All &rarr;
                    </a>
                </div>

                @if($announcements->isEmpty())
                    <div class="py-10 text-center text-slate-500">
                        <i class="fa-solid fa-om text-3xl mb-2 text-slate-600"></i>
                        <p class="text-xs">No active notices right now.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($announcements as $ann)
                            <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-bold text-slate-200 text-xs">{{ $ann->title }}</h4>
                                    <span class="text-[10px] text-slate-500">{{ $ann->publish_date ? $ann->publish_date->format('d M') : '' }}</span>
                                </div>
                                <p class="text-slate-400 text-[11px] line-clamp-2 leading-relaxed">
                                    {{ $ann->message }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
