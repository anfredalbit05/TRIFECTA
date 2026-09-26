{{-- resources/views/livewire/member-pages/my-profile/id-card-pdf-component.blade.php --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        {!! file_get_contents(resource_path('css/livewire/member-pages/my-profile/id-card.css')) !!}
    </style>
</head>

<body>

    {{-- ================================================================
         PDF PAGE 1 — FRONT OF ID (TRI-CARE artwork + live data)
         ================================================================ --}}

    <div class="pdf-page">

        <div class="card">

            {{-- Template artwork --}}
            <img class="template-img" src="{{ public_path('images/certificate/id-front.png') }}" alt="">

            {{-- MEMBER PHOTO --}}
            <div class="photo-box">
                @if ($profilePicBase64)
                    <img src="{{ $profilePicBase64 }}" alt="Member Photo">
                @endif
            </div>

            {{-- DATA LINES — written after the printed labels --}}
            <div class="idf-field idf-name">{{ strtoupper(trim($user->firstname . ' ' . $user->middlename . ' ' . $user->lastname)) }}</div>

            <div class="idf-field idf-membership">{{ $membershipNo }}</div>

            <div class="idf-field idf-issue">{{ $dateOfIssue }}</div>

            <div class="idf-field idf-valid">{{ $validUntil }}</div>

            {{-- MEMBER SIGNATURE — white card beside the barcode --}}
            <div class="idf-signature">
                @if ($signatureBase64)
                    <img src="{{ $signatureBase64 }}" alt="Member Signature">
                @endif
            </div>
            <div class="idf-signature-caption">SIGNATURE</div>

        </div>

    </div>

    {{-- ================================================================
         PDF PAGE 2 — BACK OF ID (artwork only)
         ================================================================ --}}

    <div class="pdf-page">

        <div class="card">

            <img class="template-img" src="{{ public_path('images/certificate/id-back.png') }}" alt="">

        </div>

    </div>

</body>

</html>
