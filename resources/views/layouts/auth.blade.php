<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') | {{ \App\Models\Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700&family=Rozha+One&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN for instant robust styling & animation -->
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
                        },
                        shiv: {
                            dark: '#0a0d14',
                            card: '#121824',
                            border: '#1f293d',
                            glow: '#ff9800'
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
            box-shadow: 0 0 35px rgba(241, 126, 8, 0.25);
        }
        .sacred-text-glow {
            text-shadow: 0 0 20px rgba(249, 161, 31, 0.5);
        }
        .bg-pattern {
            background-image: radial-gradient(rgba(241, 126, 8, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans selection:bg-orange-500 selection:text-white flex flex-col justify-between relative overflow-x-hidden">
    <!-- Ambient Background Glows -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-orange-600/15 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-red-600/10 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-pattern opacity-60"></div>
    </div>

    <!-- Header Navigation -->
    <header class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 py-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 p-0.5 sacred-glow transition transform group-hover:scale-105">
                <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-amber-400 font-sacred text-2xl font-bold">
                    ॐ
                </div>
            </div>
            <div>
                <h1 class="font-cinzel font-bold text-lg sm:text-xl text-amber-400 tracking-wide sacred-text-glow leading-tight">
                    {{ \App\Models\Setting::get('temple_name', 'श्री महादेव मंदिर') }}
                </h1>
                <p class="text-xs text-amber-200/70 font-medium tracking-wider">
                    {{ \App\Models\Setting::get('committee_tagline', 'सेवा ही संकल्प • हर हर महादेव') }}
                </p>
            </div>
        </a>

        <div class="flex items-center gap-3">
            @if(request()->routeIs('login'))
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-amber-300 hover:text-white bg-slate-900/80 hover:bg-slate-800/90 border border-amber-500/30 rounded-lg transition shadow-sm backdrop-blur-md">
                    <i class="fa-solid fa-user-plus mr-1.5 text-xs"></i> New Member Join
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-amber-300 hover:text-white bg-slate-900/80 hover:bg-slate-800/90 border border-amber-500/30 rounded-lg transition shadow-sm backdrop-blur-md">
                    <i class="fa-solid fa-right-to-bracket mr-1.5 text-xs"></i> Member / Admin Login
                </a>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-md">
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-sm flex items-start gap-3 backdrop-blur-md animate-fade-in shadow-lg">
                    <i class="fa-solid fa-circle-check mt-0.5 text-emerald-400 text-base"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-sm flex items-start gap-3 backdrop-blur-md shadow-lg">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-400 text-base"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-sm flex items-start gap-3 backdrop-blur-md shadow-lg">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-400 text-base"></i>
                    <div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 py-6 text-center text-xs text-slate-500 border-t border-slate-900 bg-slate-950/60 backdrop-blur-sm">
        <p class="font-medium text-slate-400">
            🙏 {{ \App\Models\Setting::get('committee_name', 'Shri Mahadev Mandir Seva Committee') }} &copy; {{ date('Y') }}
        </p>
        <p class="text-[11px] text-slate-600 mt-1">Monthly Committee Seva Contribution & Transparency Management Portal</p>
    </footer>

    @stack('scripts')
</body>
</html>
