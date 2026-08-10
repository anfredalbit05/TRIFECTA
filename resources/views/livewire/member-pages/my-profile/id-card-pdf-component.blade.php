{{-- resources/views/livewire/member-pages/my-profile/id-card-pdf-component.blade.php --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        {!! file_get_contents(resource_path('css/id-card.css')) !!}
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
                <span class="label">ID No.:</span>
                <span class="val">UL-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                <br>

                <span class="label">FULL NAME:</span>
                <span class="val">{{ strtoupper($user->firstname . ' ' . $user->middlename . ' ' . $user->lastname) }}</span>
                <br>

                <span class="label">ADDRESS:</span>
                <span class="val">{{ strtoupper($user->address ?? 'N/A') }}</span>
                <br>

                <span class="label">SEX:</span>
                <span class="val">{{ strtoupper($user->gender ?? 'N/A') }}</span>
                &nbsp;&nbsp;

                <span class="label">STATUS:</span>
                <span class="val">{{ strtoupper($user->civilstatus ?? 'N/A') }}</span>
                <br>

                <span class="label">BIRTH DATE:</span>
                <span class="val">{{ \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') }}</span>
                <br>

                <span class="label">DATE JOINED:</span>
                <span class="val">{{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}</span>
                <br>

                <span class="label">EXPIRATION DATE:</span>
                <span class="val">
                    {{ $user->created_at ? $user->created_at->copy()->addYear()->format('F d, Y') : 'N/A' }}
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
                            <li>{{ $beneficiary->fullname }}</li>
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
                <div class="sig-caption">SIGNATURE</div>
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
                TRUE LIFE DAYONGAN may terminate this MEMBERSHIP, for the following grounds:
            </div>

            {{-- TERMS --}}
            <ul class="terms-list">
                <li>Fraud or misrepresentation</li>
                <li>Misuse of True Life Dayongan name and materials</li>
                <li>Violation of company rules or applicable laws</li>
            </ul>

            {{-- NOTE --}}
            <div class="terms-note">
                Termination shall be without prejudice to any liabilities incurred prior to termination.
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
                    <span class="ef-label">NAME:</span>
                    <span class="ef-value">{{ $user->emergencyName ?: 'N/A' }}</span>
                </div>

                <div class="ef-row">
                    <span class="ef-label">ADDRESS:</span>
                    <span class="ef-value">{{ $user->emergencyAddress ?: 'N/A' }}</span>
                </div>

                <div class="ef-row">
                    <span class="ef-label">RELATIONSHIP:</span>
                    <span class="ef-value">{{ $user->emergencyRelationship ?: 'N/A' }}</span>
                </div>

                <div class="ef-row">
                    <span class="ef-label">CONTACT NO.:</span>
                    <span class="ef-value">{{ $user->emergencyContactno ?: 'N/A' }}</span>
                </div>
            </div>

            {{-- MANAGER SIGNATURE --}}
            <div class="manager-cell">
                <div class="manager-script"></div>
                <div class="manager-line"></div>
                <div class="manager-name">RENANTE SUAYBAGUIO-ROBLES, MPA, LFA</div>
                <div class="manager-title">CEO</div>
            </div>

        </div>

    </div>

</body>

</html>
