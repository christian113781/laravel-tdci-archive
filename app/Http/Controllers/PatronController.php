<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Filesystem\FileException;
use Exception;
use App\Models\Program;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\Archive;
use App\Models\Announcement;
use App\Models\Bookmark;

use App\Models\ArchiveAccessRequest;
use Illuminate\Support\Facades\Auth;


class PatronController extends Controller
{

    public function index() {

   $archives = Archive::latest()       
                ->paginate(5);   

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
    $publishedArchiveCount   = Archive::where('status', 'Publish')->count();
    $unpublishedArchiveCount = Archive::where('status', '!=', 'Publish')->count();

    return view('patron.patron_index', compact('archives','archiveCount','programCount','publishedArchiveCount','requestPendingCount','unpublishedArchiveCount','requestCount','userName','verifiedPatronCount', 'notVerifiedPatronCount','announcements','bookmarkCount'));
    }
}