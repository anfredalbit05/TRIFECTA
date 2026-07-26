<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EditableText extends Component
{
    public string $key;

    public string $value;

    public string $tag;

    public string $class;

    public function __construct(
        $key,
        $value='',
        $tag='div',
        $class=''
    ){

        $this->key=$key;
        $this->value=$value;
        $this->tag=$tag;
        $this->class=$class;

    }

    public function render()
    {
        return view('components.editable-text');
    }
}
