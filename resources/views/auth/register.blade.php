@extends('layouts.auth')

@section('title', 'Member Registration')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-amber-500/20 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
    <!-- Top Decorative Line -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600"></div>

    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 mb-3 sacred-glow">
            <i class="fa-solid fa-user-plus text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold font-cinzel text-amber-300 sacred-text-glow">Member Registration</h2>
        <p class="text-xs text-slate-400 mt-1">Join the {{ $committeeName }} seva group</p>
    </div>

    <!-- Monthly Seva Highlight Banner -->
    <div class="mb-6 p-3.5 bg-gradient-to-r from-amber-950/40 to-orange-950/40 border border-amber-500/30 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-lg">
                ₹
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-300">Monthly Seva Contribution</p>
                <p class="text-[11px] text-slate-400">Regular seva for temple maintenance & festivals</p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-lg font-extrabold text-amber-400 font-cinzel">₹{{ number_format($monthlyAmount, 0) }}</span>
            <span class="text-[10px] text-slate-400 block">/ month</span>
        </div>
    </div>

    <!-- Admin Approval Notice -->
    <div class="mb-5 p-3 rounded-xl bg-slate-950/60 border border-amber-500/20 text-xs text-slate-300 flex items-start gap-2.5">
        <i class="fa-solid fa-shield-halved text-amber-400 text-sm mt-0.5 shrink-0"></i>
        <span>
            <strong class="text-amber-300">Approval Process:</strong> After submitting your details, your registration will be reviewed and approved by the <strong>Super Admin</strong>. Once accepted, your account will be activated for sign-in.
        </span>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Full Name *
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-user text-sm text-amber-500/70"></i>
                </div>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="e.g. Rajesh Kumar Sharma"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                >
            </div>
            @error('name')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Mobile Number -->
            <div>
                <label for="mobile" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Mobile Number *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-phone text-sm text-amber-500/70"></i>
                    </div>
                    <input
                        type="tel"
                        id="mobile"
                        name="mobile"
                        value="{{ old('mobile') }}"
                        required
                        maxlength="10"
                        pattern="[0-9]{10}"
                        placeholder="10-digit mobile"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                    >
                </div>
                @error('mobile')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Email Address *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-envelope text-sm text-amber-500/70"></i>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="yourname@gmail.com"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                    >
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Password *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-lock text-sm text-amber-500/70"></i>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        minlength="6"
                        placeholder="Min 6 chars"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                    >
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Confirm Password *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-check-double text-sm text-amber-500/70"></i>
                    </div>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        placeholder="Repeat password"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                    >
                </div>
            </div>
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Address / Colony (Optional)
            </label>
            <div class="relative">
                <div class="absolute top-3 left-3.5 text-slate-500 pointer-events-none">
                    <i class="fa-solid fa-location-dot text-sm text-amber-500/70"></i>
                </div>
                <textarea
                    id="address"
                    name="address"
                    rows="2"
                    placeholder="House / Street / Colony details"
                    class="w-full pl-10 pr-4 py-2 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none resize-none"
                >{{ old('address') }}</textarea>
            </div>
            @error('address')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:via-orange-500 hover:to-amber-600 text-white font-semibold rounded-xl text-sm transition transform active:scale-[0.98] shadow-lg shadow-orange-900/30 flex items-center justify-center gap-2 sacred-glow mt-2"
        >
            <i class="fa-solid fa-user-plus"></i> Complete Registration & Join
        </button>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center pt-4 border-t border-slate-800">
        <p class="text-xs text-slate-400">
            Already registered as a member?
            <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 font-semibold underline underline-offset-4 ml-1">
                Log in here
            </a>
        </p>
    </div>
</div>
@endsection
