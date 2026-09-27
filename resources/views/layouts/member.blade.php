<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member Portal') | {{ \App\Models\Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700&family=Rozha+One&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        cinzel: ['Cinzel', 'serif'],
                        sacred: ['Rozha One', 'serif']
                    },
                    colors: {
                        saffron: {
                            50: '#fff8eb',
                            100: '#feedc7',
                            200: '#fdd98a',
                            300: '#fbbf4d',
                            400: '#f9a11f',
                            500: '#f17e08',
                            600: '#d75d04',
                            700: '#b24106',
                            800: '#8e330c',
                            900: '#732a0c',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        .sacred-glow {
            box-shadow: 0 0 25px rgba(241, 126, 8, 0.25);
        }
        .sacred-text-glow {
            text-shadow: 0 0 15px rgba(249, 161, 31, 0.4);
        }
        .bg-pattern {
            background-image: radial-gradient(rgba(241, 126, 8, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans selection:bg-orange-500 selection:text-white flex flex-col justify-between relative pb-20 md:pb-0">
    <!-- Ambient Background Lighting -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-32 left-1/4 w-96 h-96 bg-orange-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-pattern opacity-40"></div>
    </div>

    <!-- Top Navigation Header -->
    <header class="relative z-30 bg-slate-900/80 border-b border-slate-800 backdrop-blur-xl sticky top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('member.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 p-0.5 sacred-glow transition transform group-hover:scale-105">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-amber-400 font-sacred text-xl font-bold">
                        ॐ
                    </div>
                </div>
                <div>
                    <h1 class="font-cinzel font-bold text-sm sm:text-base text-amber-300 sacred-text-glow leading-tight">
                        {{ \App\Models\Setting::get('temple_name', 'श्री महादेव मंदिर') }}
                    </h1>
                    <p class="text-[11px] text-amber-200/70 font-medium tracking-wide flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        ID: {{ auth()->user()->member->member_code ?? 'MEMBER' }}
                    </p>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a
                    href="{{ route('member.dashboard') }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('member.dashboard') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                >
                    <i class="fa-solid fa-house mr-1.5"></i> Dashboard
                </a>
                <a
                    href="{{ route('member.payments.create') }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold transition bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md hover:from-amber-500 hover:to-orange-500 flex items-center gap-1.5 mx-1"
                >
                    <i class="fa-solid fa-qrcode text-xs"></i> Pay Seva (QR / UPI)
                </a>
                <a
                    href="{{ route('member.payments.index') }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('member.payments.index') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                >
                    <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Payment History
                </a>
                <a
                    href="{{ route('member.announcements') }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('member.announcements') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                >
                    <i class="fa-solid fa-bullhorn mr-1.5"></i> Notices
                </a>
            </nav>

            <!-- Right Profile & Notification Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                @endphp
                <a
                    href="{{ route('member.notifications') }}"
                    class="relative p-2 text-slate-300 hover:text-amber-300 bg-slate-800/60 hover:bg-slate-800 border border-slate-700/80 rounded-xl transition"
                    title="Notifications"
                >
                    <i class="fa-solid fa-bell text-sm"></i>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-[10px] text-white font-bold flex items-center justify-center animate-bounce">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>

                <!-- Profile Link / Dropdown -->
                <a
                    href="{{ route('member.profile') }}"
                    class="flex items-center gap-2 p-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/80 text-xs font-medium text-slate-200 transition"
                >
                    <div class="w-6 h-6 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:inline truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        title="Sign Out"
                        class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-950/40 rounded-xl transition border border-transparent hover:border-rose-900/50"
                    >
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Member Content Area -->
    <main class="relative z-10 flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-sm flex items-start justify-between gap-3 backdrop-blur-md shadow-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-200">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-sm flex items-start justify-between gap-3 backdrop-blur-md shadow-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-200">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-sm backdrop-blur-md shadow-lg">
                <div class="flex items-center gap-2 font-semibold mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs text-rose-200 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom App Bar Navigation (Smooth Native App Feel) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 border-t border-slate-800/90 backdrop-blur-xl px-2 py-1.5 flex items-center justify-around shadow-2xl">
        <a
            href="{{ route('member.dashboard') }}"
            class="flex flex-col items-center py-1 px-3 text-[10px] font-medium transition {{ request()->routeIs('member.dashboard') ? 'text-amber-400 font-bold' : 'text-slate-400' }}"
        >
            <i class="fa-solid fa-house text-base mb-0.5"></i>
            <span>Home</span>
        </a>

        <a
            href="{{ route('member.payments.index') }}"
            class="flex flex-col items-center py-1 px-3 text-[10px] font-medium transition {{ request()->routeIs('member.payments.index') ? 'text-amber-400 font-bold' : 'text-slate-400' }}"
        >
            <i class="fa-solid fa-receipt text-base mb-0.5"></i>
            <span>History</span>
        </a>

        <!-- Floating Sacred Pay Button -->
        <a
            href="{{ route('member.payments.create') }}"
            class="flex flex-col items-center -mt-5"
        >
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-amber-600 to-orange-500 flex items-center justify-center text-white shadow-xl shadow-orange-950/60 border-2 border-slate-950 transform active:scale-95 transition sacred-glow">
                <i class="fa-solid fa-qrcode text-lg"></i>
            </div>
            <span class="text-[10px] font-bold text-amber-400 mt-0.5">Pay Seva</span>
        </a>

        <a
            href="{{ route('member.announcements') }}"
            class="flex flex-col items-center py-1 px-3 text-[10px] font-medium transition {{ request()->routeIs('member.announcements') ? 'text-amber-400 font-bold' : 'text-slate-400' }}"
        >
            <i class="fa-solid fa-bullhorn text-base mb-0.5"></i>
            <span>Notices</span>
        </a>

        <a
            href="{{ route('member.profile') }}"
            class="flex flex-col items-center py-1 px-3 text-[10px] font-medium transition {{ request()->routeIs('member.profile') ? 'text-amber-400 font-bold' : 'text-slate-400' }}"
        >
            <i class="fa-solid fa-user text-base mb-0.5"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Desktop Footer -->
    <footer class="relative z-10 py-5 text-center text-xs text-slate-500 border-t border-slate-900 bg-slate-950/60 backdrop-blur-sm hidden md:block">
        <p class="font-medium text-slate-400">
            🙏 {{ \App\Models\Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee') }} &copy; {{ date('Y') }}
        </p>
        <p class="text-[11px] text-slate-600 mt-0.5">हर हर महादेव • सेवा ही संकल्प • जय श्री महाकाल</p>
    </footer>

    @stack('scripts')
</body>
</html>
