@extends('layouts.member')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-950/60 via-slate-900/90 to-slate-900/80 border border-amber-500/30 backdrop-blur-md flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-xl sacred-glow">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-500 p-0.5 sacred-glow flex-shrink-0">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-amber-400 font-bold text-2xl">
                    {{ strtoupper(substr($member->name, 0, 2)) }}
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-100">{{ $member->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize {{ $member->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                        {{ $member->status }}
                    </span>
                </div>
                <p class="font-mono text-xs font-semibold text-amber-400 mt-0.5">
                    Member ID: {{ $member->member_code }}
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    Joined on {{ $member->joining_date ? $member->joining_date->format('d M Y') : 'N/A' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="p-3 bg-slate-950/70 border border-slate-800 rounded-2xl text-right">
                <span class="text-[10px] uppercase font-semibold text-slate-400 block">Monthly Contribution</span>
                <span class="text-lg font-bold font-cinzel text-amber-400">₹{{ number_format($member->monthly_amount, 0) }}</span>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Profile Update Form | Financial Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Update Form (2 Cols) -->
        <div class="lg:col-span-2 p-6 sm:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 backdrop-blur-xl shadow-xl">
            <h3 class="text-sm font-bold text-amber-300 font-cinzel uppercase tracking-wider pb-3 mb-6 border-b border-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-amber-400"></i>
                <span>Personal & Contact Information</span>
            </h3>

            <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-4">
                @csrf

                <!-- Name & Mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Full Name *
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $member->name) }}"
                            required
                            class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500"
                        >
                    </div>

                    <div>
                        <label for="mobile" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Mobile Number * (10 Digits)
                        </label>
                        <input
                            type="tel"
                            id="mobile"
                            name="mobile"
                            value="{{ old('mobile', $member->mobile) }}"
                            required
                            maxlength="10"
                            pattern="[0-9]{10}"
                            class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Email Address *
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500"
                    >
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Residential Address / Colony
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        rows="2"
                        class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs sm:text-sm outline-none focus:border-amber-500 resize-none"
                    >{{ old('address', $member->address) }}</textarea>
                </div>

                <!-- Change Password Section -->
                <div class="pt-4 border-t border-slate-800 space-y-4">
                    <h4 class="text-xs font-semibold text-slate-300 uppercase tracking-wider">
                        Security & Password Change
                    </h4>

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-400 mb-1">
                            Current Password (Required to change password)
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            placeholder="Current Password"
                            class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="new_password" class="block text-xs font-semibold text-slate-400 mb-1">
                                New Password (Min 6 chars)
                            </label>
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                placeholder="New Password"
                                minlength="6"
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                            >
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-semibold text-slate-400 mb-1">
                                Confirm New Password
                            </label>
                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                placeholder="Repeat New Password"
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:border-amber-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md transition flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile Details
                    </button>
                </div>
            </form>
        </div>

        <!-- Financial Status Overview (1 Col) -->
        <div class="space-y-4">
            <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
                <h4 class="text-xs font-semibold text-slate-300 uppercase tracking-wider border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple text-amber-400"></i>
                    <span>Seva Summary</span>
                </h4>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Cleared Months:</span>
                        <span class="font-bold text-emerald-400">{{ $summary['paid_months'] }} Months</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Pending Months:</span>
                        <span class="font-bold text-amber-400">{{ $summary['total_pending_months'] }} Months</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total Seva Given:</span>
                        <span class="font-cinzel font-bold text-emerald-400 text-sm">₹{{ number_format($summary['total_paid'], 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                        <span class="text-slate-400">Pending Amount:</span>
                        <span class="font-cinzel font-bold text-amber-400 text-sm">₹{{ number_format($summary['total_pending'], 2) }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    <a
                        href="{{ route('member.payments.create') }}"
                        class="w-full py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center justify-center gap-1.5 transition"
                    >
                        <i class="fa-solid fa-qrcode"></i> Pay Pending Seva
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
