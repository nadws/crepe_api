<?php

namespace App\View\Components;

use Illuminate\View\Component;

class modal extends Component
{
    public $title, $idModal, $btnSave, $size;
    public function __construct($title,$idModal = null, $btnSave = '', $size = '')
    {
        $this->title = $title;
        $this->idModal = $idModal;
        $this->btnSave = $btnSave;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
