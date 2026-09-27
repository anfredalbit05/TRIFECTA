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
         ONE PAGE — HALF SHORT BOND (8.5in x 5.5in)
         FRONT face on the LEFT, BACK face on the RIGHT.
         Cut down the middle for a two-sided badge.
         ================================================================ --}}

    <div class="pdf-page">

        {{-- ============ FRONT (left half) ============ --}}

        <div class="card card-front">

            {{-- Template artwork --}}
            <img class="template-img" src="{{ public_path('images/certificate/id-front.png') }}" alt="">

            {{-- MEMBER PHOTO --}}
            <div class="photo-box">
                @if ($profilePicBase64)
                    <img src="{{ $profilePicBase64 }}" alt="Member Photo">
                @endif
            </div>

            {{-- DATA LINES — same positions as the live preview --}}
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

        </div>

        {{-- ============ BACK (right half) ============ --}}

        <div class="card card-back">

            <img class="template-img" src="{{ public_path('images/certificate/id-back.png') }}" alt="">

        </div>

    </div>

</body>

</html>
