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
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class MonthlyReport implements FromView, WithStyles, WithEvents, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.monthly', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A:K')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                try { $event->sheet->getDelegate()->freezePane('A3'); } catch (\Throwable $e) {}
                // Attach expense notes as cell comments where available
                try {
                    $notes = $this->data['expenseNotesByRow'] ?? [];
                    foreach ($notes as $row => $text) {
                        if ($text === '') { continue; }
                        $cell = 'E' . (int)$row; // Expenses column
                        $comment = $event->sheet->getDelegate()->getComment($cell);
                        $comment->getText()->createTextRun($text);
                    }
                } catch (\Throwable $e) {}
            },
        ];
    }

    public function columnWidths(): array
    {
        return [ 'A' => 16, 'B' => 16, 'C' => 16, 'D' => 16, 'E' => 16, 'F' => 18, 'G' => 16, 'H' => 16, 'I' => 16, 'J' => 16, 'K' => 16 ];
    }
}


