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
        // Single source of truth for the ID card PDF (template, overlays
        // and QR code) lives in the controller.
        return app(\App\Http\Controllers\MemberIdCardController::class)->download();
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile.id-card-pdf-component');
    }
}
