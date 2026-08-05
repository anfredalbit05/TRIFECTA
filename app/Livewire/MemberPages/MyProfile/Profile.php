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

    // ID Card & Form Fields
    public string $civilStatus = 'Single';
    public string $sex = 'Male';
    public string $qrCode = '';
    public string $referralUrl = '';
    public $beneficiaries = [];

    // Temporary static fields for emergency contacts (won't throw DB errors)
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

        // Correctly mapped database columns: 'civilstatus' and 'gender'
        $this->civilStatus = $user->civilstatus ?? 'Single';
        $this->sex = $user->gender ?? 'Male';
        $this->beneficiaries = $user->beneficiaries;
        $this->referralUrl = url('/?ref=' . ($user->referralCode ?? ''));

        $this->signature = $user->signature ?? null;

        // Generate QR code for ID card
        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $this->qrCode = $writer->writeString($this->referralUrl);
    }

    // Save signature coming from canvas dataURL
   public function saveSignature($dataUrl): void
{
    $user = User::findOrFail(Auth::id());

    // Remove "data:image/png;base64,"
    $image = str_replace('data:image/png;base64,', '', $dataUrl);
    $image = str_replace(' ', '+', $image);

    $imageData = base64_decode($image);

    $filename = 'signatures/signature_' . $user->UserID . '_' . time() . '.png';

    // Delete old signature if exists
    if ($user->signature && Storage::disk('public')->exists($user->signature)) {
        Storage::disk('public')->delete($user->signature);
    }

    Storage::disk('public')->put($filename, $imageData);

    $user->update([
        'signature' => $filename
    ]);

    $this->signature = $filename;

    $this->dispatch('signature-saved');
}

public function removeSignature(): void
{
    $user = User::findOrFail(Auth::id());

    if ($user->signature && Storage::disk('public')->exists($user->signature)) {
        Storage::disk('public')->delete($user->signature);
    }

    $user->update([
        'signature' => null,
    ]);

    $this->signature = null;

    session()->flash('profile-success', 'Signature removed successfully.');
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
        ]);

        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        // Update database using exact migration column names
        $user->update([
            'firstname'   => $this->firstname,
            'lastname'    => $this->lastname,
            'middlename'  => $this->middlename,
            'birthdate'   => $this->birthdate,
            'contactno'   => $this->contactno,
            'address'     => $this->address,
            'civilstatus' => $this->civilStatus, // Mapped to 'civilstatus' DB column
            'gender'      => $this->sex,         // Mapped to 'gender' DB column
        ]);

        $this->dispatch('profile-updated');

        session()->flash('profile-success', 'Personal information updated successfully.');
    }

    public function updateProfilePicture(): void
    {
        $this->validate([
            'profilepic' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        if (!empty($user->profilepic) && Storage::disk('public')->exists($user->profilepic)) {
            Storage::disk('public')->delete($user->profilepic);
        }

        $path = $this->profilepic->store('profiles', 'public');

        $user->update([
            'profilepic' => $path,
        ]);

        $this->dispatch('profile-updated');
        $this->reset('profilepic');

        session()->flash('picture-success', 'Profile picture updated successfully.');
    }

    // Add this method inside your Profile class in Profile.php

    public function removeProfilePicture(): void
    {
        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        // 1. Delete the existing file from disk if it exists
        if (!empty($user->profilepic) && Storage::disk('public')->exists($user->profilepic)) {
            Storage::disk('public')->delete($user->profilepic);
        }

        // 2. Reset the database field to null or the default path
        // Using default path matching your migration default:
        $user->update([
            'profilepic' => 'images/profiles/default-avatar.png',
        ]);

        // 3. Reset local property, dispatch event, and send alert
        $this->reset('profilepic');
        $this->dispatch('profile-updated');

        session()->flash('picture-success', 'Profile picture removed successfully.');
    }

    public function requestPhysicalId(): void
    {
        session()->flash('id-success', 'Physical ID request submitted successfully!');
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile.profile');
    }
}
