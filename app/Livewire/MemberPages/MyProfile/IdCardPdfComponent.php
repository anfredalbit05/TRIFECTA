<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Barryvdh\DomPDF\Facade\Pdf;

class IdCardPdfComponent extends Component
{
  public function download()
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

        // Make sure this matches your actual blade file path:
        $pdf = Pdf::loadView('livewire.member-pages.my-profile.id-card-pdf-component', [
            'user' => $user,
            'beneficiaries' => $beneficiaries,
            'qrCode' => $qrCode,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'unitylife-id-card.pdf');
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile.id-card-pdf-component');
    }
}
