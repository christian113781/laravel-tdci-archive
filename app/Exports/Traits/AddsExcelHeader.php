<?php

namespace App\Exports\Traits;

use Maatwebsite\Excel\Events\AfterSheet;

trait AddsExcelHeader
{
    abstract protected function reportTitle(): string;

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', $this->reportTitle());
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(20);
            },
        ];
    }
}
