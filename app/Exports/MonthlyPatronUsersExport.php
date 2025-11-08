<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyPatronUsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;
    protected $year;

    public function __construct(int $month, int $year = null)
    {
        $this->month = $month;
        $this->year = $year ?: now()->year;
    }

    public function collection()
    {
        return User::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->where('role', 'patron')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Created At',
            // other columns if needed
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->created_at->toDateTimeString(),
        ];
    }
}
