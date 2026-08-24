<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CommitteeNotification;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'in:normal,important,urgent'],
            'publish_date' => ['required', 'date'],
            'status' => ['required', 'in:published,draft,archived'],
            'send_notification' => ['nullable', 'boolean'],
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'priority' => $validated['priority'],
            'publish_date' => $validated['publish_date'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        if ($request->boolean('send_notification') && $validated['status'] === 'published') {
            CommitteeNotification::sendToAllMembers(
                "📢 {$validated['title']}",
                $validated['message'],
                'announcement',
                route('member.announcements')
            );
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement published successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'in:normal,important,urgent'],
            'publish_date' => ['required', 'date'],
            'status' => ['required', 'in:published,draft,archived'],
        ]);

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully.');
    }
}
