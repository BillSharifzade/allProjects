<?php

namespace App\Exports;

use App\Models\Cashbox;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashboxReport implements FromView
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.cashbox_report', [
            'data' => $this->data
        ]);
    }
}
