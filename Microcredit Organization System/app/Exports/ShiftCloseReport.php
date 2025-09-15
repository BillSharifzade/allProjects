<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ShiftCloseReport implements FromView, WithStyles, WithEvents, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.shift_close', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        // Right-align numeric columns (B, D, F)
        $sheet->getStyle('B:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A:F')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Freeze top rows after header; fallback safe for older versions
                try {
                    $event->sheet->getDelegate()->freezePane('A5');
                } catch (\Throwable $e) {
                    try { $event->sheet->getDelegate()->freezePaneByColumnAndRow(1,5); } catch (\Throwable $e2) {}
                }
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 18,
            'C' => 28,
            'D' => 18,
            'E' => 28,
            'F' => 18,
        ];
    }

}


