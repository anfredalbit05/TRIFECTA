<?php

namespace App\Livewire\AdminPages\Post;

use App\Models\Module;
use App\Models\Privilege;
use App\Models\User;
use App\Models\Testimonial;
use App\Models\NewsUpdate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Overview extends Component
{
    public function refreshOverview()
    {
        session()->flash('message', 'Overview data refreshed successfully.');
    }

    protected function hasPermission($action)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->RoleID) {
            return false;
        }

        if ((int) $user->RoleID === 1) {
            return true;
        }

        $module = Module::where('modulename', 'Overview')->first();

        if (!$module) {
            return false;
        }

        $privilege = Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', $module->ModuleID)
            ->first();

        if (!$privilege) {
            return false;
        }

        return match ($action) {
            'create' => (bool) $privilege->can_create,
            'read'   => (bool) $privilege->can_read,
            'update' => (bool) $privilege->can_update,
            'delete' => (bool) $privilege->can_delete,
            default  => false,
        };
    }

    public function getCanCreateProperty() { return $this->hasPermission('create'); }
    public function getCanReadProperty() { return $this->hasPermission('read'); }
    public function getCanUpdateProperty() { return $this->hasPermission('update'); }
    public function getCanDeleteProperty() { return $this->hasPermission('delete'); }

    public function render()
    {
        return view('components.admin-pages.post.overview', [
            'canCreate'         => $this->canCreate,
            'canRead'           => $this->canRead,
            'canUpdate'         => $this->canUpdate,
            'canDelete'         => $this->canDelete,
            'totalTestimonials' => Testimonial::where('status', 'Published')->count(),
            'totalNews'         => NewsUpdate::where('status', 'Published')->count(),
        ]);
    }
}
