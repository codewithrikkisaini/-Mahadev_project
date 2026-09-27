<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | {{ \App\Models\Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee') }}</title>

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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sacred-glow {
            box-shadow: 0 0 25px rgba(241, 126, 8, 0.2);
        }
        .sacred-text-glow {
            text-shadow: 0 0 15px rgba(249, 161, 31, 0.4);
        }
        .bg-pattern {
            background-image: radial-gradient(rgba(241, 126, 8, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #f97316;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans selection:bg-orange-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex flex-col lg:flex-row relative">
        <!-- Ambient background glow -->
        <div class="fixed inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -left-32 w-80 h-80 bg-amber-600/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-32 w-80 h-80 bg-orange-600/10 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-pattern opacity-40"></div>
        </div>

        <!-- Mobile Sidebar Backdrop -->
        <div
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/80 z-40 lg:hidden backdrop-blur-sm"
            style="display: none;"
        ></div>

        <!-- Sidebar Navigation -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static top-0 bottom-0 left-0 z-50 w-72 bg-slate-900/95 lg:bg-slate-900/80 border-r border-slate-800 backdrop-blur-xl flex flex-col justify-between transition-transform duration-300 ease-in-out shadow-2xl"
        >
            <div>
                <!-- Brand Header -->
                <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 p-0.5 sacred-glow transition transform group-hover:scale-105">
                            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-amber-400 font-sacred text-xl font-bold">
                                ॐ
                            </div>
                        </div>
                        <div>
                            <span class="font-cinzel font-bold text-sm tracking-wide text-amber-400 block sacred-text-glow leading-tight">
                                {{ \App\Models\Setting::get('temple_name', 'श्री महादेव मंदिर') }}
                            </span>
                            <span class="text-[10px] uppercase font-semibold tracking-wider text-amber-500/80 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                Super Admin Panel
                            </span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5 text-sm">
                    <div class="px-3 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        Core Management
                    </div>

                    <!-- Dashboard -->
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-gauge text-base w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Members -->
                    <a
                        href="{{ route('admin.members.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.members.*') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-users text-base w-5 text-center {{ request()->routeIs('admin.members.*') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Members</span>
                        <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                            {{ \App\Models\Member::count() }}
                        </span>
                    </a>

                    <!-- Pending Verifications (Alert Badge) -->
                    @php
                        $pendingVerificationsCount = \App\Models\Payment::where('status', 'pending')->count();
                    @endphp
                    <a
                        href="{{ route('admin.payments.pending') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.payments.pending') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center {{ request()->routeIs('admin.payments.pending') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Verification Queue</span>
                        @if($pendingVerificationsCount > 0)
                            <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full bg-rose-600 text-white animate-pulse">
                                {{ $pendingVerificationsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- All Payments Ledger -->
                    <a
                        href="{{ route('admin.payments.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.payments.index') || request()->routeIs('admin.payments.show') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-receipt text-base w-5 text-center {{ request()->routeIs('admin.payments.index') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Payments Ledger</span>
                    </a>

                    <div class="pt-3 px-3 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        Reports & Notices
                    </div>

                    <!-- Monthly & Member Matrix Reports -->
                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.reports.index') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-chart-pie text-base w-5 text-center {{ request()->routeIs('admin.reports.index') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Monthly Reports</span>
                    </a>

                    <a
                        href="{{ route('admin.reports.member') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.reports.member') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-table-cells text-base w-5 text-center {{ request()->routeIs('admin.reports.member') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Member Matrix Grid</span>
                    </a>

                    <!-- Announcements -->
                    <a
                        href="{{ route('admin.announcements.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.announcements.*') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-bullhorn text-base w-5 text-center {{ request()->routeIs('admin.announcements.*') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Announcements</span>
                    </a>

                    <div class="pt-3 px-3 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        Configuration
                    </div>

                    <!-- Settings -->
                    <a
                        href="{{ route('admin.settings.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-orange-950/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-sliders text-base w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-amber-400' }}"></i>
                        <span>Committee Settings</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer User Profile & Logout -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 truncate">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-bold text-sm">
                            👑
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-slate-200 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-amber-400/80 capitalize">Super Admin</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-950/40 rounded-lg transition">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 relative z-10">
            <!-- Topbar Header -->
            <header class="h-16 bg-slate-900/60 border-b border-slate-800 backdrop-blur-md px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-slate-800/50 border border-slate-700">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h2 class="text-base sm:text-lg font-bold font-cinzel text-amber-300 truncate">
                        @yield('header_title', 'Admin Dashboard')
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Monthly Amount Pill -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs">
                        <span class="text-slate-400 font-medium">Monthly Seva:</span>
                        <span class="font-bold text-amber-400">₹{{ number_format(\App\Models\Setting::getMonthlyAmount(), 0) }}</span>
                    </div>

                    <!-- Quick Add Member Action -->
                    <a href="{{ route('admin.members.create') }}" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span class="hidden sm:inline">Add Member</span>
                    </a>

                    <!-- Verification Queue Notification Bell -->
                    <a href="{{ route('admin.payments.pending') }}" class="relative p-2 text-slate-400 hover:text-amber-400 bg-slate-800/50 hover:bg-slate-800 border border-slate-700 rounded-xl transition">
                        <i class="fa-solid fa-bell text-sm"></i>
                        @if($pendingVerificationsCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-[10px] text-white font-bold flex items-center justify-center animate-bounce">
                                {{ $pendingVerificationsCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">
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

                @if(session('warning'))
                    <div class="p-4 rounded-2xl bg-amber-950/80 border border-amber-500/40 text-amber-300 text-sm flex items-start justify-between gap-3 backdrop-blur-md shadow-lg" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-base"></i>
                            <div>{{ session('warning') }}</div>
                        </div>
                        <button @click="show = false" class="text-amber-400 hover:text-amber-200">
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
                            <span>Please correct the following errors:</span>
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

            <!-- Admin Footer -->
            <footer class="py-4 px-6 border-t border-slate-800 text-center text-xs text-slate-500 bg-slate-950/60">
                🙏 {{ \App\Models\Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee') }} &copy; {{ date('Y') }} • Har Har Mahadev
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
