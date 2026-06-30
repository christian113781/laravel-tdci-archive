<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ArchiveViewLogsExport;
use App\Exports\ProgramViewsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyPatronUsersExport;
use App\Exports\ArchivesByYearExport;
use App\Exports\ArchivesByDateRangeExport;
use App\Exports\MonthlyLoginStatsExport;
use App\Exports\PatronLoginCountExport;
use App\Exports\PatronUsersByDateRangeExport;
use Illuminate\Support\Facades\DB;

class AdminControllerReport extends Controller
{
    public function index (){
        return view('admin.admin_report');
    }
    
    /**
     * Get monthly login statistics grouped by user role (count of unique users)
     */
    public function getMonthlyLoginStats($year = null)
    {
        $year = $year ?? now()->year;
        
        $stats = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, users.role, COUNT(DISTINCT user_login_sessions.user_id) as count')
            ->whereYear('user_login_sessions.login_at', $year)
            ->groupBy('month', 'users.role')
            ->get();
        
        return $stats;
    }
    
    /**
     * Export monthly login statistics as an Excel file.
     */
    public function exportMonthlyLoginStats($year = null)
    {
        $year = $year ?? now()->year;
        $fileName = 'monthly_login_stats_' . $year . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new MonthlyLoginStatsExport($year), $fileName);
    }
    
    /**
     * Export patron login count as an Excel file.
     */
    public function exportPatronLoginCount($year = null)
    {
        $year = $year ?? now()->year;
        $fileName = 'patron_login_count_' . $year . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PatronLoginCountExport($year), $fileName);
    }
    
    /**
     * Export top 10 archives by views as an Excel file.
     * Supports filtering by date range and program.
     */
    public function exportTop10ByViews(Request $request)
    {
        // Get and validate date parameters (optional)
        $dateFrom = $request->query('from');
        $dateTo = $request->query('to');
        $programId = $request->query('program');

        // Validate dates if provided
        if ($dateFrom && $dateTo) {
            try {
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo);
            } catch (\Exception $e) {
                abort(400, 'Invalid date format. Use YYYY-MM-DD');
            }

            // Validate date range
            if ($from->isAfter($to)) {
                abort(400, 'From date must be before or equal to To date');
            }
        }

        $fileName = 'top10_archives_by_views';
        if ($dateFrom && $dateTo) {
            $fileName .= "_{$dateFrom}_to_{$dateTo}";
        }
        if ($programId) {
            $fileName .= "_program_{$programId}";
        }
        $fileName .= '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ArchiveViewLogsExport($dateFrom, $dateTo, $programId), $fileName);
    }

    public function exportProgramViewsByProgram()
    {
        $fileName = 'program_views_overall_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ProgramViewsExport(), $fileName);
    }

    public function exportPatronsByMonth($month, $year = null)
    {
        // Validate month
        if ($month < 1 || $month > 12) {
            abort(404);
        }

        // Default year if not passed
        $year = $year ?: now()->year;

        $fileName = "patrons_report_{$year}_month_{$month}.xlsx";
        return Excel::download(new MonthlyPatronUsersExport($month, $year), $fileName);
    }

    /**
     * Export patrons and staff by date range as an Excel file.
     */
    public function exportPatronsByDateRange(Request $request)
    {
        // Get and validate date parameters
        $dateFrom = $request->query('from');
        $dateTo = $request->query('to');

        // Validate dates
        if (!$dateFrom || !$dateTo) {
            abort(400, 'Date range parameters are required');
        }

        try {
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
            $to = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo);
        } catch (\Exception $e) {
            abort(400, 'Invalid date format. Use YYYY-MM-DD');
        }

        // Validate date range
        if ($from->isAfter($to)) {
            abort(400, 'From date must be before or equal to To date');
        }

        $fileName = "patrons_report_{$dateFrom}_to_{$dateTo}.xlsx";
        return Excel::download(new PatronUsersByDateRangeExport($dateFrom, $dateTo), $fileName);
    }


public function exportArchivesByYear($year, Request $request)
{
    // validate year
    if ($year < 1900 || $year > now()->year) {
        abort(404);
    }
    
    $programId = $request->query('program');
    
    $fileName = $programId 
        ? "archives_{$year}_program_{$programId}.xlsx"
        : "archives_{$year}.xlsx";
    
    return Excel::download(new ArchivesByYearExport($year, $programId), $fileName);
}

public function exportArchivesByDateRange(Request $request)
{
    // Get and validate date parameters
    $dateFrom = $request->query('from');
    $dateTo = $request->query('to');
    $programId = $request->query('program');
    
    // Validate dates
    if (!$dateFrom || !$dateTo) {
        abort(400, 'Date range parameters are required');
    }
    
    try {
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo);
    } catch (\Exception $e) {
        abort(400, 'Invalid date format. Use YYYY-MM-DD');
    }
    
    // Validate date range
    if ($from->isAfter($to)) {
        abort(400, 'From date must be before or equal to To date');
    }
    
    $fileName = $programId 
        ? "publication_inventory_{$dateFrom}_to_{$dateTo}_program_{$programId}.xlsx"
        : "publication_inventory_{$dateFrom}_to_{$dateTo}.xlsx";
    
    return Excel::download(new ArchivesByDateRangeExport($dateFrom, $dateTo, $programId), $fileName);
}

}

