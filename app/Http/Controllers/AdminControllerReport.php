<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TopViewsArchiveExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyPatronUsersExport;
use App\Exports\ArchivesByYearExport;

class AdminControllerReport extends Controller
{
    public function index (){
        return view('admin.admin_report');
    }
    /**
     * Export top 10 archives by views as an Excel file.
     */
    public function exportTop10ByViews()
    {
        $fileName = 'top10_archives_by_views_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new TopViewsArchiveExport, $fileName);
    }

    public function exportPatronsByMonth($month, $year = null)
    {
        // Validate month
        if ($month < 1 || $month > 12) {
            abort(404);
        }

        // Default year if not passed
        $year = $year ?: now()->year;

        $fileName = "patrons_{$year}_month_{$month}.xlsx";
        return Excel::download(new MonthlyPatronUsersExport($month, $year), $fileName);
    }



public function exportArchivesByYear($year)
{
    // validate year
    if ($year < 1900 || $year > now()->year) {
        abort(404);
    }
    $fileName = "archives_{$year}.xlsx";
    return Excel::download(new ArchivesByYearExport($year), $fileName);
}

}
