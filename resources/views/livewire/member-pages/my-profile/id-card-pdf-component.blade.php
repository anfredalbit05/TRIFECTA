{{-- resources/views/livewire/member-pages/my-profile/id-card-pdf-component.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

{{--
|--------------------------------------------------------------------------
| MEMBER ID CARD — PDF
|--------------------------------------------------------------------------
|
| One card, printed on half-letter landscape (8.5in × 5.5in) and centred.
| The artwork is 1200 × 896, so the card box is 85.6mm × 63.9mm (the same
| ratio) and every overlay is positioned in mm using the SAME percentages
| as the live ID preview in my-profile.css. That is what keeps the PDF and
| the browser preview identical: each value sits on its printed label.
|
--}}

<style>

@page {
    size: 8.5in 5.5in;
    margin: 0;
}

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
    width: 8.5in;
    height: 5.5in;
}

.pdf-page {
    position: relative;
    width: 8.5in;
    height: 5.5in;
    overflow: hidden;
}

/* Card = 85.6mm × 63.9mm, centred on the page */
.id-card {
    position: absolute;
    left: 65.15mm;
    top: 37.9mm;
    width: 85.6mm;
    height: 63.9mm;
    overflow: hidden;
}

.template-img {
    position: absolute;
    left: 0;
    top: 0;
    width: 85.6mm;
    height: 63.9mm;
}

/* MEMBER PHOTO — right side, below the member type */
.idf-photo {
    position: absolute;
    left: 68.05mm;
    top: 16.93mm;
    width: 12.41mm;
    height: 17.25mm;
    overflow: hidden;
}

.idf-photo img {
    width: 12.41mm;
    height: 17.25mm;
}

/* MEMBER INFORMATION — on the printed labels, navy on the light artwork */
.idf-field {
    position: absolute;
    font-family: "DejaVu Sans", Helvetica, Arial, sans-serif;
    font-size: 8pt;
    font-weight: bold;
    line-height: 1.2;
    color: #0c2c6b;
    white-space: nowrap;
    overflow: hidden;
}

/* The name shares its line with the printed label, so it is one size
   down to keep full names inside the card. */
.idf-name       { left: 31.59mm; top: 20mm; width: 36.46mm;}
.idf-membership { left: 25.08mm; top: 28.1mm; width: 38.50mm; }
.idf-issue      { left: 31.07mm; top: 36.2mm; width: 35.50mm; }
.idf-valid      { left: 27.56mm; top: 44.1mm; width: 38.50mm; }

/* REFERRAL QR — encodes the referral / join link */
.idf-qr {
    position: absolute;
    left: 69.34mm;
    top: 45mm;
}

.idf-qr img {
    width: 12mm;
    height: 12mm;
}

</style>
</head>

<body>

    <div class="pdf-page">

        <div class="id-card">

            {{-- FRONT TEMPLATE --}}
            @if ($frontTemplateBase64)
                <img
                    class="template-img"
                    src="{{ $frontTemplateBase64 }}"
                    alt="Trifecta ID Card"
                >
            @endif

            {{-- MEMBER PHOTO --}}
            <div class="idf-photo">
                @if ($profilePicBase64)
                    <img src="{{ $profilePicBase64 }}" alt="Member Photo">
                @endif
            </div>

            {{-- MEMBER NAME --}}
            <div class="idf-field idf-name">
                {{ strtoupper(trim(
                    $user->firstname . ' ' .
                    ($user->middlename
                        ? substr(trim($user->middlename), 0, 1) . '. '
                        : '') .
                    $user->lastname
                )) }}
            </div>

            {{-- POLICY NUMBER --}}
            <div class="idf-field idf-membership">
                {{ $membershipNo }}
            </div>

            {{-- DATE OF ISSUE --}}
            <div class="idf-field idf-issue">
                {{ $dateOfIssue }}
            </div>

            {{-- VALID UNTIL --}}
            <div class="idf-field idf-valid">
                {{ $validUntil }}
            </div>

            {{-- REFERRAL QR (PNG — dompdf cannot draw inline SVG) --}}
            <div class="idf-qr">
                @if (! empty($qrCodePng))
                    <img src="{{ $qrCodePng }}" alt="Referral QR">
                @endif
            </div>

        </div>

    </div>

</body>
</html>
