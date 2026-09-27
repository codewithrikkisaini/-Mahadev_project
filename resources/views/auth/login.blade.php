@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-amber-500/20 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden" x-data="{ roleTab: 'member' }">
    <!-- Top Decorative Line -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600"></div>

    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 mb-3 sacred-glow">
            <i class="fa-solid fa-om text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold font-cinzel text-amber-300 sacred-text-glow">Committee Portal</h2>
        <p class="text-xs text-slate-400 mt-1">Enter your Member ID, Mobile Number or Email to sign in</p>
    </div>

    <!-- Quick Role Switcher Tabs (For UI guidance) -->
    <div class="flex p-1 bg-slate-950/70 rounded-xl border border-slate-800 mb-6">
        <button type="button" @click="roleTab = 'member'" :class="roleTab === 'member' ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:text-slate-200'" class="flex-1 py-2 text-xs rounded-lg transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-users text-xs"></i> Member Login
        </button>
        <button type="button" @click="roleTab = 'admin'" :class="roleTab === 'admin' ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:text-slate-200'" class="flex-1 py-2 text-xs rounded-lg transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-shield text-xs"></i> Super Admin
        </button>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="login" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                <span x-show="roleTab === 'member'">Member ID / Mobile / Email</span>
                <span x-show="roleTab === 'admin'">Admin Email / Mobile</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-user text-sm text-amber-500/70"></i>
                </div>
                <input
                    type="text"
                    id="login"
                    name="login"
                    value="{{ old('login') }}"
                    required
                    autofocus
                    :placeholder="roleTab === 'member' ? 'e.g. MSC00001 or 9876543210' : 'e.g. admin@mandirseva.com'"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                >
            </div>
            @error('login')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                    Password
                </label>
                <span class="text-[11px] text-amber-400/80">Default: 123456</span>
            </div>
            <div class="relative" x-data="{ showPass: false }">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-lock text-sm text-amber-500/70"></i>
                </div>
                <input
                    :type="showPass ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-950/90 border border-slate-700/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-slate-100 placeholder-slate-500 text-sm transition outline-none"
                >
                <button
                    type="button"
                    @click="showPass = !showPass"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-amber-400 transition"
                >
                    <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-amber-600 focus:ring-amber-500/30">
                <span class="text-xs text-slate-400">Remember me</span>
            </label>
            <span class="text-xs text-slate-500">Need help? Ask Admin</span>
        </div>

        <button
            type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:via-orange-500 hover:to-amber-600 text-white font-semibold rounded-xl text-sm transition transform active:scale-[0.98] shadow-lg shadow-orange-900/30 flex items-center justify-center gap-2 sacred-glow"
        >
            <i class="fa-solid fa-right-to-bracket"></i> Sign In to Seva Portal
        </button>
    </form>

    <!-- Quick Demo Credentials helper -->
    <div class="mt-6 pt-5 border-t border-slate-800/80">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
            <span>Demo Test Logins</span>
            <span class="text-amber-400 font-normal">Click to fill</span>
        </p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button
                type="button"
                onclick="fillLogin('admin@mandirseva.com', 'password')"
                class="p-2 bg-slate-950/60 hover:bg-amber-950/30 border border-slate-800 hover:border-amber-500/40 rounded-lg text-left transition text-slate-300 hover:text-amber-300 group"
            >
                <div class="font-bold text-amber-400 flex items-center justify-between">
                    <span>👑 Admin</span>
                    <i class="fa-solid fa-arrow-turn-down text-[10px] opacity-0 group-hover:opacity-100 transition"></i>
                </div>
                <div class="text-[10px] text-slate-400 truncate">admin@mandirseva.com (password)</div>
            </button>
            <button
                type="button"
                onclick="fillLogin('MSC00001', 'password')"
                class="p-2 bg-slate-950/60 hover:bg-amber-950/30 border border-slate-800 hover:border-amber-500/40 rounded-lg text-left transition text-slate-300 hover:text-amber-300 group"
            >
                <div class="font-bold text-amber-400 flex items-center justify-between">
                    <span>🙏 Member</span>
                    <i class="fa-solid fa-arrow-turn-down text-[10px] opacity-0 group-hover:opacity-100 transition"></i>
                </div>
                <div class="text-[10px] text-slate-400 truncate">MSC00001 (password)</div>
            </button>
        </div>
    </div>

    <!-- Registration Link -->
    <div class="mt-5 text-center">
        <p class="text-xs text-slate-400">
            Not registered yet?
            <a href="{{ route('register') }}" class="text-amber-400 hover:text-amber-300 font-semibold underline underline-offset-4 ml-1">
                Register as Member
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    function fillLogin(login, pass) {
        document.getElementById('login').value = login;
        document.getElementById('password').value = pass;
    }
</script>
@endpush
@endsection
