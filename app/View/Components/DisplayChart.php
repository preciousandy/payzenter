<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DisplayChart extends Component
{
    public $id;
    /**
     * Create a new component instance.
     */
    public function __construct(public $chartLabels = [], public $chartData = [], public $title = "Data")
    {
        $this->id = generateRandomId('chart');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.display-chart');
    }
}
