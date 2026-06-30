<?php

namespace App\Exports;

use App\Exports\Traits\AddsExcelHeader;
use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArchivesByYearExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use AddsExcelHeader;

    protected $year;
    protected $programId;

    public function __construct(int $year, $programId = null)
    {
        $this->year = $year;
        // Cast programId to int if it's not null or empty string
        $this->programId = (!empty($programId) && $programId !== '') ? (int)$programId : null;
    }

    public function collection()
    {
        $query = Archive::with('keywords', 'program')
            ->where('year', $this->year);
        
        // Apply program filter if programId is provided
        if (!is_null($this->programId) && $this->programId > 0) {
            $query->where('program_id', $this->programId);
        }
        
        return $query->get();
    }

    protected function reportTitle(): string
    {
        return 'Archives By Year Report';
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
        ];
    }
}
