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
use App\Models\ArchiveAccessRequest;

class AdminController extends Controller
{

    public function index() {

    $archiveCount = Archive::count();
    $requestCount = ArchiveAccessRequest::count();  
    $programCount = Program::count(); 
    $requestPendingCount = ArchiveAccessRequest::where('status', 'pending')->count();



    $verifiedPatronCount = User::where('status', 'verified')
    ->where('role', 'patron')
    ->count();

    $notVerifiedPatronCount = User::where('status', '!=', 'verified')
    ->where('role', 'patron')
    ->count();

    $userName = auth()->check() ? auth()->user()->last_name : 'Guest';
    $publishedArchiveCount   = Archive::where('status', 'Publish')->count();
    $unpublishedArchiveCount = Archive::where('status', '!=', 'Publish')->count();

    return view('admin.admin_index', compact('archiveCount','programCount','publishedArchiveCount','requestPendingCount','unpublishedArchiveCount','requestCount','userName','verifiedPatronCount', 'notVerifiedPatronCount'));
    }
}