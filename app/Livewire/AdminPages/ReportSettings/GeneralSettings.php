<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Setting;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GeneralSettings extends Component
{

     use WithPagination;
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

    // Check which required fields are empty
    $emptyFields = [];

    if (empty(trim($this->name))) {
        $emptyFields[] = 'Setting Name';
    }

    if (empty(trim($this->value))) {
        $emptyFields[] = 'Value';
    }

    if (!empty($emptyFields)) {
        if (count($emptyFields) > 1) {
            $text = 'The following fields are empty:<br><br><b>• ' . implode('</b><br><b>• ', $emptyFields) . '</b>';
        } else {
            $text = 'The <b>' . $emptyFields[0] . '</b> field is empty.';
        }

        $this->dispatch('swal:error', [
            'title' => 'Missing Fields',
            'text' => $text
        ]);
        return;
    }

    // Run standard validation for max length, etc.
    $this->validate();

    // Check for existing setting name (ignoring current ID if updating)
    $existingQuery = Setting::where('name', $this->name);
    if ($this->setting_id) {
        $existingQuery->where('SettingsID', '!=', $this->setting_id);
    }

    if ($existingQuery->exists()) {
        $this->dispatch('swal:error', [
            'title' => 'Duplicate Error',
            'text' => 'A setting with this name already exists.'
        ]);
        return;
    }

    // If updating, trigger SweetAlert confirmation first
    if ($this->setting_id) {
        $this->dispatch('confirm-update');
        return;
    }

    // Otherwise, create new record
    Setting::create([
        'name' => $this->name,
        'value' => $this->value,
    ]);

    $this->closeModal();

    $this->dispatch('swal:success', [
        'title' => 'Success!',
        'text' => 'Setting successfully created!'
    ]);
}

public function performUpdate()
{
    if (!$this->setting_id || !$this->canUpdate) {
        return;
    }

    $setting = Setting::findOrFail($this->setting_id);

    // Check if any data actually changed
    if ($setting->name === $this->name && $setting->value === $this->value) {
        $this->closeModal();
        $this->dispatch('swal:success', [
            'title' => 'No Changes!',
            'text' => 'Nothing was changed.'
        ]);
        return;
    }

    $setting->update([
        'name' => $this->name,
        'value' => $this->value,
    ]);

    $this->closeModal();

    $this->dispatch('swal:success', [
        'title' => 'Success!',
        'text' => 'Setting successfully updated!'
    ]);
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
            'settings' => Setting::latest('SettingsID')->paginate(10)
        ]);
    }
}
