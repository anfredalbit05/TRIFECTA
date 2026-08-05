<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class Profile extends Component
{
    use WithFileUploads;

    public $profilepic = null;

    public string $firstname = '';
    public string $lastname = '';
    public string $middlename = '';

    public ?string $birthdate = null;
    public ?string $contactno = null;
    public ?string $address = null;

    // ID Card & Emergency Preview Fields
    public string $civilStatus = 'Single';
    public string $sex = 'Male';
    public string $qrCode = '';
    public string $referralUrl = '';
    public $beneficiaries = [];

    public string $emergencyName = '';
    public string $emergencyAddress = '';
    public string $emergencyRelationship = '';
    public string $emergencyContactno = '';

    // Signature Property
    public ?string $signature = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = User::with('beneficiaries')->findOrFail(Auth::id());

        $this->firstname = $user->firstname ?? '';
        $this->lastname = $user->lastname ?? '';
        $this->middlename = $user->middlename ?? '';

        $this->birthdate = $user->birthdate
            ? Carbon::parse($user->birthdate)->format('Y-m-d')
            : null;

        $this->contactno = $user->contactno ?? '';
        $this->address = $user->address ?? '';

        // Load ID preview and emergency fields
        $this->civilStatus = $user->civil_status ?? 'Single';
        $this->sex = $user->sex ?? 'Male';
        $this->beneficiaries = $user->beneficiaries;
        $this->referralUrl = url('/?ref=' . ($user->referralCode ?? ''));

        $this->emergencyName = $user->emergency_name ?? '';
        $this->emergencyAddress = $user->emergency_address ?? '';
        $this->emergencyRelationship = $user->emergency_relationship ?? '';
        $this->emergencyContactno = $user->emergency_contactno ?? '';

        $this->signature = $user->signature ?? null;

        // Generate QR code for ID card
        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $this->qrCode = $writer->writeString($this->referralUrl);
    }

    // Save signature coming from canvas dataURL
    public function saveSignature($dataUrl)
    {
        $this->signature = $dataUrl;

        /** @var User $user */
        $user = Auth::user();
        $user->update(['signature' => $dataUrl]);

        session()->flash('profile-success', 'Signature saved successfully.');
    }

    public function updatedProfilepic(): void
    {
        $this->validateOnly('profilepic', [
            'profilepic' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    public function saveProfile(): void
    {
        $this->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'middlename' => ['nullable', 'string', 'max:100'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'contactno' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'civilStatus' => ['required', 'string'],
            'sex' => ['required', 'string'],
            'emergencyName' => ['nullable', 'string', 'max:150'],
            'emergencyAddress' => ['nullable', 'string', 'max:255'],
            'emergencyRelationship' => ['nullable', 'string', 'max:50'],
            'emergencyContactno' => ['nullable', 'string', 'max:20'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'birthdate' => $this->birthdate,
            'contactno' => $this->contactno,
            'address' => $this->address,
            'civil_status' => $this->civilStatus,
            'sex' => $this->sex,
            'emergency_name' => $this->emergencyName,
            'emergency_address' => $this->emergencyAddress,
            'emergency_relationship' => $this->emergencyRelationship,
            'emergency_contactno' => $this->emergencyContactno,
        ]);

        $user->refresh();
        $this->dispatch('profile-updated');

        session()->flash('profile-success', 'Personal information and emergency details updated successfully.');
    }

    public function updateProfilePicture(): void
    {
        $this->validate([
            'profilepic' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!empty($user->profilepic) && Storage::disk('public')->exists($user->profilepic)) {
            Storage::disk('public')->delete($user->profilepic);
        }

        $path = $this->profilepic->store('profiles', 'public');

        $user->update([
            'profilepic' => $path,
        ]);

        $user->refresh();
        $this->dispatch('profile-updated');
        $this->reset('profilepic');

        session()->flash('picture-success', 'Profile picture updated successfully.');
    }

    public function requestPhysicalId()
    {
        session()->flash('id-success', 'Physical ID request submitted successfully!');
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile.profile');
    }
}
