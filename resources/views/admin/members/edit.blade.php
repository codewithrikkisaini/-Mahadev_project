@extends('layouts.admin')

@section('title', 'Edit Member: ' . $member->name)
@section('header_title', 'Update Member Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.members.show', $member) }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $member->name }} Profile
        </a>
    </div>

    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl p-6 sm:p-8">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold font-cinzel text-amber-300">Edit Member Information</h3>
                    <p class="text-xs text-slate-400">Update contact info, contribution amount, or reset credentials.</p>
                </div>
            </div>
            <div class="font-mono text-sm font-bold text-amber-400 px-3 py-1 bg-amber-500/10 border border-amber-500/30 rounded-xl">
                {{ $member->member_code }}
            </div>
        </div>

        <form method="POST" action="{{ route('admin.members.update', $member) }}" class="space-y-6">
            @csrf
            @method('PUT')

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
                        value="{{ old('name', $member->name) }}"
                        required
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
                        value="{{ old('mobile', $member->mobile) }}"
                        required
                        maxlength="10"
                        pattern="[0-9]{10}"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <!-- Email & Custom Monthly Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $member->email) }}"
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
                        value="{{ old('custom_monthly_amount', $member->custom_monthly_amount) }}"
                        step="10"
                        min="0"
                        placeholder="Leave empty for global default"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-amber-400 font-cinzel font-bold text-sm outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <!-- Joining Date & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="joining_date" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Joining Date *
                    </label>
                    <input
                        type="date"
                        id="joining_date"
                        name="joining_date"
                        value="{{ old('joining_date', $member->joining_date ? $member->joining_date->format('Y-m-d') : '') }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
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
                        <option value="active" {{ old('status', $member->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $member->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ old('status', $member->status) === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    </select>
                </div>
            </div>

            <!-- New Password (Optional Reset) -->
            <div>
                <label for="new_password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Reset Login Password (Optional)
                </label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="Leave blank to keep existing password"
                    minlength="6"
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                >
            </div>

            <!-- Address & Notes -->
            <div>
                <label for="address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Address / Colony
                </label>
                <textarea
                    id="address"
                    name="address"
                    rows="2"
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none"
                >{{ old('address', $member->address) }}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Internal Admin Notes
                </label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="2"
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none"
                >{{ old('notes', $member->notes) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.members.show', $member) }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-semibold transition">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-orange-950/50 flex items-center gap-2 transition"
                >
                    <i class="fa-solid fa-floppy-disk"></i> Update Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
