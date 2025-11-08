<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArchivesByYearExport implements FromCollection, WithHeadings, WithMapping
{
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function collection()
    {
        return Archive::with('keywords')
            ->where('year', $this->year)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Archive Code',   // new column
            'Title',
            'Authors',
            'Year',
            'Category',
            'Keywords',
            'Status',
        ];
    }

    public function map($archive): array
    {
        return [
            $archive->archive_code,            // new field
            $archive->title,
            $archive->authors,
            $archive->year,
            $archive->category,
            $archive->keywords->pluck('name')->implode(', '),
            $archive->status,
        ];
    }
}
