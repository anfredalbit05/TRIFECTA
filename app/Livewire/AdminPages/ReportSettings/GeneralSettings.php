<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Setting;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GeneralSettings extends Component
{
    public $isOpen = false;
    public $setting_id;

    // Form fields matching your schema
    public $name;
    public $value;

    protected $rules = [
        'name' => 'required|string|max:100',
        'value' => 'nullable|string',
    ];

    // Helper to check permissions dynamically
    protected function hasPermission($action)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->RoleID) {
            return false;
        }

        // Super Admin (RoleID 1) bypasses checks
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $module = Module::where('modulename', 'Report & Settings - General')->first(); // Adjust modulename if needed

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

    // Computed properties for permissions in Blade
    public function getCanCreateProperty()
    {
        return $this->hasPermission('create');
    }

    public function getCanReadProperty()
    {
        return $this->hasPermission('read');
    }

    public function getCanUpdateProperty()
    {
        return $this->hasPermission('update');
    }

    public function getCanDeleteProperty()
    {
        return $this->hasPermission('delete');
    }

    public function openCreateModal()
    {
        if (!$this->canCreate) {
            session()->flash('message', 'Unauthorized action.');
            return;
        }

        $this->resetValidation();
        $this->reset(['name', 'value', 'setting_id']);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->reset(['name', 'value', 'setting_id']);
    }

    public function editSetting($id)
    {
        if (!$this->canUpdate) {
            session()->flash('message', 'Unauthorized action.');
            return;
        }

        $this->resetValidation();
        $setting = Setting::findOrFail($id);

        $this->setting_id = $setting->SettingsID;
        $this->name = $setting->name;
        $this->value = $setting->value;

        $this->isOpen = true;
    }

    public function saveSetting()
    {
        if ($this->setting_id && !$this->canUpdate) {
            return;
        }
        if (!$this->setting_id && !$this->canCreate) {
            return;
        }

        $this->validate();

        if ($this->setting_id) {
            $setting = Setting::findOrFail($this->setting_id);
            $setting->update([
                'name' => $this->name,
                'value' => $this->value,
            ]);
            $message = 'Setting successfully updated!';
        } else {
            Setting::create([
                'name' => $this->name,
                'value' => $this->value,
            ]);
            $message = 'Setting successfully created!';
        }

        $this->closeModal();
        session()->flash('message', $message);
    }

    public function confirmDeleteSetting($id)
    {
        if (!$this->canDelete) {
            return;
        }
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteSetting($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $setting = Setting::findOrFail($id);
        $setting->delete();

        $this->dispatch('setting-deleted');
    }

    public function render()
    {
        return view('components.admin-pages.report-settings.general', [
            'settings' => Setting::latest('SettingsID')->get()
        ]);
    }
}
