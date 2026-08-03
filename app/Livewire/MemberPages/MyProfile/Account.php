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

class Account extends Component
{
    public string $username = '';
    public string $email = '';
    public string $qrCode = '';
    public string $referralUrl = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';

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

                Rule::unique(
                    'users',
                    'username'
                )->ignore(
                    $user->UserID,
                    'UserID'
                ),
            ],

            'email' => [
                'required',
                'email',

                Rule::unique(
                    'users',
                    'email'
                )->ignore(
                    $user->UserID,
                    'UserID'
                ),
            ],
        ]);

        $user->update([
            'username' => $this->username,
            'email' => $this->email,
        ]);

        session()->flash(
            'account-success',
            'Account information updated successfully.'
        );
    }

    public function render()
    {
        return view(
            'livewire.member-pages.my-profile.account'
        );
    }
}
