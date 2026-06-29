<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Archive;
use App\Models\ArchiveAccessRequest;
use App\Models\Bookmark;
use App\Models\Program;
use App\Models\User;

class PatronController extends Controller
{
    public function index()
    {

        $archives = Archive::where('status', 'Publish')
            ->latest()
            ->limit(10)
            ->get();

        // Add this: fetch announcements
        $announcements = Announcement::latest()->get();

        $userId = auth()->id(); // Retrieves the authenticated user's I
        $archiveCount = Archive::count();
        $programCount = Program::count();
        $userId = auth()->id();
        $bookmarkCount = 0;

        if ($userId) {
            $bookmarkCount = Bookmark::where('user_id', $userId)->count();
        }

        $requestCount = ArchiveAccessRequest::where('user_id', $userId)->count();
        $requestPendingCount = ArchiveAccessRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $verifiedPatronCount = User::where('status', 'verified')
            ->where('role', 'patron')
            ->count();

        $notVerifiedPatronCount = User::where('status', '!=', 'verified')
            ->where('role', 'patron')
            ->count();

        $userName = auth()->check() ? auth()->user()->last_name : 'Guest';
        $publishedArchiveCount = Archive::where('status', 'Publish')->count();
        $unpublishedArchiveCount = Archive::where('status', '!=', 'Publish')->count();

        return view('patron.patron_index', compact('archives', 'archiveCount', 'programCount', 'publishedArchiveCount', 'requestPendingCount', 'unpublishedArchiveCount', 'requestCount', 'userName', 'verifiedPatronCount', 'notVerifiedPatronCount', 'announcements', 'bookmarkCount'));
    }
}
