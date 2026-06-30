<?php

use App\Exports\ProgramViewsExport;
use Maatwebsite\Excel\Events\AfterSheet;

class FakeStyle
{
    public $font;
    public $alignment;

    public function __construct()
    {
        $this->font = new class {
            public function setBold($value) { return $this; }
            public function setSize($value) { return $this; }
        };
        $this->alignment = new class {
            public function setHorizontal($value) { return $this; }
        };
    }

    public function getFont()
    {
        return $this->font;
    }

    public function getAlignment()
    {
        return $this->alignment;
    }
}

class FakeRowDimension
{
    public $height;

    public function setRowHeight($height)
    {
        $this->height = $height;
    }
}

class FakeDelegate
{
    public array $values = [];
    public array $styles = [];
    public array $rowDimensions = [];
    public int $insertedRows = 0;

    public function insertNewRowBefore($index, $count)
    {
        $this->insertedRows += $count;
    }

    public function setCellValue($coordinate, $value)
    {
        $this->values[$coordinate] = $value;
    }

    public function getStyle($coordinate)
    {
        $this->styles[$coordinate] = new FakeStyle();

        return $this->styles[$coordinate];
    }

    public function getRowDimension($row)
    {
        $this->rowDimensions[$row] = new FakeRowDimension();

        return $this->rowDimensions[$row];
    }
}

class FakeSheet
{
    protected FakeDelegate $delegate;

    public function __construct()
    {
        $this->delegate = new FakeDelegate();
    }

    public function getDelegate()
    {
        return $this->delegate;
    }
}

it('adds a report header row to the exported sheet', function () {
    $export = new ProgramViewsExport();
    $events = $export->registerEvents();

    $sheet = new FakeSheet();
    $event = new class($sheet) extends AfterSheet {
        public function __construct($sheet)
        {
            $this->sheet = $sheet;
        }
    };

    $events[AfterSheet::class]($event);
    $delegate = $sheet->getDelegate();

    expect($delegate->insertedRows)->toBe(1);
    expect($delegate->values['A1'])->toBe('Program Views Report');
    expect($delegate->rowDimensions[1]->height)->toBe(20);
});
