<?php

namespace App\Exports;

use App\Models\Archive;
use App\Models\ArchiveViewLog;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArchiveViewLogsExport implements FromCollection, WithHeadings
{
    protected $dateFrom;
    protected $dateTo;
    protected $programId;

    public function __construct($dateFrom = null, $dateTo = null, $programId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->programId = $programId;
    }

    public function collection()
    {
        // Query archive_view_logs grouped by archive_id
        $query = DB::table('archive_view_logs')
            ->selectRaw('
                archives.archive_code,
                archives.title,
                archives.authors,
                programs.name as program,
                COUNT(archive_view_logs.id) as view_count
            ')
            ->join('archives', 'archive_view_logs.archive_id', '=', 'archives.id')
            ->leftJoin('programs', 'archives.program_id', '=', 'programs.id')
            ->groupBy('archives.archive_code', 'archives.title', 'archives.authors', 'programs.name');

        // Apply date range filter based on archive_view_logs.created_at
        if ($this->dateFrom && $this->dateTo) {
            $query->whereBetween(DB::raw('DATE(archive_view_logs.created_at)'), [
                $this->dateFrom,
                $this->dateTo
            ]);
        }

        // Apply program filter if specified
        if ($this->programId) {
            $query->where('archives.program_id', $this->programId);
        }

        // Order by view count (descending) and get top 10
        $results = $query
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();

        // Format results for export
        $formattedData = collect();
        foreach ($results as $row) {
            $formattedData->push([
                $row->archive_code,
                $row->title,
                $row->authors ?? 'N/A',
                $row->program ?? 'N/A',
                $row->view_count,
            ]);
        }

        return $formattedData;
    }

    public function headings(): array
    {
        return [
            'Archive Code',
            'Title',
            'Author',
            'Program',
            'Views',
        ];
    }
}
