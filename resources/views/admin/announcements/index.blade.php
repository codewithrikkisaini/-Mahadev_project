@extends('layouts.admin')

@section('title', 'Announcements & Notice Board')
@section('header_title', 'Committee Announcements & Notices')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-lg sacred-glow">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-200">Mandir Seva Announcements</h3>
                <p class="text-xs text-slate-400">Broadcast festival dates, aarti timings, committee meetings & financial updates</p>
            </div>
        </div>

        <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5 transition whitespace-nowrap self-start sm:self-auto">
            <i class="fa-solid fa-plus text-xs"></i> New Announcement
        </a>
    </div>

    <!-- Announcements Cards List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($announcements as $announcement)
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col justify-between hover:border-slate-700 transition relative overflow-hidden group">
                <!-- Priority Border Indicator -->
                @if($announcement->priority === 'urgent')
                    <div class="absolute top-0 left-0 right-0 h-1 bg-rose-500"></div>
                @elseif($announcement->priority === 'important')
                    <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500"></div>
                @else
                    <div class="absolute top-0 left-0 right-0 h-1 bg-sky-500"></div>
                @endif

                <div>
                    <!-- Meta Row -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider {{ $announcement->priority === 'urgent' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : ($announcement->priority === 'important' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30') }}">
                            {{ $announcement->priority }}
                        </span>

                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize {{ $announcement->status === 'published' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                            {{ $announcement->status }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h4 class="text-base font-bold text-slate-100 mb-2 group-hover:text-amber-300 transition">
                        {{ $announcement->title }}
                    </h4>

                    <!-- Message Body -->
                    <p class="text-xs text-slate-300 leading-relaxed line-clamp-4 mb-4">
                        {{ $announcement->message }}
                    </p>
                </div>

                <!-- Footer details & Actions -->
                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1 text-[11px]">
                        <i class="fa-solid fa-calendar-day text-slate-600"></i>
                        {{ $announcement->publish_date ? $announcement->publish_date->format('d M Y') : '' }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="p-1.5 text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 rounded-2xl border border-slate-800">
                <i class="fa-solid fa-bullhorn text-4xl mb-3 text-slate-600"></i>
                <h4 class="text-sm font-semibold text-slate-300">No Announcements Created Yet</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Publish festival circulars, seva schedules and committee updates for members.</p>
            </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of {{ $announcements->total() }} announcements
            </div>
            <div>{{ $announcements->links() }}</div>
        </div>
    @endif
</div>
@endsection
