<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Program;
use Illuminate\Support\Facades\Storage;
use App\Models\ArchiveAccessRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookmark;
class PatronControllerBookmark extends Controller
{

public function index(Request $request)
{
    $user = auth()->user(); // get the logged-in user

    // Only show archives that this user bookmarked
    $query = Archive::with(['program', 'keywords', 'bookmarks'])
        ->whereHas('bookmarks', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'Publish');

    // Total published archives (for reference)
    $totalArchives = Archive::where('status', 'Publish')->count();

    // Count after filter
    $filteredCount = $query->count();

    // Order by views and latest created
    $query->orderByDesc('views')->orderByDesc('created_at');

    // Paginate
    $archives = $query->paginate(50)->withQueryString();

    return view('patron.patron_bookmark', compact('archives', 'totalArchives', 'filteredCount'));
}


public function getArchive($id)
{  
    $archive = Archive::with(['program'])->findOrFail($id);

    // Increment views
    $archive->increment('views');

    // ✅ Check if current user has already requested access
    $existingRequest = ArchiveAccessRequest::where('user_id', auth()->id())
        ->where('archive_id', $id)
        ->latest()
        ->first();

    return response()->json([
        'id'             => $archive->id, // <--- add this
        'file_path'      => asset('storage/' . $archive->file_path),
        'title'          => $archive->title,
        'subject'       => $archive->subject,
        'authors'        => $archive->authors ?? null,
        'program'        => $archive->program->name ?? null,
        'year'           => $archive->year ?? null,
        'views'          => $archive->views,
        'category'       => $archive->category ?? null,
        'request_status' => $existingRequest?->status, // null if no request
    ]);
}

public function requestAccess($id)
{
    $userId = auth()->id();

    // Check if already requested
    $existing = ArchiveAccessRequest::where('user_id', $userId)
        ->where('archive_id', $id)
        ->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'You already requested access for this archive.',
            'status' => $existing->status
        ], 400);
    }

    // Create new request
    $request = ArchiveAccessRequest::create([
        'user_id'   => $userId,
        'archive_id'=> $id,
        'status'    => 'pending',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Access request submitted successfully.',
        'status'  => $request->status,
    ]);
}

 public function toggleBookmark($id)
    {
        $userId = Auth::id();

        $bookmark = Bookmark::where('user_id', $userId)
                            ->where('archive_id', $id)
                            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'Bookmark removed successfully!';
        } else {
            Bookmark::create([
                'user_id' => $userId,
                'archive_id' => $id,
            ]);
            $message = 'Bookmark added successfully!';
        }

        return redirect()->back()->with('success', $message);
    }


}
