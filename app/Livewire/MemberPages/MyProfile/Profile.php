<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use HasTabSkeleton;
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

    // ID card data
    public string $membershipNo = '';

    public string $dateOfIssue = '';

    public string $validUntil = '';

    /**
     * True when the member has an approved activation with a receipt
     * number — gates the "Download Receipt" button (hidden otherwise).
     */
    public bool $hasDownloadableReceipt = false;

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

        // Emergency Contact fields mapping
        $this->emergencyName = $user->emergencyName ?? '';
        $this->emergencyAddress = $user->emergencyAddress ?? '';
        $this->emergencyRelationship = $user->emergencyRelationship ?? '';
        $this->emergencyContactno = $user->emergencyContactno ?? '';

        // Correctly mapped database columns: 'civilstatus' and 'gender'
        $this->civilStatus = $user->civilstatus ?? 'Single';
        $this->sex = $user->gender ?? 'Male';
        $this->beneficiaries = $user->beneficiaries;
        // Same referral link format as the Account page, so the QR code
        // printed on the ID opens the join/registration page.
        $this->referralUrl = rtrim(config('app.url'), '/').'/?ref='.urlencode($user->referralCode ?? '');

        // Treat a signature value whose file no longer exists as none,
        // so the preview and pad don't render broken images.
        $this->signature = ($user->signature && Storage::disk('public')->exists($user->signature))
            ? $user->signature
            : null;

        // Receipt download is only offered once an approved activation
        // with a generated receipt number exists.
        $this->hasDownloadableReceipt = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->whereNotNull('receipt_number')
            ->exists();

        // Date of issue / valid until come from the approved activation's
        // policy effectivity/expiration dates (falling back to the
        // activation's approval date for the issue date).
        $approvedActivation = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->latest('ActivationID')
            ->first();

       // ID card data — membership no: TL + current year + zero-padded UserID
       // Membership number comes from the approved activation policy number
        $this->membershipNo = $approvedActivation?->policy_number ?? 'N/A';


        $issueDate = $approvedActivation?->policy_effectivity_date
            ?: $approvedActivation?->updated_at;

        $this->dateOfIssue = $issueDate
            ? Carbon::parse($issueDate)->format('F d, Y')
            : 'N/A';

        $this->validUntil = $approvedActivation?->policy_expiration_date
            ? Carbon::parse($approvedActivation->policy_expiration_date)->format('F d, Y')
            : 'N/A';

        // Generate QR code for ID card
        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd);
        $writer = new Writer($renderer);
        $this->qrCode = $writer->writeString($this->referralUrl);
    }

    // Save signature coming from canvas dataURL
    public function saveSignature($dataUrl): void
    {
        $user = User::findOrFail(Auth::id());

        // Strip whatever data URI prefix is present (now image/jpeg, previously image/png)
        if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
            $dataUrl = substr($dataUrl, strpos($dataUrl, ',') + 1);
        } else {
            $extension = 'jpg'; // fallback
        }

        $dataUrl = str_replace(' ', '+', $dataUrl);
        $imageData = base64_decode($dataUrl);

        $filename = 'images/SIG_'.$user->UserID.'_'.time().'.'.$extension;

        // Delete old signature if exists
        if ($user->signature && Storage::disk('public')->exists($user->signature)) {
            Storage::disk('public')->delete($user->signature);
        }

        Storage::disk('public')->put($filename, $imageData);

        $user->update([
            'signature' => $filename,
        ]);

        $this->signature = $filename;

        $this->dispatch(
        'signature-saved',
        url: Storage::url($filename)
    );
    }


public function removeSignature(): void
{
    $user = User::findOrFail(Auth::id());

    // Check if there is actually a saved signature
    if (!$user->signature) {
        $this->signature = null;

        $this->dispatch('signature-delete-no-signature');

        return;
    }

    // Delete the physical signature file
    if (Storage::disk('public')->exists($user->signature)) {
        Storage::disk('public')->delete($user->signature);
    }

    // Remove signature from database
    $user->update([
        'signature' => null,
    ]);

    // Update Livewire state
    $this->signature = null;

    // Notify JavaScript
    $this->dispatch('signature-deleted');
}


    public function updatedProfilepic(): void
    {
        $this->validateOnly('profilepic', [
            'profilepic' => ['required', 'image', 'mimes:jpg,jpeg', 'max:2048'], // removed png, webp
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
            'emergencyName' => ['nullable', 'string', 'max:255'],
            'emergencyRelationship' => ['nullable', 'string', 'max:255'],
            'emergencyContactno' => ['nullable', 'string', 'max:50'],
            'emergencyAddress' => ['nullable', 'string'],
        ]);

        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        $user->update([
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'birthdate' => $this->birthdate,
            'contactno' => $this->contactno,
            'address' => $this->address,
            'civilstatus' => $this->civilStatus,
            'gender' => $this->sex,
            'emergencyName' => $this->emergencyName,
            'emergencyRelationship' => $this->emergencyRelationship,
            'emergencyContactno' => $this->emergencyContactno,
            'emergencyAddress' => $this->emergencyAddress,
        ]);

        // Use a dedicated event name for profile details
        $this->dispatch('profile-details-updated');
    }

    public function updateProfilePicture(): void
    {
        $this->validate([
            'profilepic' => ['required', 'image', 'mimes:jpg,jpeg', 'max:2048'], // removed png, webp
        ]);

        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        if (! empty($user->profilepic) && Storage::disk('public')->exists($user->profilepic)) {
            Storage::disk('public')->delete($user->profilepic);
        }

        $filename = 'PRO_'.$user->UserID.'_'.time().'.'.$this->profilepic->extension();
        $path = $this->profilepic->storeAs('images', $filename, 'public');

        $user->update([
            'profilepic' => $path,
        ]);

        $this->reset('profilepic');
        $this->dispatch('picture-updated');
    }

    public function removeProfilePicture(): void
    {
        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        if (! empty($user->profilepic) && Storage::disk('public')->exists($user->profilepic)) {
            Storage::disk('public')->delete($user->profilepic);
        }

        $user->update([
            'profilepic' => 'images/profiles/default-avatar.png',
        ]);

        $this->reset('profilepic');
        $this->dispatch('picture-updated');
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
