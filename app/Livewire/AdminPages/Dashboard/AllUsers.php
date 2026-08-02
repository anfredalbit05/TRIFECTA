<?php

namespace App\Livewire\AdminPages\Dashboard;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AllUsers extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('contactno', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin-pages.dashboard.all-users', [
            'users' => $users,
        ]);
    }
}
