<?php

namespace App\Exports\Traits;

use Maatwebsite\Excel\Events\AfterSheet;

trait AddsExcelHeader
{
    abstract protected function reportTitle(): string;

    protected function headerLines(): array
    {
        return [
            "Tagum Doctor's College Inc.",
            'Learning and Information Resource Center',
            $this->reportTitle(),
        ];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            $headerLines = $this->headerLines();
            $sheet->insertNewRowBefore(1, count($headerLines));

            foreach ($headerLines as $index => $line) {
                $row = $index + 1;
                $sheet->setCellValue('A' . $row, $line);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getRowDimension($row)->setRowHeight(16);
            }

            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->mergeCells('A3:H3');

            $sheet->getStyle('A1:H3')->getFont()->setSize(12);

            // Set column widths for A, B, C, D
            $columnWidths = [
                'A' => 16,
                'B' => 50,
                'C' => 50,
                'D' => 50,
            ];

            foreach ($columnWidths as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }

            // Wrap text for columns B to D, starting at row 5 (actual data rows)
            $highestRow = $sheet->getHighestRow();
            $sheet->getStyle('B5:D' . $highestRow)->getAlignment()->setWrapText(true);

            // Set all data (A to H, from row 5) to middle (vertical center) alignment
            $sheet->getStyle('A5:H' . $highestRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        },
    ];
}
}
