<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class SelectYear extends Component
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
        $years = [];

        for ($i = date('Y'); $i > 1960; $i--) {
            $years[$i] = $i;
        }

        return view('components.forms.select-year', [
            'years' => $years,
            'name' => $this->name,
            'value' => $this->value,
        ]);
    }
}
