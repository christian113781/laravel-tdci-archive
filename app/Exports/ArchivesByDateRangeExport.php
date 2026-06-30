<?php

namespace App\Exports;

use App\Exports\Traits\AddsExcelHeader;
use App\Models\Archive;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArchivesByDateRangeExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use AddsExcelHeader;

    protected $dateFrom;
    protected $dateTo;
    protected $programId;

    public function __construct($dateFrom, $dateTo, $programId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        // Cast programId to int if it's not null or empty string
        $this->programId = (!empty($programId) && $programId !== '') ? (int)$programId : null;
    }

    public function collection()
    {
        // Extract year from date range
        $year = Carbon::createFromFormat('Y-m-d', $this->dateFrom)->year;

        $query = Archive::with('keywords', 'program')
            ->where('year', $year);
        
        // Apply program filter if programId is provided
        if (!is_null($this->programId) && $this->programId > 0) {
            $query->where('program_id', $this->programId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    protected function reportTitle(): string
    {
        return 'Publication Inventory Report';
    }

    public function headings(): array
    {
        return [
            'Archive Code',
            'Title',
            'Authors',
            'Year',
            'Program',
            'Category',
            'Keywords',
            'Status',
            'Created Date',
        ];
    }

    public function map($archive): array
    {
        return [
            $archive->archive_code,
            $archive->title,
            $archive->authors,
            $archive->year,
            $archive->program?->name ?? 'N/A',
            $archive->category,
            $archive->keywords->pluck('name')->implode(', '),
            $archive->status,
            $archive->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
