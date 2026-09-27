@extends('layouts.member')

@section('title', 'Notifications Center')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-950/60 via-slate-900/90 to-slate-900/80 border border-amber-500/30 backdrop-blur-md flex items-center justify-between gap-4 shadow-xl sacred-glow">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold text-2xl sacred-glow flex-shrink-0">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold font-cinzel text-amber-300 sacred-text-glow">Notifications & Alerts</h3>
                <p class="text-xs text-slate-400">Payment receipts, reminder notices, and committee communications.</p>
            </div>
        </div>

        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button
                    type="submit"
                    class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition whitespace-nowrap"
                >
                    <i class="fa-solid fa-check-double"></i> Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <!-- Notification Feed -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            <a
                href="{{ route('notifications.read', $notif) }}"
                class="block p-4 sm:p-5 rounded-2xl border transition shadow-lg relative overflow-hidden group {{ $notif->is_read ? 'bg-slate-900/70 border-slate-800 hover:border-slate-700 text-slate-300' : 'bg-slate-900/95 border-amber-500/40 hover:border-amber-500/70 text-slate-100 sacred-glow' }}"
            >
                @if(!$notif->is_read)
                    <span class="absolute top-4 right-4 w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                @endif

                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base flex-shrink-0 {{ $notif->type === 'payment_approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($notif->type === 'reminder' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($notif->type === 'payment_rejected' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-sky-500/20 text-sky-400 border border-sky-500/30')) }}">
                        @if($notif->type === 'payment_approved')
                            <i class="fa-solid fa-receipt"></i>
                        @elseif($notif->type === 'reminder')
                            <i class="fa-solid fa-clock"></i>
                        @elseif($notif->type === 'payment_rejected')
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        @else
                            <i class="fa-solid fa-bullhorn"></i>
                        @endif
                    </div>

                    <div class="flex-1 pr-6">
                        <h4 class="text-sm font-bold {{ $notif->is_read ? 'text-slate-200' : 'text-amber-300' }} group-hover:text-amber-300 transition">
                            {{ $notif->title }}
                        </h4>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            {{ $notif->message }}
                        </p>
                        <span class="text-[10px] text-slate-500 mt-2 block">
                            {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-16 rounded-3xl bg-slate-900/50 border border-slate-800 text-center text-slate-500">
                <i class="fa-solid fa-bell-slash text-4xl mb-3 text-slate-600"></i>
                <h4 class="text-sm font-semibold text-slate-300">No Notifications</h4>
                <p class="text-xs text-slate-500 mt-1">You have no new alerts or notifications at this time.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} alerts
            </div>
            <div>{{ $notifications->links() }}</div>
        </div>
    @endif
</div>
@endsection
