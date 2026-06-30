<?php

namespace App\Exports;

use App\Exports\Traits\AddsExcelHeader;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyLoginStatsExport implements FromArray, WithHeadings, WithTitle, WithEvents
{
    use AddsExcelHeader;

    protected $year;

    public function __construct(int $year = null)
    {
        $this->year = $year ?? now()->year;
    }

    public function array(): array
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        // Get unique patron users by month
        $patronLogins = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, COUNT(DISTINCT user_login_sessions.user_id) as count')
            ->whereYear('user_login_sessions.login_at', $this->year)
            ->where('users.role', 'patron')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Get unique staff users by month
        $staffLogins = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, COUNT(DISTINCT user_login_sessions.user_id) as count')
            ->whereYear('user_login_sessions.login_at', $this->year)
            ->where('users.role', 'staff')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Build array for export
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => $months[$i - 1],
                'patron_users' => $patronLogins->get($i, 0),
                'staff_users' => $staffLogins->get($i, 0),
                'total_users' => ($patronLogins->get($i, 0) + $staffLogins->get($i, 0))
            ];
        }
        
        return $data;
    }

    protected function reportTitle(): string
    {
        return 'Monthly Login Stats Report';
    }

    public function headings(): array
    {
        return [
            'Month',
            'Patron Users',
            'Staff Users',
            'Total Users'
        ];
    }

    public function title(): string
    {
        return "Login Stats {$this->year}";
    }
}
