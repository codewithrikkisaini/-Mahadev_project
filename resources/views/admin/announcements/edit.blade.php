@extends('layouts.admin')

@section('title', 'Edit Announcement')
@section('header_title', 'Edit Mandir Notice')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.announcements.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Announcements
        </a>
    </div>

    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-800">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold font-cinzel text-amber-300">Edit Announcement Details</h3>
                <p class="text-xs text-slate-400">Modify content, change schedule date or update priority status.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Announcement Title *
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $announcement->title) }}"
                    required
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                >
            </div>

            <!-- Priority, Publish Date & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="priority" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Priority Level *
                    </label>
                    <select id="priority" name="priority" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500">
                        <option value="normal" {{ old('priority', $announcement->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="important" {{ old('priority', $announcement->priority) === 'important' ? 'selected' : '' }}>Important 🔔</option>
                        <option value="urgent" {{ old('priority', $announcement->priority) === 'urgent' ? 'selected' : '' }}>Urgent 🚨</option>
                    </select>
                </div>

                <div>
                    <label for="publish_date" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Publish Date *
                    </label>
                    <input
                        type="date"
                        id="publish_date"
                        name="publish_date"
                        value="{{ old('publish_date', $announcement->publish_date ? $announcement->publish_date->format('Y-m-d') : '') }}"
                        required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500"
                    >
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Status *
                    </label>
                    <select id="status" name="status" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500">
                        <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $announcement->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <!-- Message Body -->
            <div>
                <label for="message" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Announcement Message / Notice Details *
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    required
                    class="w-full px-4 py-3 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-sm outline-none focus:border-amber-500 resize-none leading-relaxed"
                >{{ old('message', $announcement->message) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.announcements.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-semibold transition">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-orange-950/50 flex items-center gap-2 transition"
                >
                    <i class="fa-solid fa-floppy-disk"></i> Update Notice
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
