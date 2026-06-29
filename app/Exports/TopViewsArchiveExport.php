<?php

namespace App\Exports;

use App\Models\Archive;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopViewsArchiveExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateTo;
    protected $programId;

    public function __construct($dateFrom = null, $dateTo = null, $programId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        // Cast programId to int if it's not null or empty string
        $this->programId = (!empty($programId) && $programId !== '') ? (int)$programId : null;
    }

    /**
     * Return the collection of data to be exported.
     * Count views only from the selected date range.
     */
    public function collection()
    {
        $from = null;
        $to = null;

        // Parse dates if provided
        if ($this->dateFrom && $this->dateTo) {
            $from = Carbon::createFromFormat('Y-m-d', $this->dateFrom)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $this->dateTo)->endOfDay();
        }

        // Subquery to count views in the date range
        $viewCountSubquery = DB::table('archive_access_requests')
            ->selectRaw('archive_id, COUNT(*) as view_count');

        if ($from && $to) {
            $viewCountSubquery->whereBetween('created_at', [$from, $to]);
        }

        $viewCountSubquery->groupBy('archive_id');

        $query = Archive::with('keywords', 'program')
            ->select('archives.*', 
                DB::raw('COALESCE(view_counts.view_count, 0) as views_in_range')
            )
            ->leftJoinSub($viewCountSubquery, 'view_counts', function ($join) {
                $join->on('archives.id', '=', 'view_counts.archive_id');
            });

        // Filter by program if provided
        if (!is_null($this->programId) && $this->programId > 0) {
            $query->where('archives.program_id', $this->programId);
        }

        // Order by view count in range (descending) and take top 10
        $query->orderBy('views_in_range', 'desc')
              ->take(10);

        return $query->get();
    }

    /**
     * The headings for the columns in Excel.
     */
    public function headings(): array
    {
        return [
            'Archive Code',
            'Title',
            'Authors',
            'Year',
            'Category',
            'Program',
            'Views (In Date Range)',
            'Keywords',
            'Status',
        ];
    }

    /**
     * Map each archive record to a row for Excel.
     */
    public function map($archive): array
    {
        return [
            $archive->archive_code,
            $archive->title,
            $archive->authors,
            $archive->year,
            $archive->category,
            $archive->program ? $archive->program->name : 'N/A',
            $archive->views_in_range ?? 0,
            $archive->keywords->pluck('name')->implode(', '),
            $archive->status,
        ];
    }
}
