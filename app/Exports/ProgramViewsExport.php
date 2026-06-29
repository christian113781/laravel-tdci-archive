<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProgramViewsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $results = DB::table('programs')
            ->selectRaw('programs.name as program_name, COALESCE(COUNT(archive_view_logs.id), 0) as total_views')
            ->leftJoin('archives', 'programs.id', '=', 'archives.program_id')
            ->leftJoin('archive_view_logs', 'archives.id', '=', 'archive_view_logs.archive_id')
            ->groupBy('programs.id', 'programs.name')
            ->orderByDesc('total_views')
            ->get()
            ->map(function ($row) {
                return [
                    'program_name' => $row->program_name,
                    'total_views' => $row->total_views,
                ];
            });

        return $results;
    }

    public function headings(): array
    {
        return [
            'Program Name',
            'Overall Views',
        ];
    }
}
