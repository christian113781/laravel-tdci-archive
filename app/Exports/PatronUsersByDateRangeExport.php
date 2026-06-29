<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatronUsersByDateRangeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom, $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $from = Carbon::createFromFormat('Y-m-d', $this->dateFrom)->startOfDay();
        $to = Carbon::createFromFormat('Y-m-d', $this->dateTo)->endOfDay();

        // Get only patrons (not staff) with logins in the date range
        $users = User::where('role', 'patron')
            ->with(['loginSessions' => function ($query) use ($from, $to) {
                $query->whereBetween('login_at', [$from, $to]);
            }])
            ->get();

        // Filter to only include users who have logins in the date range
        $users = $users->filter(function ($user) {
            return $user->loginSessions->count() > 0;
        })->values();

        // Sort by total login count in descending order (highest to lowest)
        $users = $users->sortByDesc(function ($user) {
            return $user->loginSessions->count();
        })->values();

        return $users;
    }

    public function headings(): array
    {
        return [
            'User ID',
            'First Name',
            'Last Name',
            'Email',
            'Role',
            'Account Created At',
            'Total Logins (Date Range)',
            'Last Login (Date Range)',
        ];
    }

    public function map($user): array
    {
        // Count logins in the date range
        $loginCount = $user->loginSessions->count();
        
        // Get last login in the date range
        $lastLogin = $user->loginSessions->sortByDesc('login_at')->first();
        $lastLoginDate = $lastLogin ? $lastLogin->login_at->toDateTimeString() : 'No logins';

        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->email,
            ucfirst($user->role),
            $user->created_at->toDateTimeString(),
            $loginCount,
            $lastLoginDate,
        ];
    }
}
