{{-- resources/views/livewire/member-pages/my-profile/id-card-pdf-component.blade.php --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        /*
        |--------------------------------------------------------------------------
        | DOMPDF PAGE
        |--------------------------------------------------------------------------
        |
        | Controller sets:
        |
        | 85.6mm × 54mm
        |
        | Do NOT set the paper size here.
        |
        */

        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: Helvetica, Arial, sans-serif;

            font-size: 0;

            color: #1f2933;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF PAGE CONTAINER
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | This is what controls pagination.
        |
        | One .pdf-page = ONE PDF PAGE.
        |
        */

        .pdf-page {
            position: relative;

            width: 85.6mm;
            height: 54mm;

            margin: 0;
            padding: 0;

            page-break-after: always;
            page-break-inside: avoid;
        }

        .pdf-page:last-child {
            page-break-after: auto;
        }


        /*
        |--------------------------------------------------------------------------
        | ID CARD
        |--------------------------------------------------------------------------
        |
        | Slightly smaller than the physical page so DomPDF cannot overflow.
        |
        */

        .card {
            position: relative;

            width: 85.0mm;
            height: 53.2mm;

            margin: 0;
            padding: 0;

            background: #ffffff;

            border: 0.3mm solid #e2e5ea;

            overflow: hidden;

            font-size: 5pt;
        }


        /*
        |--------------------------------------------------------------------------
        | FRONT
        |--------------------------------------------------------------------------
        */

        .brand {
            position: absolute;

            top: 3mm;
            left: 4mm;

            font-size: 8.5pt;
            font-weight: bold;

            color: #17335e;
        }


        .badge {
            position: absolute;

            top: 3.4mm;
            right: 4mm;

            background: #e6f0fd;

            color: #2563eb;

            font-size: 5pt;
            font-weight: bold;

            letter-spacing: 0.3pt;

            padding: 1mm 2.5mm;

            border-radius: 3mm;
        }


        .divider-top {
            position: absolute;

            top: 8.1mm;
            left: 4mm;
            right: 4mm;

            border-top: 0.5pt solid #1a2b4c;
        }


        /*
        |--------------------------------------------------------------------------
        | PHOTO
        |--------------------------------------------------------------------------
        */

        .photo-box {
            position: absolute;

            top: 10mm;
            left: 4mm;

            width: 15.5mm;
            height: 19.5mm;

            border: 0.4pt solid #9aa1ac;

            text-align: center;

            overflow: hidden;

            color: #9aa1ac;

            font-size: 5pt;

            line-height: 19.5mm;
        }


        .photo-box img {
            display: block;

            width: 15.5mm;
            height: 19.5mm;

            object-fit: cover;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER DETAILS
        |--------------------------------------------------------------------------
        */

        .details {
            position: absolute;

            top: 10.2mm;
            left: 22mm;

            width: 42mm;
            height: 21.5mm;

            font-size: 4.8pt;
            line-height: 2.7mm;

            overflow: hidden;
        }

        .details .label {
            font-weight: bold;
            color: #17335e;
        }

        .details .val {
            font-weight: bold;
            color: #1f2933;
        }


        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        .qr-cell {
            position: absolute;

            top: 10mm;
            right: 4mm;

            width: 15mm;
            height: 15mm;

            text-align: center;

            overflow: hidden;
        }


        .qr-cell img {
            display: block;

            width: 15mm;
            height: 15mm;
        }


        /*
        |--------------------------------------------------------------------------
        | BENEFICIARIES
        |--------------------------------------------------------------------------
        */

        .divider-mid {
            position: absolute;

            top: 32.5mm;
            left: 4mm;
            right: 4mm;

            border-top: 0.4pt solid #c7ccd4;
        }


        .ben-label {
            position: absolute;

            top: 34mm;
            left: 4mm;

            font-size: 5pt;

            font-weight: bold;

            color: #1f2933;
        }


        .ben-content {
            position: absolute;

            top: 37.2mm;
            left: 4mm;

            width: 50mm;
            height: 9mm;

            font-size: 5pt;

            line-height: 2.5mm;

            overflow: hidden;

            color: #1f2933;
        }


        .ben-content ul {
            margin: 0;

            padding-left: 3mm;
        }


        .ben-content li {
            margin: 0;
            padding: 0;

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER SIGNATURE
        |--------------------------------------------------------------------------
        */

        .member-signature {
            display: block;
            width: 25mm;
            height: 7mm;
            margin: 0 auto;
            object-fit: contain;
        }

        .sig-cell {
            position: absolute;

            bottom: 2.5mm;
            right: 4mm;

            width: 26mm;

            text-align: center;
        }


        .sig-line {
            border-top: 0.4pt solid #1f2933;

            margin-bottom: 0.7mm;
        }


        .sig-caption {
            font-size: 4pt;

            letter-spacing: 0.5pt;

            color: #4b5563;
        }


        /*
        |--------------------------------------------------------------------------
        | BACK
        |--------------------------------------------------------------------------
        */

        .terms-title {
            position: absolute;

            top: 3.2mm;
            left: 4mm;
            right: 4mm;

            text-align: center;

            font-size: 6.5pt;

            font-weight: bold;

            color: #17335e;
        }


        .terms-intro {
            position: absolute;

            top: 8.2mm;
            left: 4mm;
            right: 4mm;

            font-size: 4.6pt;

            font-weight: bold;

            color: #2563eb;

            line-height: 2.3mm;
        }


        .terms-list {
            position: absolute;

            top: 12.3mm;
            left: 4mm;
            right: 4mm;

            margin: 0;

            padding-left: 3mm;

            font-size: 4.5pt;

            line-height: 2.4mm;

            color: #b8721a;
        }


        .terms-list li {
            margin: 0;
            padding: 0;
        }


        .terms-note {
            position: absolute;

            top: 20.5mm;
            left: 4mm;
            right: 4mm;

            font-size: 4.2pt;

            font-style: italic;

            color: #6b7280;

            line-height: 2.2mm;
        }


        .divider-back {
            position: absolute;

            top: 25mm;
            left: 4mm;
            right: 4mm;

            border-top: 0.4pt solid #c7ccd4;
        }


        /*
        |--------------------------------------------------------------------------
        | EMERGENCY
        |--------------------------------------------------------------------------
        */

        .emergency-title {
            position: absolute;

            top: 26.5mm;
            left: 4mm;

            font-size: 4.8pt;
            font-weight: bold;
        }

        .emergency-fields {
            position: absolute;

            top: 31mm;
            left: 4mm;

            /* Limit emergency section width */
            width: 43mm;

            font-size: 4.4pt;
        }

        .ef-row {
            height: 3mm;
            margin-bottom: 1.4mm;

            white-space: nowrap;

            overflow: hidden;
        }

        .ef-label {
            display: inline-block;

            width: 15mm;

            font-weight: bold;

            color: #17335e;
        }

        .ef-value {
            display: inline-block;

            width: 27mm;

            font-weight: normal;

            color: #1f2933;

            overflow: hidden;

            white-space: nowrap;
        }

        /* ============================================================
   MANAGER / CEO / COO SIGNATURE
   ============================================================ */

        .manager-cell {
            position: absolute;

            bottom: 1.8mm;
            right: 4mm;

            width: 30mm;
            height: 14mm;

            text-align: center;

            overflow: hidden;
        }

        .manager-script {
            height: 7mm;

            font-family: 'Zapfino', 'Brush Script MT', cursive;

            font-size: 8pt;

            line-height: 7mm;

            color: #1f2933;
        }

        .manager-line {
            width: 27mm;

            margin: 0 auto;

            border-top: 0.4pt solid #1f2933;
        }

        .manager-name {
            height: 2.8mm;

            margin-top: 0.5mm;

            font-size: 3.8pt;

            line-height: 2.8mm;

            font-weight: bold;

            color: #17335e;
        }

        .manager-title {
            font-size: 3.2pt;

            line-height: 2mm;

            color: #4b5563;
        }
    </style>
</head>


<body>


    {{-- ================================================================
         PDF PAGE 1
         FRONT OF ID
         ================================================================ --}}

    <div class="pdf-page">

        <div class="card">

            {{-- BRAND --}}

            <div class="brand">
                True Life Dayongan
            </div>


            {{-- BADGE --}}

            <div class="badge">
                PRIMARY MEMBER
            </div>


            {{-- TOP DIVIDER --}}

            <div class="divider-top"></div>


            {{-- PHOTO --}}

            <div class="photo-box">
                @if ($profilePicBase64)
                    <img src="{{ $profilePicBase64 }}" alt="Member Photo">
                @else
                    PHOTO
                @endif
            </div>


            {{-- MEMBER DETAILS --}}

            <div class="details">

                <span class="label">
                    ID No.:
                </span>

                <span class="val">
                    UL-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                </span>

                <br>


                <span class="label">
                    FULL NAME:
                </span>

                <span class="val">
                    {{ strtoupper($user->full_name ?? $user->name) }}
                </span>

                <br>


                <span class="label">
                    ADDRESS:
                </span>

                <span class="val">
                    {{ strtoupper($user->address ?? 'N/A') }}
                </span>

                <br>


                <span class="label">
                    SEX:
                </span>

                <span class="val">
                    {{ strtoupper($user->sex ?? 'N/A') }}
                </span>

                &nbsp;&nbsp;


                <span class="label">
                    STATUS:
                </span>

                <span class="val">
                    {{ strtoupper($user->civil_status ?? 'N/A') }}
                </span>

                <br>


                <span class="label">
                    BIRTH DATE:
                </span>

                <span class="val">
                    {{ \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') }}
                </span>

                <br>


                <span class="label">
                    DATE JOINED:
                </span>

                <span class="val">
                    {{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}
                </span>

                <br>

                <span class="label">
                    EXPIRATION:
                </span>

                <span class="val">
                    {{ $user->created_at
                        ? $user->created_at->copy()->addYear()->format('F d, Y')
                        : 'N/A'
                    }}
                </span>

            </div>


            {{-- QR CODE --}}

            <div class="qr-cell">

                @if (!empty($qrCode))
                    <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
                @endif

            </div>


            {{-- BENEFICIARIES DIVIDER --}}

            <div class="divider-mid"></div>


            {{-- BENEFICIARIES --}}

            <div class="ben-label">
                BENEFICIARIES:
            </div>


            <div class="ben-content">

                @if (isset($beneficiaries) && $beneficiaries->count())

                    <ul>
                        @foreach ($beneficiaries->take(3) as $beneficiary)
                            <li>
                                {{ $beneficiary->fullname }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    No beneficiaries added

                @endif

            </div>


            {{-- MEMBER SIGNATURE --}}

            <div class="sig-cell">

                @if ($signatureBase64)
                    <img src="{{ $signatureBase64 }}" class="member-signature" alt="Member Signature">
                @endif

                <div class="sig-line"></div>

                <div class="sig-caption">
                    SIGNATURE
                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
         PDF PAGE 2
         BACK OF ID
         ================================================================ --}}

    <div class="pdf-page">

        <div class="card">

            {{-- TITLE --}}

            <div class="terms-title">
                TRUE LIFE DAYONGAN TERM AND TERMINATION
            </div>


            {{-- INTRO --}}

            <div class="terms-intro">
                TRUE LIFE DAYONGAN may terminate this MEMBERSHIP,
                for the following grounds:
            </div>


            {{-- TERMS --}}

            <ul class="terms-list">

                <li>
                    Fraud or misrepresentation
                </li>

                <li>
                    Misuse of True Life Dayongan name and materials
                </li>

                <li>
                    Violation of company rules or applicable laws
                </li>

            </ul>


            {{-- NOTE --}}

            <div class="terms-note">
                Termination shall be without prejudice to any liabilities
                incurred prior to termination.
            </div>


            {{-- DIVIDER --}}

            <div class="divider-back"></div>


            {{-- EMERGENCY TITLE --}}

            <div class="emergency-title">
                IN CASE OF EMERGENCY:
            </div>


            {{-- EMERGENCY FIELDS --}}

            <div class="emergency-fields">

                <div class="ef-row">
                    <span class="ef-label">
                        NAME:
                    </span>

                    <span class="ef-value">
                        {{ $user->emergencyName ?: 'N/A' }}
                    </span>
                </div>


                <div class="ef-row">
                    <span class="ef-label">
                        ADDRESS:
                    </span>

                    <span class="ef-value">
                        {{ $user->emergencyAddress ?: 'N/A' }}
                    </span>
                </div>


                <div class="ef-row">
                    <span class="ef-label">
                        RELATIONSHIP:
                    </span>

                    <span class="ef-value">
                        {{ $user->emergencyRelationship ?: 'N/A' }}
                    </span>
                </div>


                <div class="ef-row">
                    <span class="ef-label">
                        CONTACT NO.:
                    </span>

                    <span class="ef-value">
                        {{ $user->emergencyContactno ?: 'N/A' }}
                    </span>
                </div>

            </div>

            {{-- MANAGER SIGNATURE --}}

            <div class="manager-cell">

                {{-- Signature space --}}
                <div class="manager-script">
                </div>

                {{-- Signature line --}}
                <div class="manager-line"></div>

                {{-- Signatory name --}}
                <div class="manager-name">
                    REYMARK P. BAUTISTA
                </div>

                {{-- Position --}}
                <div class="manager-title">
                    CEO / COO
                </div>

            </div>

        </div>

    </div>


</body>

</html>
