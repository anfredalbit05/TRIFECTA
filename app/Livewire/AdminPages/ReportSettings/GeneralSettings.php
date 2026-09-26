<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Module;
use App\Models\Privilege;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GeneralSettings extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $isOpen = false;

    public $setting_id;

    public string $search = '';

    public $name;

    public $value;

    /**
     * Uploaded cashier signature.
     */
    public $signatureUpload = null;

    protected $rules = [
        'name' => 'required|string|max:100',
        'value' => 'nullable|string',
        'signatureUpload' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
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

    /**
     * Check permissions.
     */
    protected function hasPermission($action)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->RoleID) {
            return false;
        }

        // Super Admin
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $module = Module::where(
            'modulename',
            'Report & Settings - General'
        )->first();

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
            'read'   => (bool) $privilege->can_read,
            'update' => (bool) $privilege->can_update,
            'delete' => (bool) $privilege->can_delete,
            default  => false,
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

    /**
     * Open create modal.
     */
    public function openCreateModal()
    {
        if (! $this->canCreate) {
            session()->flash('message', 'Unauthorized action.');
            return;
        }

        $this->resetValidation();

        $this->reset([
            'name',
            'value',
            'setting_id',
            'signatureUpload',
        ]);

        $this->dispatch(
            'setting-modal-type',
            type: 'text'
        );

        $this->isOpen = true;
    }

    /**
     * Close modal.
     */
    public function closeModal()
    {
        $this->isOpen = false;

        $this->resetValidation();

        $this->reset([
            'name',
            'value',
            'setting_id',
            'signatureUpload',
        ]);
    }

    /**
     * Edit setting.
     */
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
        $this->signatureUpload = null;

        $storedValue = (string) $setting->value;

        /*
        |--------------------------------------------------------------------------
        | Determine input type
        |--------------------------------------------------------------------------
        */
        if (
            str_contains($storedValue, 'CASHIER_SIG_') ||
            str_contains($storedValue, 'SIG_')
        ) {
            $type = 'signature';
        } elseif (
            in_array(
                strtolower($storedValue),
                ['true', 'false', '1', '0'],
                true
            )
        ) {
            $type = 'boolean';
        } else {
            $type = 'text';
        }

        $this->dispatch(
            'setting-modal-type',
            type: $type
        );

        $this->isOpen = true;
    }

    /**
     * Save setting.
     */
    public function saveSetting()
    {
        if ($this->setting_id && ! $this->canUpdate) {
            return;
        }

        if (! $this->setting_id && ! $this->canCreate) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | If a signature upload exists, save it first.
        |--------------------------------------------------------------------------
        */
        if ($this->signatureUpload) {
            $this->saveUploadedSignature();
        }

        $emptyFields = [];

        if (empty(trim((string) $this->name))) {
            $emptyFields[] = 'Setting Name';
        }

        if (
            empty(trim((string) $this->value)) &&
            $this->value !== '0'
        ) {
            $emptyFields[] =
                str_contains(
                    strtolower((string) $this->name),
                    'signature'
                )
                    ? 'Value (upload or draw the signature first)'
                    : 'Value';
        }

        if (! empty($emptyFields)) {

            $text = count($emptyFields) > 1
                ? 'The following fields are empty:<br><br><b>• '
                    . implode('</b><br><b>• ', $emptyFields)
                    . '</b>'
                : 'The <b>'.$emptyFields[0].'</b> field is empty.';

            $this->dispatch('swal:error', [
                'title' => 'Missing Fields',
                'html' => $text,
            ]);

            return;
        }

        $this->validate([
            'name' => 'required|string|max:100',
            'value' => 'nullable|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate setting names
        |--------------------------------------------------------------------------
        */
        $existingQuery = Setting::where(
            'name',
            $this->name
        );

        if ($this->setting_id) {
            $existingQuery->where(
                'SettingsID',
                '!=',
                $this->setting_id
            );
        }

        if ($existingQuery->exists()) {

            $this->dispatch('swal:error', [
                'title' => 'Duplicate Error',
                'text' => 'A setting with this name already exists.',
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */
        if ($this->setting_id) {

            $this->dispatch('confirm-update');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */
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

    /**
     * Perform confirmed update.
     */
    public function performUpdate()
    {
        if (! $this->setting_id || ! $this->canUpdate) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | If a signature upload exists, save it before update.
        |--------------------------------------------------------------------------
        */
        if ($this->signatureUpload) {
            $this->saveUploadedSignature();
        }

        $setting = Setting::findOrFail(
            $this->setting_id
        );

        if (
            $setting->name === $this->name &&
            $setting->value === $this->value
        ) {
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
     * Save uploaded cashier signature.
     *
     * Livewire handles the temporary upload instead of sending
     * the entire image as a base64 method argument.
     */
    public function saveUploadedSignature(): void
    {
        if (! $this->canCreate && ! $this->canUpdate) {
            return;
        }

        if (! $this->signatureUpload) {
            return;
        }

        $this->validateOnly(
            'signatureUpload',
            [
                'signatureUpload' =>
                    'required|image|mimes:png,jpg,jpeg|max:5120',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Delete old CASHIER_SIG file only
        |--------------------------------------------------------------------------
        */
        if (
            is_string($this->value) &&
            str_contains(
                $this->value,
                'images/CASHIER_SIG_'
            ) &&
            Storage::disk('public')->exists($this->value)
        ) {
            Storage::disk('public')->delete(
                $this->value
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Generate unique filename
        |--------------------------------------------------------------------------
        */
        $extension = strtolower(
            $this->signatureUpload->getClientOriginalExtension()
        );

        if (! in_array(
            $extension,
            ['png', 'jpg', 'jpeg'],
            true
        )) {
            $extension = 'png';
        }

        $filename =
            'CASHIER_SIG_' .
            now()->format('Ymd_His') .
            '_' .
            uniqid() .
            '.' .
            $extension;

        /*
        |--------------------------------------------------------------------------
        | Store in public disk
        |--------------------------------------------------------------------------
        */
        $path = $this->signatureUpload->storeAs(
            'images',
            $filename,
            'public'
        );

        /*
        |--------------------------------------------------------------------------
        | Set setting value
        |--------------------------------------------------------------------------
        */
        $this->value = $path;

        /*
        |--------------------------------------------------------------------------
        | Clear temporary upload
        |--------------------------------------------------------------------------
        */
        $this->signatureUpload = null;

        $this->dispatch(
            'signature-uploaded'
        );
    }

    /**
     * Save drawn signature.
     *
     * This keeps your existing canvas functionality.
     */
    public function saveSignatureSetting(?string $base64): void
    {
        if (! $this->canCreate && ! $this->canUpdate) {
            return;
        }

        $base64 = trim((string) $base64);

        /*
        |--------------------------------------------------------------------------
        | Remove data URL prefix safely
        |--------------------------------------------------------------------------
        */
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64, 2)[1];
        }

        /*
        |--------------------------------------------------------------------------
        | Decode
        |--------------------------------------------------------------------------
        */
        $binary = base64_decode(
            $base64,
            true
        );

        if ($binary === false || empty($binary)) {

            $this->dispatch('swal:error', [
                'title' => 'Invalid Signature',
                'text' =>
                    'The signature image could not be read. Please try again.',
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate image
        |--------------------------------------------------------------------------
        */
        $info = @getimagesizefromstring(
            $binary
        );

        if ($info === false) {

            $this->dispatch('swal:error', [
                'title' => 'Invalid Image',
                'text' =>
                    'The signature image is not a valid image.',
            ]);

            return;
        }

        $mime = $info['mime'] ?? null;

        $extension = match ($mime) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            default => null,
        };

        if ($extension === null) {

            $this->dispatch('swal:error', [
                'title' => 'Unsupported Image',
                'text' =>
                    'The signature must be a PNG or JPEG image.',
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Delete previous generated cashier signature
        |--------------------------------------------------------------------------
        */
        if (
            is_string($this->value) &&
            str_contains(
                $this->value,
                'images/CASHIER_SIG_'
            ) &&
            Storage::disk('public')->exists($this->value)
        ) {
            Storage::disk('public')->delete(
                $this->value
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Store new signature
        |--------------------------------------------------------------------------
        */
        $path =
            'images/CASHIER_SIG_' .
            now()->format('Ymd_His') .
            '_' .
            uniqid() .
            '.' .
            $extension;

        Storage::disk('public')->put(
            $path,
            $binary
        );

        /*
        |--------------------------------------------------------------------------
        | Update Livewire value
        |--------------------------------------------------------------------------
        */
        $this->value = $path;

        $this->dispatch(
            'signature-uploaded'
        );
    }

    /**
     * Confirm delete.
     */
    public function confirmDeleteSetting($id)
    {
        if (! $this->canDelete) {
            return;
        }

        $this->dispatch(
            'confirm-delete',
            id: $id
        );
    }

    /**
     * Delete setting.
     */
    public function deleteSetting($id)
    {
        if (! $this->canDelete) {
            return;
        }

        $setting = Setting::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Delete generated signature file
        |--------------------------------------------------------------------------
        */
        if (
            is_string($setting->value) &&
            str_contains(
                $setting->value,
                'images/CASHIER_SIG_'
            ) &&
            Storage::disk('public')->exists(
                $setting->value
            )
        ) {
            Storage::disk('public')->delete(
                $setting->value
            );
        }

        $setting->delete();

        $this->dispatch(
            'setting-deleted'
        );
    }

    /**
     * Render.
     */
    public function render()
    {
        $settings = Setting::query()
            ->when(
                filled($this->search),
                function ($query) {

                    $search = trim(
                        $this->search
                    );

                    $query->where(
                        function ($q) use ($search) {

                            $q->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )->orWhere(
                                'value',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            )
            ->latest('SettingsID')
            ->paginate(10);

        return view(
            'livewire.admin-pages.report-settings.general',
            [
                'settings' => $settings,
            ]
        );
    }
}
