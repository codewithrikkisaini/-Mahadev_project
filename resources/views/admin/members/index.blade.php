@extends('layouts.admin')

@section('title', 'Member Directory')
@section('header_title', 'Committee Members Management')

@section('content')
<!-- Header Stats & Search Row -->
<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-lg sacred-glow">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-200">
                Total Members: <span class="text-amber-400">{{ $totalMembers }}</span>
                <span class="text-xs font-normal text-slate-400 ml-2">({{ $activeMembers }} Active, {{ $inactiveMembers }} Inactive)</span>
            </h3>
            <p class="text-xs text-slate-400">Search by Name, Member ID, Mobile or Email</p>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
        <div class="relative flex-1 md:w-64">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search member..."
                class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl outline-none focus:border-amber-500"
            >
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-500 text-xs"></i>
        </div>

        <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
            <option value="">All Statuses</option>
            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
        </select>

        @if($search || $status)
            <a href="{{ route('admin.members.index') }}" class="p-2 text-slate-400 hover:text-white bg-slate-800 rounded-xl text-xs" title="Reset Filters">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        @endif

        <a href="{{ route('admin.members.create') }}" class="px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition whitespace-nowrap">
            <i class="fa-solid fa-user-plus text-xs"></i> Add Member
        </a>
    </form>
</div>

<!-- Member List Table Card -->
<div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800 font-semibold">
                <tr>
                    <th class="py-3.5 px-4">Member ID & Name</th>
                    <th class="py-3.5 px-4">Mobile & Email</th>
                    <th class="py-3.5 px-4">Monthly Fee</th>
                    <th class="py-3.5 px-4">Current Month</th>
                    <th class="py-3.5 px-4">Total Paid</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-medium">
                @forelse($members as $member)
                    @php
                        $monthStatus = $member->current_month_status;
                        $summary = $member->payment_summary;
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.members.show', $member) }}" class="font-bold text-slate-200 hover:text-amber-400 transition text-sm">
                                        {{ $member->name }}
                                    </a>
                                    <p class="font-mono text-amber-400/90 text-[11px]">{{ $member->member_code }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="text-slate-300">{{ $member->mobile }}</div>
                            <div class="text-[11px] text-slate-500 truncate max-w-[180px]">{{ $member->email ?? 'N/A' }}</div>
                        </td>

                        <td class="py-3.5 px-4">
                            <span class="font-bold text-amber-300 font-cinzel">₹{{ number_format($member->monthly_amount, 0) }}</span>
                            <span class="text-[10px] text-slate-500 block">/ month</span>
                        </td>

                        <td class="py-3.5 px-4">
                            @if($monthStatus === 'approved')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Paid
                                </span>
                            @elseif($monthStatus === 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                    <i class="fa-solid fa-clock text-[9px]"></i> In Review
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                    <i class="fa-solid fa-circle-xmark text-[9px]"></i> Due
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-200 font-cinzel">₹{{ number_format($member->total_paid, 0) }}</div>
                            <div class="text-[10px] text-slate-500">{{ $summary['paid_months'] }} months cleared</div>
                        </td>

                        <td class="py-3.5 px-4">
                            <form method="POST" action="{{ route('admin.members.toggleStatus', $member) }}" class="inline">
                                @csrf
                                <button type="submit" title="Click to toggle active status" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold transition {{ $member->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-rose-500/20 hover:text-rose-400' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30 hover:bg-emerald-500/20 hover:text-emerald-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $member->status === 'active' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                    <span class="capitalize">{{ $member->status }}</span>
                                </button>
                            </form>
                        </td>

                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.members.show', $member) }}" class="p-2 text-slate-400 hover:text-amber-400 hover:bg-slate-800 rounded-lg transition" title="View 360 Matrix & Ledger">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.members.edit', $member) }}" class="p-2 text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition" title="Edit Member">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-users-slash text-3xl mb-2"></i>
                            <p class="text-sm">No members found matching your search query.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
        <div class="p-4 border-t border-slate-800 bg-slate-950/50 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of {{ $members->total() }} members
            </div>
            <div>
                {{ $members->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
