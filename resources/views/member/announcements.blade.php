@extends('layouts.member')

@section('title', 'Mandir Announcements & Notice Board')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-950/60 via-slate-900/90 to-orange-950/60 border border-amber-500/30 backdrop-blur-md flex items-center justify-between gap-4 shadow-xl sacred-glow">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold text-2xl sacred-glow flex-shrink-0">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold font-cinzel text-amber-300 sacred-text-glow">Mandir Notice Board</h3>
                <p class="text-xs text-slate-400">Festival preparations, special pujas, committee updates and community circulars.</p>
            </div>
        </div>
    </div>

    <!-- Announcements Cards -->
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden transition hover:border-amber-500/40">
                @if($announcement->priority === 'urgent')
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-rose-500"></div>
                @elseif($announcement->priority === 'important')
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-amber-500"></div>
                @else
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-sky-500"></div>
                @endif

                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider {{ $announcement->priority === 'urgent' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : ($announcement->priority === 'important' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30') }}">
                        {{ $announcement->priority }}
                    </span>

                    <span class="text-xs text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-day text-slate-500"></i>
                        {{ $announcement->publish_date ? $announcement->publish_date->format('d M Y') : '' }}
                    </span>
                </div>

                <h4 class="text-base font-bold text-slate-100 mb-2">
                    {{ $announcement->title }}
                </h4>

                <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line">
                    {{ $announcement->message }}
                </p>
            </div>
        @empty
            <div class="p-16 rounded-3xl bg-slate-900/50 border border-slate-800 text-center text-slate-500">
                <i class="fa-solid fa-om text-4xl mb-3 text-slate-600"></i>
                <h4 class="text-sm font-semibold text-slate-300">No Announcements at Present</h4>
                <p class="text-xs text-slate-500 mt-1">Check back later for festival and committee announcements.</p>
            </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of {{ $announcements->total() }} notices
            </div>
            <div>{{ $announcements->links() }}</div>
        </div>
    @endif
</div>
@endsection
