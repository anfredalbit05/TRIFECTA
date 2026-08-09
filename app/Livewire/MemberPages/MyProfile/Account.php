<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

use Barryvdh\DomPDF\Facade\Pdf;

class Account extends Component
{
    public string $username = '';
    public string $email = '';
    public string $qrCode = '';
    public string $referralUrl = '';
    public string $status = '';

    public function mount(): void
    {
        $userId = Auth::id();

        // Fetch user with beneficiaries loaded properly using Eloquent query
        $user = User::with('beneficiaries')->findOrFail($userId);

        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->status = $user->status ?? '';
        $this->referralUrl = url('/?ref=' . $user->referralCode);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $this->qrCode = $writer->writeString(
            $this->referralUrl
        );
    }

 public function save(): void
{
    /** @var User $user */
    $user = Auth::user();

    $this->validate([
        'username' => [
            'required',
            'string',
            'max:50',
            Rule::unique('users', 'username')->ignore($user->UserID, 'UserID'),
        ],
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($user->UserID, 'UserID'),
        ],
        'status' => [
            'required',
            Rule::in(['Active', 'Inactive']),
        ],
    ]);

    // Track individual changes
    $changes = [];

    if ($this->username !== $user->username) {
        $changes[] = "<b>Username:</b> {$user->username} &rarr; {$this->username}";
    }

    if ($this->email !== $user->email) {
        $changes[] = "<b>Email:</b> {$user->email} &rarr; {$this->email}";
    }

    if ($user->RoleID != 4 && $this->status !== $user->status) {
        $changes[] = "<b>Status:</b> {$user->status} &rarr; {$this->status}";
    }

    // If no changes exist, show info alert
    if (empty($changes)) {
        $this->js("Swal.fire({ title: 'No Changes Detected', text: 'You haven’t modified any account details.', icon: 'info', confirmButtonColor: '#0C2C6B' })");
        return;
    }

    // Dispatch event with the list of changes
    $this->dispatch('prompt-save-confirmation', changes: $changes);
}

public function executeSave(): void
{
    /** @var User $user */
    $user = Auth::user();

    $data = [
        'username' => $this->username,
        'email' => $this->email,
    ];

    if ($user->RoleID != 4) {
        $data['status'] = $this->status;
    }

    $user->update($data);

    $this->dispatch('account-updated');
}


    public function downloadPdf()
    {
        /** @var User $user */
        $user = User::with('beneficiaries')->findOrFail(Auth::id());

        $beneficiaries = $user->beneficiaries;

        $renderer = new ImageRenderer(
            new RendererStyle(80),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $referralUrl = url('/?ref=' . ($user->referralCode ?? ''));
        $qrCode = $writer->writeString($referralUrl);

        $pdf = Pdf::loadView('pdf.id-card', [
            'user' => $user,
            'beneficiaries' => $beneficiaries,
            'qrCode' => $qrCode,
        ]);

        return $pdf->download('unitylife-id-card.pdf');
    }

    public function render()
    {
        return view(
            'livewire.member-pages.my-profile.account'
        );
    }
}
