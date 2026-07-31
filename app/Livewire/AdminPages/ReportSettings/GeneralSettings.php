<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Setting;
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

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'value', 'setting_id']);$this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();$this->reset(['name', 'value', 'setting_id']);
    }

    public function editSetting($id)
    {
        $this->resetValidation();
        $setting = Setting::findOrFail($id);

        $this->setting_id =$setting->SettingsID;
        $this->name =$setting->name;
        $this->value =$setting->value;

        $this->isOpen = true;
    }

    public function saveSetting()
    {
        $this->validate();

        if ($this->setting_id) {$setting = Setting::findOrFail($this->setting_id);$setting->update([
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
        $this->dispatch('confirm-delete', id:$id);
    }

    public function deleteSetting($id)
    {
        $setting = Setting::findOrFail($id);$setting->delete();

        $this->dispatch('setting-deleted');
    }

 public function render()
    {
        return view('components.admin-pages.report-settings.general', [
            'settings' => Setting::latest('SettingsID')->get()
        ]);
    }
}
