<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Module;
use App\Models\Privilege;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class GeneralSettings extends Component
{
    use WithPagination;

    public $isOpen = false;

    public $setting_id;

    // Search Property
    public string $search = '';    // Form fields

    public $name;

    public $value;

    protected $rules = [
        'name' => 'required|string|max:100',
        'value' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    // Helper to check permissions dynamically
    protected function hasPermission($action)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->RoleID) {
            return false;
        }

        // Super Admin (RoleID 1) bypasses checks
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $module = Module::where('modulename', 'Report & Settings - General')->first();

        if (! $module) {
            return false;
        }

        $privilege = Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', $module->ModuleID)
            ->first();

        if (! $privilege) {
            return false;
        }

        return match ($action) {
            'create' => (bool) $privilege->can_create,
            'read' => (bool) $privilege->can_read,
            'update' => (bool) $privilege->can_update,
            'delete' => (bool) $privilege->can_delete,
            default => false,
        };
    }

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
        if (! $this->canCreate) {
            session()->flash('message', 'Unauthorized action.');

            return;
        }

        $this->resetValidation();
        $this->reset(['name', 'value', 'setting_id']);

        // Show the plain-text panel for a brand-new setting (one-way push —
        // the panel state itself lives in Alpine, so it can never be
        // overwritten by a stale entangled value syncing back).
        $this->dispatch('setting-modal-type', type: 'text');

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
        if (! $this->canUpdate) {
            session()->flash('message', 'Unauthorized action.');

            return;
        }

        $this->resetValidation();
        $setting = Setting::findOrFail($id);

        $this->setting_id = $setting->SettingsID;
        $this->name = $setting->name;
        $this->value = $setting->value;

        // Show the panel matching the stored value type (signature / boolean / text).
        // Any stored images/...SIG_... path is a signature (covers CASHIER_SIG_
        // files created here and profile-style SIG_ files used as a cashier sig).
        $type = str_contains((string) $setting->value, 'SIG_')
            ? 'signature'
            : (in_array(strtolower((string) $setting->value), ['true', 'false', '1', '0']) ? 'boolean' : 'text');

        $this->dispatch('setting-modal-type', type: $type);

        $this->isOpen = true;
    }

    public function saveSetting()
    {
        if ($this->setting_id && ! $this->canUpdate) {
            return;
        }
        if (! $this->setting_id && ! $this->canCreate) {
            return;
        }

        $emptyFields = [];

        if (empty(trim($this->name))) {
            $emptyFields[] = 'Setting Name';
        }

        if (empty(trim($this->value)) && $this->value !== '0') {
            // Signature settings: the uploaded/drawn image becomes the value
            // automatically — tell the user there is nothing to type.
            $emptyFields[] = str_contains(strtolower((string) $this->name), 'signature')
                ? 'Value (upload or draw the signature first — its storage path is saved as the value automatically, no typing needed)'
                : 'Value';
        }

        if (! empty($emptyFields)) {
            $text = count($emptyFields) > 1
                ? 'The following fields are empty:<br><br><b>• '.implode('</b><br><b>• ', $emptyFields).'</b>'
                : 'The <b>'.$emptyFields[0].'</b> field is empty.';

            // Sent as html so the <b> tags render (the shared Swal handler
            // only renders markup from the html key — text shows it raw).
            $this->dispatch('swal:error', [
                'title' => 'Missing Fields',
                'html' => $text,
            ]);

            return;
        }

        $this->validate();

        $existingQuery = Setting::where('name', $this->name);
        if ($this->setting_id) {
            $existingQuery->where('SettingsID', '!=', $this->setting_id);
        }

        if ($existingQuery->exists()) {
            $this->dispatch('swal:error', [
                'title' => 'Duplicate Error',
                'text' => 'A setting with this name already exists.',
            ]);

            return;
        }

        if ($this->setting_id) {
            $this->dispatch('confirm-update');

            return;
        }

        Setting::create([
            'name' => $this->name,
            'value' => $this->value,
        ]);

        $this->closeModal();

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Setting successfully created!',
        ]);
    }

    public function performUpdate()
    {
        if (! $this->setting_id || ! $this->canUpdate) {
            return;
        }

        $setting = Setting::findOrFail($this->setting_id);

        if ($setting->name === $this->name && $setting->value === $this->value) {
            $this->closeModal();
            $this->dispatch('swal:success', [
                'title' => 'No Changes!',
                'text' => 'Nothing was changed.',
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
            'text' => 'Setting successfully updated!',
        ]);
    }

    /**
     * Store an uploaded or drawn cashier signature (raw base64 image data) on
     * the public disk and use its storage path as the current setting value.
     * The previous CASHIER_SIG file referenced by the value is deleted.
     */
    public function saveSignatureSetting(?string $base64): void
    {
        if (! $this->canCreate && ! $this->canUpdate) {
            return;
        }

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', (string) $base64);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            $this->dispatch('swal:error', [
                'title' => 'Invalid Signature',
                'text' => 'The signature image could not be read. Please try again.',
            ]);

            return;
        }

        $info = @getimagesizefromstring($binary);

        $extension = match ($info === false ? null : $info['mime']) {
            'image/png' => 'png',
            'image/jpeg', 'image/jpg' => 'jpg',
            default => null,
        };

        if ($extension === null) {
            $this->dispatch('swal:error', [
                'title' => 'Unsupported Image',
                'text' => 'The signature must be a PNG or JPEG image.',
            ]);

            return;
        }

        // Replace the previous cashier signature file, if the value points at one
        // Only delete files this feature created (CASHIER_SIG_ prefix). A
        // profile-style SIG_ path is shared with the member's profile — leave
        // it alone, it simply stops being referenced here.
        if (is_string($this->value)
            && str_contains($this->value, 'images/CASHIER_SIG_')
            && Storage::disk('public')->exists($this->value)) {
            Storage::disk('public')->delete($this->value);
        }

        $path = 'images/CASHIER_SIG_'.time().'.'.$extension;

        Storage::disk('public')->put($path, $binary);

        $this->value = $path;
    }

    public function confirmDeleteSetting($id)
    {
        if (! $this->canDelete) {
            return;
        }
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteSetting($id)
    {
        if (! $this->canDelete) {
            return;
        }

        $setting = Setting::findOrFail($id);
        $setting->delete();

        $this->dispatch('setting-deleted');
    }

    public function render()
    {
        $settings = Setting::query()
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('value', 'like', "%{$search}%");
                });
            })
            ->latest('SettingsID')
            ->paginate(10);

        return view('livewire.admin-pages.report-settings.general', [
            'settings' => $settings,
        ]);
    }
}
