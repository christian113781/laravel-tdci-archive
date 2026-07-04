<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Log;
use App\Models\Program;
use Illuminate\Support\Facades\Storage;
use App\Models\ArchiveAccessRequest;
use App\Models\ArchiveViewLog;

class StaffControllerArchive extends Controller
{

public function index(Request $request)
{
    // Base query with relationships, only published archives
    $query = Archive::with(['program'])
                    ->where('status', 'Publish');

    // Total before filters
    $totalArchives = Archive::where('status', 'Publish')->count();

    // Apply filters if any
    if ($request->filled('field') && $request->filled('search')) {
        $search = $request->input('search');
        $field  = $request->input('field');

        switch ($field) {
            case '1': // Title
                $query->where('title', 'like', "%$search%");
                break;
            case '2': // Author (from archive table)
                $query->where('authors', 'like', "%$search%");
                break;
            case '3': // Keyword
                $query->whereHas('keywords', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
                break;
            case '4': // Year
                $query->where('year', $search);
                break;
            case '5': // Program
                $query->whereHas('program', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
                break;
            case '6': // Subject
                $query->where('subject', 'like', "%$search%");
                break;
        }
    }

    // Count after filter
    $filteredCount = $query->count();

    // Order by latest created
    $query->orderByDesc('created_at');

    // Paginate
    $archives = $query->paginate(50)->withQueryString();

    return view('staff.staff_archive', compact('archives', 'totalArchives', 'filteredCount'));
}


public function getArchive($id)
{  
    $archive = Archive::with(['program'])->findOrFail($id);

    // Log the view
    if (auth()->check()) {
        ArchiveViewLog::create([
            'user_id' => auth()->id(),
            'archive_id' => $id,
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'archive_id' => $id,
            'event_type' => 'archive_view',
            'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " viewed archive '{$archive->archive_code}'.",
        ]);
    }

  
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
        'views'          => $archive->viewLogs()->count(),
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
}
