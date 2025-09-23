<?php

namespace App\Http\Controllers;

use App\Models\ArchiveAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatronControllerRequest extends Controller
{
    public function index()
    {
        $userId = Auth::id();  // get the id of the logged in user

        // Eager load archive with program & keywords, filter by user_id
        $requests = ArchiveAccessRequest::with([
                'archive.program',
                'archive.keywords'
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('patron.patron_archive_request', compact('requests'));
    }

     public function destroy($id)
    {
        $userId = Auth::id();

        $requestEntry = ArchiveAccessRequest::where('id', $id)
                          ->where('user_id', $userId)  // ensure the user owns this request
                          ->firstOrFail();

        $requestEntry->delete();

        return redirect()->back()->with('success', 'Request deleted successfully.');
    }
}
