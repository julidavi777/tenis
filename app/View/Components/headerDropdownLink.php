<?php

namespace App\View\Components;

use Illuminate\View\Component;

class headerDropdownLink extends Component
{
    public $iconClass;
    public $href;
    public $text;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($iconClass, $href, $text)
    {
        $this->iconClass = $iconClass;
        $this->href = $href;
        $this->text = $text;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.header-dropdown-link');
    }
}