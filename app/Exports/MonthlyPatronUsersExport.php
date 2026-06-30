<?php

namespace App\Exports;

use App\Exports\Traits\AddsExcelHeader;
use App\Models\User;
use App\Models\UserLoginSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyPatronUsersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use AddsExcelHeader;

    protected $month;
    protected $year;

    public function __construct(int $month, int $year = null)
    {
        $this->month = $month;
        $this->year = $year ?: now()->year;
    }

    public function collection()
    {
        $users = User::where('role', 'patron')
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->with('loginSessions')
            ->get();

        // Filter login sessions after retrieval for better compatibility
        $users->each(function ($user) {
            $user->loginSessions = $user->loginSessions
                ->filter(function ($session) {
                    return $session->login_at->month == $this->month 
                        && $session->login_at->year == $this->year;
                })
                ->sortByDesc('login_at')
                ->values();
        });

        return $users;
    }

    protected function reportTitle(): string
    {
        return 'Monthly Patron Users Report';
    }

    public function headings(): array
    {
        return [
            'User ID',
            'First Name',
            'Last Name',
            'Email',
            'Account Created At',
            'Total Logins (This Month)',
            'Last Login (This Month)',
        ];
    }

    public function map($user): array
    {
            // Count logins for this month
            $loginCount = $user->loginSessions->count();
        
        // Get last login for this month
        $lastLogin = $user->loginSessions->first();
        $lastLoginDate = $lastLogin ? $lastLogin->login_at->toDateTimeString() : 'No logins';

        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->created_at->toDateTimeString(),
            $loginCount,
            $lastLoginDate,
        ];
    }
    }


