<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PatronLoginCountExport implements FromArray, WithHeadings, WithTitle
{
    protected $year;

    public function __construct(int $year = null)
    {
        $this->year = $year ?? now()->year;
    }

    public function array(): array
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        // Get total patron login sessions by month
        $patronLoginSessions = DB::table('user_login_sessions')
            ->join('users', 'user_login_sessions.user_id', '=', 'users.id')
            ->selectRaw('MONTH(user_login_sessions.login_at) as month, COUNT(*) as count')
            ->whereYear('user_login_sessions.login_at', $this->year)
            ->where('users.role', 'patron')
            ->groupBy('month')
            ->pluck('count', 'month');
        
        // Build array for export
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => $months[$i - 1],
                'login_count' => $patronLoginSessions->get($i, 0)
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Month',
            'Patron Login Count'
        ];
    }

    public function title(): string
    {
        return "Patron Logins {$this->year}";
    }
}
