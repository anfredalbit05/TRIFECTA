<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Context;
use Illuminate\Support\Facades\Auth;

class ContextEditor extends Component
{
    public bool $open = false;

    public string $contextKey = '';

    public string $details = '';

    #[On('open-context-editor')]
    public function open($key)
    {
        $context = Context::where('context_key', $key)->first();

        if (!$context) {
            return;
        }

        $this->contextKey = $context->context_key;
        $this->details = $context->details;

        $this->open = true;
    }

    public function save()
    {
        abort_unless(
            Auth::check() && 
            in_array(Auth::user()->RoleID, [1, 2]),
            403
        );

        Context::where(
            'context_key',
            $this->contextKey
        )->update([
            'details' => $this->details
        ]);

        $this->dispatch('context-updated');

        $this->open = false;
    }

    public function render()
    {
        return view('livewire.context-editor');
    }
}