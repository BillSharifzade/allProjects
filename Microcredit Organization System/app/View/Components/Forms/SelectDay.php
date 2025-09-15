<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class SelectDay extends Component
{
    public $name;
    public $value;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $days = [];

        for($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }

        return view('components.forms.select-day', [
            'days' => $days,
            'name' => $this->name,
            'value' => $this->value,
        ]);
    }
}
