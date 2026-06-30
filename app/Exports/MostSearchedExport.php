<?php

namespace App\Exports;

use App\Exports\Traits\AddsExcelHeader;
use App\Models\SearchLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MostSearchedExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use AddsExcelHeader;

    protected $month;
    protected $year;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($month = null, $year = null, $dateFrom = null, $dateTo = null)
    {
        $this->month = $month ?: now()->month;
        $this->year = $year ?: now()->year;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $query = SearchLog::select('search_term')
            ->selectRaw('COUNT(*) as search_count')
            ->groupBy('search_term')
            ->orderByDesc('search_count');

        // Use date range if provided
        if ($this->dateFrom && $this->dateTo) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $this->dateFrom)->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $this->dateTo)->endOfDay();
                $query->whereBetween('created_at', [$from, $to]);
            } catch (\Exception $e) {
                // Fallback to month/year if date parsing fails
                $query->whereMonth('created_at', $this->month)
                    ->whereYear('created_at', $this->year);
            }
        } else {
            // Use month/year filter
            $query->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year);
        }

        return $query->limit(50)->get();
    }

    protected function reportTitle(): string
    {
        return 'Most Searched Report';
    }

    public function headings(): array
    {
        return [
            'Search Term',
            'Total Searches',
        ];
    }

    public function map($item): array
    {
        return [
            $item->search_term,
            $item->search_count,
        ];
    }
}
