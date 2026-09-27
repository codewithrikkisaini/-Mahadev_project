@extends('layouts.admin')

@section('title', 'Add New Member')
@section('header_title', 'Register New Committee Member')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.members.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Members Directory
        </a>
    </div>

    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-800">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold font-cinzel text-amber-300">Create New Member Profile</h3>
                <p class="text-xs text-slate-400">Generate Member ID, configure monthly contribution, and assign initial login credentials.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.members.store') }}" class="space-y-6">
            @csrf

            <!-- Member Code & Joining Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="member_code" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Member ID / Code *
                    </label>
                    <input
                        type="text"
                        id="member_code"
                        name="member_code"
                        value="{{ old('member_code', $nextCode) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-amber-400 font-mono font-bold text-sm outline-none focus:border-amber-500"
                    >
                    <p class="text-[11px] text-slate-500 mt-1">Auto-generated sequential identifier</p>
                </div>

                <div>
                    <label for="joining_date" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Joining Date *
                    </label>
                    <input
                        type="date"
                        id="joining_date"
                        name="joining_date"
                        value="{{ old('joining_date', date('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <!-- Full Name & Mobile -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Full Name *
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g. Ramesh Chandra"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
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
                        value="{{ old('mobile') }}"
                        required
                        maxlength="10"
                        pattern="[0-9]{10}"
                        placeholder="e.g. 9876543210"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <!-- Email & Custom Monthly Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Email Address (Optional)
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="member@gmail.com"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>

                <div>
                    <label for="custom_monthly_amount" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Monthly Contribution (₹)
                    </label>
                    <input
                        type="number"
                        id="custom_monthly_amount"
                        name="custom_monthly_amount"
                        value="{{ old('custom_monthly_amount', $defaultMonthlyAmount) }}"
                        step="10"
                        min="0"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-amber-400 font-cinzel font-bold text-sm outline-none focus:border-amber-500"
                    >
                    <p class="text-[11px] text-slate-500 mt-1">Default is ₹{{ $defaultMonthlyAmount }}. Leave or change for special donor.</p>
                </div>
            </div>

            <!-- Initial Password & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Initial Login Password
                    </label>
                    <input
                        type="text"
                        id="password"
                        name="password"
                        value="{{ old('password', '123456') }}"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                    <p class="text-[11px] text-slate-500 mt-1">Default is 123456. Member can change in their profile.</p>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Membership Status *
                    </label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Regular Seva Contributor)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    </select>
                </div>
            </div>

            <!-- Address & Notes -->
            <div>
                <label for="address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Address / Colony (Optional)
                </label>
                <textarea
                    id="address"
                    name="address"
                    rows="2"
                    placeholder="House, Street, Locality details"
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none"
                >{{ old('address') }}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Internal Notes / Reference
                </label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="2"
                    placeholder="Additional details, reference person, etc."
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none"
                >{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.members.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-semibold transition">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-orange-950/50 flex items-center gap-2 transition"
                >
                    <i class="fa-solid fa-check"></i> Save & Register Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
