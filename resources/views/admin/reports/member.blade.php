@extends('layouts.admin')

@section('title', 'Member Contribution Matrix (' . $year . ')')
@section('header_title', 'Annual Member Matrix Grid')

@section('content')
<div class="space-y-6">
    <!-- Filter & Export Header -->
    <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-lg sacred-glow">
                <i class="fa-solid fa-table-cells"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-200">
                    Annual Contribution Grid: <span class="text-amber-400 font-cinzel">{{ $year }}</span>
                </h3>
                <p class="text-xs text-slate-400">12-Month cross-matrix of all member donations & seva status</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" action="{{ route('admin.reports.member') }}" class="flex flex-wrap items-center gap-2">
                <!-- Member Filter Dropdown -->
                <select name="member_id" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500 max-w-[200px]">
                    <option value="">All Members</option>
                    @foreach($allMembersList as $m)
                        <option value="{{ $m->id }}" {{ $selectedMemberId == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->member_code }})
                        </option>
                    @endforeach
                </select>

                <!-- Year Filter Dropdown -->
                <select name="year" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                @if($selectedMemberId)
                    <a href="{{ route('admin.reports.member', ['year' => $year]) }}" class="p-2 text-slate-400 hover:text-white bg-slate-800 rounded-xl text-xs" title="Reset Member Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>

            <!-- Export Matrix CSV Button -->
            <a
                href="{{ route('admin.reports.exportMemberMatrixCsv', ['year' => $year]) }}"
                class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition"
            >
                <i class="fa-solid fa-file-csv text-sm"></i> Export Matrix CSV
            </a>
        </div>
    </div>

    <!-- Matrix Legend -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-900/50 border border-slate-800 text-xs text-slate-400">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                <span class="w-3 h-3 rounded bg-emerald-500 flex items-center justify-center text-[8px] text-slate-950 font-bold">✓</span> Paid
            </span>
            <span class="flex items-center gap-1.5 text-amber-400 font-medium">
                <span class="w-3 h-3 rounded bg-amber-500 flex items-center justify-center text-[8px] text-slate-950 font-bold">~</span> In Review
            </span>
            <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                <span class="w-3 h-3 rounded bg-slate-800 border border-slate-700"></span> Unpaid / Due
            </span>
        </div>
        <div class="text-[11px] text-slate-500">
            Total active members in matrix: <strong class="text-slate-300">{{ $members->count() }}</strong>
        </div>
    </div>

    <!-- 12-Month Matrix Grid Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-center text-xs text-slate-300">
                <thead class="bg-slate-950/90 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4 text-left min-w-[200px]">Member</th>
                        @for($m = 1; $m <= 12; $m++)
                            <th class="py-3 px-2 min-w-[65px] {{ date('n') == $m && date('Y') == $year ? 'bg-amber-500/10 text-amber-300 border-x border-amber-500/20' : '' }}">
                                {{ \Carbon\Carbon::create($year, $m, 1)->format('M') }}
                            </th>
                        @endfor
                        <th class="py-3.5 px-4 text-right min-w-[100px]">Annual Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($members as $member)
                        @php
                            $memberPayments = ($payments->get($member->id) ?? collect())->keyBy('payment_month');
                            $totalMemberPaid = 0;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-left">
                                <a href="{{ route('admin.members.show', $member) }}" class="font-bold text-slate-200 hover:text-amber-400 transition block">
                                    {{ $member->name }}
                                </a>
                                <span class="font-mono text-[10px] text-amber-400/80">{{ $member->member_code }}</span>
                            </td>

                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $p = $memberPayments->get($m);
                                    if ($p && $p->status === 'approved') {
                                        $totalMemberPaid += $p->amount;
                                    }
                                @endphp
                                <td class="py-2.5 px-1.5 {{ date('n') == $m && date('Y') == $year ? 'bg-amber-500/5 border-x border-amber-500/20' : '' }}">
                                    @if($p && $p->status === 'approved')
                                        <a href="{{ route('admin.payments.receipt', $p) }}" target="_blank" class="block py-1 px-1 rounded bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 font-bold text-[11px] hover:bg-emerald-500 hover:text-white transition" title="Paid ₹{{ number_format($p->amount, 0) }} • Click for receipt">
                                            ₹{{ number_format($p->amount, 0) }}
                                        </a>
                                    @elseif($p && $p->status === 'pending')
                                        <span class="block py-1 px-1 rounded bg-amber-500/20 border border-amber-500/40 text-amber-400 font-bold text-[10px]" title="Pending Verification">
                                            In Rev
                                        </span>
                                    @else
                                        <span class="block py-1 px-1 text-slate-600 text-[11px]">
                                            -
                                        </span>
                                    @endif
                                </td>
                            @endfor

                            <td class="py-3 px-4 text-right font-cinzel font-bold text-emerald-400 text-sm">
                                ₹{{ number_format($totalMemberPaid, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="py-10 text-center text-slate-500">
                                No member records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
