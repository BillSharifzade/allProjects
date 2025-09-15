<?php

namespace App\Widgets;

use App\Models\Cashbox;
use Arrilot\Widgets\AbstractWidget;

class CashboxFilter extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'cashbox' => true,
        'audit' => true,
        'closed' => true,
        'from' => true,
        'to' => true
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $cashboxes = [];

        foreach(Cashbox::get() as $cashbox) {
            $cashboxes[$cashbox->id] = $cashbox->name . ' (' . $cashbox->nickname . ')';
        }

        return view('widgets.cashbox_filter', [
            'config' => $this->config,
            'cashboxes' => $cashboxes,
        ]);
    }
}
