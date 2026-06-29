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
    /**
     * Get monthly login statistics grouped by user role (count of unique users)
     */
    private function getMonthlyLoginStats($year = null)
    {
        $year = $year ?? now()->year;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Get unique patron users by month
        $patronLogins = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, COUNT(DISTINCT user_login_sessions.user_id) as count')
            ->whereYear('user_login_sessions.login_at', $year)
            ->where('users.role', 'patron')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Get unique staff users by month
        $staffLogins = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, COUNT(DISTINCT user_login_sessions.user_id) as count')
            ->whereYear('user_login_sessions.login_at', $year)
            ->where('users.role', 'staff')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Build arrays for all 12 months
        $patronData = [];
        $staffData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $patronData[] = $patronLogins->get($i, 0);
            $staffData[] = $staffLogins->get($i, 0);
        }
        
        return [
            'months' => $months,
            'patronLogins' => $patronData,
            'staffLogins' => $staffData
        ];
    }

    /**
     * Get total patron login count per month (all login sessions, not unique users)
     */
    private function getPatronLoginCount($year = null)
    {
        $year = $year ?? now()->year;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Get total patron login sessions by month
        $patronLoginSessions = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, COUNT(*) as count')
            ->whereYear('user_login_sessions.login_at', $year)
            ->where('users.role', 'patron')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Build array for all 12 months
        $patronData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $patronData[] = $patronLoginSessions->get($i, 0);
        }
        
        return [
            'months' => $months,
            'patronLoginCount' => $patronData
        ];
    }

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
    
    // Get latest 10 published archives
    $archives = Archive::where('status', 'Publish')
        ->latest()
        ->limit(10)
        ->get();
    
    // Get top 10 most viewed archives (only those with views)
    $mostViewedArchives = Archive::where('status', 'Publish')
        ->withCount('viewLogs')
        ->having('view_logs_count', '>', 0)
        ->orderBy('view_logs_count', 'desc')
        ->limit(10)
        ->get();
    
    // Get all programs for dropdown
    $programs = Program::all();
     $userCount = User::count();
    
    // Get total views per program (count from archive_view_logs)
    $viewsData = DB::table('programs')
        ->selectRaw('programs.id, programs.name, COALESCE(COUNT(archive_view_logs.id), 0) as total_views')
        ->leftJoin('archives', 'programs.id', '=', 'archives.program_id')
        ->leftJoin('archive_view_logs', 'archives.id', '=', 'archive_view_logs.archive_id')
        ->groupBy('programs.id', 'programs.name')
        ->orderBy('total_views', 'desc')
        ->get();
    
    // Ensure we have data, even if empty
    if ($viewsData->isEmpty()) {
        // Get all programs with 0 views if no data exists
        $viewsData = Program::select('id', 'name')
            ->get()
            ->map(function ($program) {
                $program->total_views = 0;
                return $program;
            });
    }
    
    // Format data for chart
    $programNames = $viewsData->pluck('name')->toArray();
    $viewCounts = $viewsData->pluck('total_views')->toArray();
    
    $viewsPerProgram = [
        'names' => $programNames,
        'views' => $viewCounts,
        'data' => $viewsData
    ];
    
    // Get monthly login statistics
    $monthlyStats = $this->getMonthlyLoginStats();
    $patronLoginStats = $this->getPatronLoginCount();

    return view('admin.admin_index', compact('userCount','archiveCount','programCount','publishedArchiveCount','requestPendingCount','unpublishedArchiveCount','requestCount','userName','verifiedPatronCount', 'notVerifiedPatronCount', 'monthlyStats', 'patronLoginStats', 'archives', 'programs', 'viewsPerProgram', 'mostViewedArchives'));
    }

    /**
     * Get archives by program (AJAX endpoint)
     */
    public function getArchivesByProgram($programId)
    {
        $archives = Archive::where('status', 'Publish')
            ->where('program_id', $programId)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'archives' => $archives->map(function ($archive) {
                return [
                    'id' => $archive->id,
                    'title' => $archive->title,
                    'authors' => $archive->authors,
                    'year' => $archive->year,
                    'subject' => \Illuminate\Support\Str::limit($archive->subject, 200),
                    'keywords' => $archive->keywords->pluck('name')->implode(', '),
                ];
            })
        ]);
    }

    /**
     * Get views by month for a specific program
     * Uses archive access requests as a proxy for views
     */
    public function getViewsByProgram($programId)
    {
        $year = now()->year;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Get access requests (views) count per month for archives in the program
        $viewsData = DB::table('archive_access_requests')
            ->join('archives', 'archive_access_requests.archive_id', '=', 'archives.id')
            ->selectRaw('MONTH(archive_access_requests.created_at) as month, COUNT(*) as count')
            ->whereYear('archive_access_requests.created_at', $year)
            ->where('archives.program_id', $programId)
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Build array for all 12 months
        $monthlyViews = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyViews[] = $viewsData->get($i, 0);
        }
        
        return response()->json([
            'success' => true,
            'months' => $months,
            'views' => $monthlyViews
        ]);
    }
}