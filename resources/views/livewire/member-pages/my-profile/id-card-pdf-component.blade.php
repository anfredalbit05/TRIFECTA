
{{-- resources/views/livewire/member-pages/my-profile/id-card-pdf-component.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

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
    page-break-after: avoid;
    page-break-inside: avoid;
}


.card-front {
    position: absolute;

    /* Smaller landscape card */
    width: 72mm;
    height: 45.7mm;

    /* Center horizontally and vertically */
    left: 50%;
    top: 50%;
    margin-left: -36mm;
    margin-top: -22.85mm;

    overflow: hidden;
}


.template-img {
    position: absolute;
    left: 0;
    top: 0;

    width: 100%;
    height: 100%;

    object-fit: fill;
}

/* MEMBER PHOTO — MATCH LIVE PREVIEW */
.photo-box {
    position: absolute;
    z-index: 2;

    left: 6.2%;
    top: 32.6%;

    width: 18%;
    height: 33.6%;

    overflow: hidden;
    border-radius: 9%;
}

.photo-box img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}


/* MEMBER INFORMATION — MATCH LIVE PREVIEW */
.idf-field {
    position: absolute;
    z-index: 3;

    font-family: "DejaVu Sans", sans-serif;
    font-size: 6pt;
    font-weight: 700;
    line-height: 1.2;

    color: #ffffff;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* MEMBER NAME — MATCH LIVE PREVIEW */
.idf-name {
    left: 28.3%;
    top: 34%;
    width: 67%;

}

/* MEMBERSHIP NUMBER */
.idf-membership {
    left: 28.3%;
    top: 48%;
    width: 29%;

    width: 29%;

    color: #f5d34e;
    font-size: 2.5cqw;
}

/* CERTIFICATE NUMBER */
.idf-certificate {
    left: 61.3%;
    top: 50.2%;
    width: 32%;

    width: 32%;

    font-size: 2.3cqw;
}

/* DATE OF ISSUE */
.idf-issue {
    left: 28.3%;
    top: 61%;
    width: 29%;

    width: 29%;

    font-size: 2.3cqw;
}

/* VALID UNTIL */
.idf-valid {
    left: 61.3%;
    top: 61%;
    width: 32%;

    width: 32%;

    font-size: 2.3cqw;
}

/* MEMBER SIGNATURE */
.idf-signature {
    position: absolute;
    z-index: 3;

    left: 60%;
    top: 80%;

    width: 25%;
    height: 8%;
}

.idf-signature img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

</style>
</head>

<body>

    <div class="pdf-page">

        {{-- FRONT ID CARD ONLY --}}
        <div class="card card-front">

            {{-- FRONT TEMPLATE --}}
            @if ($frontTemplateBase64)
                <img
                    class="template-img"
                    src="{{ $frontTemplateBase64 }}"
                    alt="Trifecta ID Card"
                >
            @endif

            {{-- MEMBER PHOTO --}}
            <div class="photo-box">
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

            {{-- MEMBERSHIP NUMBER --}}
            <div class="idf-field idf-membership">
                {{ $membershipNo }}
            </div>

            {{-- CERTIFICATE NUMBER --}}
            <div class="idf-field idf-certificate">
                {{ $certificateNo ?? '' }}
            </div>

            {{-- DATE OF ISSUE --}}
            <div class="idf-field idf-issue">
                {{ $dateOfIssue }}
            </div>

            {{-- VALID UNTIL --}}
            <div class="idf-field idf-valid">
                {{ $validUntil }}
            </div>

            {{-- MEMBER SIGNATURE --}}
            <div class="idf-signature">
                @if ($signatureBase64)
                    <img src="{{ $signatureBase64 }}" alt="Member Signature">
                @endif
            </div>

        </div>

    </div>

</body>
</html>
