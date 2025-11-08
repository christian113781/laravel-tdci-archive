<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopViewsArchiveExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Return the collection of data to be exported.
     */
    public function collection()
    {
        // Get top 10 archives by views, eager-load keywords
        return Archive::with('keywords')
            ->orderBy('views', 'desc')
            ->take(10)
            ->get();
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
            'Views',
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
            $archive->views,
            $archive->keywords->pluck('name')->implode(', '),
            $archive->status,
        ];
    }
}
