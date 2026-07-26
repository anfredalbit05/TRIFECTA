<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class Members extends Component
{
    public $search = '';

    public function render()
    {
        $members = User::where('RoleID', 2)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('firstname', 'like', '%' . $this->search . '%')
                      ->orWhere('middlename', 'like', '%' . $this->search . '%')
                      ->orWhere('lastname', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->get();

        return view('components.member-pages.masterlist.members', [
            'members' => $members,
        ]);
    }
}
