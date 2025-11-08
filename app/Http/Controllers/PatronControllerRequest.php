<?php
namespace App\Http\Controllers;
use App\Models\ArchiveAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Archive;
use App\Models\Program;
use Illuminate\Support\Facades\Storage;
use App\Models\Bookmark;
class PatronControllerRequest extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();
    $userId = $user->id;

    $query = Archive::with([
            'program',
            'keywords',
            // load only this user’s requests
            'accessRequests' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])
        ->whereHas('accessRequests', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->where('status', 'Publish');

    $totalArchives = Archive::where('status', 'Publish')->count();
    $filteredCount = $query->count();

    $archives = $query
        ->orderByDesc('views')
        ->orderByDesc('created_at')
        ->paginate(50)
        ->withQueryString();

    return view('patron.patron_archive_request', compact('archives', 'totalArchives', 'filteredCount'));
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
        'request_status' => $existingRequest?->status, 
    ]);
}

public function requestAccess($archiveId)
{
    $userId = Auth::id();

    // Check if already requested
    $existingRequest = ArchiveAccessRequest::where('user_id', $userId)
        ->where('archive_id', $archiveId)
        ->first();

    if ($existingRequest) {
        return redirect()->back()->with('info', 'You have already requested access for this archive.');
    }

    // Create a new request
    ArchiveAccessRequest::create([
        'user_id' => $userId,
        'archive_id' => $archiveId,
        'status' => 'pending',
    ]);

    return redirect()->back()->with('success', 'Access request submitted successfully.');
}

}


