<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminControllerAnnouncement extends Controller
{
    // Show all announcements
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.admin_announcement', compact('announcements'));
    }

    // Store a new announcement
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'nullable|string|max:1500',
    ]);

    $message = $request->message;
    if (!is_null($message)) {
        // Replace actual newline characters with literal "\n"
        $message = str_replace(["\r\n", "\r", "\n"], '\\n', $message);
    }

    Announcement::create([
        'title' => $request->title,
        'message' => $message,
    ]);

    return redirect()->back()->with('success', 'Announcement added successfully.');
}

    // Update an existing announcement
    public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'nullable|string|max:1500',
    ]);

    $message = $request->message;
    if (!is_null($message)) {
        // Replace actual newline characters with literal "\n"
        $message = str_replace(["\r\n", "\r", "\n"], '\\n', $message);
    }

    $announcement = Announcement::findOrFail($id);
    $announcement->update([
        'title' => $request->title,
        'message' => $message,  // use the converted message
    ]);

    return redirect()->back()->with('success', 'Announcement updated successfully.');
}


    // Delete an announcement
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }
}
