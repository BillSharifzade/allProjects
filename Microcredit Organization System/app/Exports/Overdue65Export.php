<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Overdue65Export implements FromView
{
    protected $rows;
    protected $title;
    protected $totals;
    protected $groupTotals;

    public function __construct($rows, $title, $totals = [], $groupTotals = [])
    {
        $this->rows = $rows;
        $this->title = $title;
        $this->totals = $totals;
        $this->groupTotals = $groupTotals;
    }

    public function view(): View
    {
        return view('exports.overdue65', [
            'rows' => $this->rows,
            'title' => $this->title,
            'totals' => $this->totals,
            'groupTotals' => $this->groupTotals,
        ]);
    }
}


