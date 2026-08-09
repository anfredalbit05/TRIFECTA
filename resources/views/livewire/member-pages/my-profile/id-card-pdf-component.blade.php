{{-- resources/views/livewire/member-pages/my-profile/id-card-pdf-component.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 30px 35px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2933;
            font-size: 12px;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* ===== Header ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            color: #1a2b4c;
        }

        .badge {
            text-align: right;
        }

        .badge span {
            display: inline-block;
            background-color: #e6f0fd;
            color: #2563eb;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            padding: 5px 12px;
            border-radius: 12px;
        }

        .divider {
            border-top: 1.5px solid #1a2b4c;
            margin: 6px 0 14px 0;
        }

        .divider-light {
            border-top: 1px solid #c7ccd4;
            margin: 10px 0;
        }

        /* ===== Info section ===== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .photo-cell {
            width: 100px;
            vertical-align: top;
        }

        .photo-box {
            width: 95px;
            height: 115px;
            border: 1px solid #9aa1ac;
            text-align: center;
            vertical-align: middle;
            color: #9aa1ac;
            font-size: 9px;
        }

        .details-cell {
            vertical-align: top;
            padding-left: 14px;
            font-size: 11px;
            line-height: 20px;
        }

        .details-cell .label {
            font-weight: bold;
            color: #1f2933;
        }

        .qr-cell {
            width: 90px;
            text-align: right;
            vertical-align: top;
        }

        .qr-cell img {
            width: 80px;
            height: 80px;
        }

        /* ===== Beneficiaries ===== */
        .section-label {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .beneficiary-empty {
            color: #9aa1ac;
            font-size: 11px;
            font-style: normal;
        }

        .beneficiary-list {
            font-size: 11px;
            margin: 0;
            padding-left: 16px;
        }

        /* ===== Signature ===== */
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .signature-cell {
            width: 220px;
            float: right;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #1f2933;
            width: 200px;
            margin: 0 0 4px auto;
        }

        .signature-caption {
            font-size: 9px;
            letter-spacing: 1px;
            color: #4b5563;
        }

        /* ===== Terms page ===== */
        .terms-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #1a2b4c;
            margin-bottom: 14px;
        }

        .terms-intro {
            font-size: 11px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .terms-list {
            font-size: 11px;
            margin: 0 0 14px 0;
            padding-left: 18px;
        }

        .terms-list li {
            margin-bottom: 4px;
            color: #1f2933;
        }

        .terms-note {
            font-size: 10.5px;
            font-style: italic;
            color: #6b7280;
            margin-bottom: 40px;
        }

        .emergency-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .emergency-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .emergency-table td {
            padding-bottom: 14px;
        }

        .emergency-label {
            font-weight: bold;
            width: 110px;
        }

        .emergency-fill {
            border-bottom: 1px solid #1f2933;
        }

        .manager-sign-table {
            width: 100%;
            margin-top: 50px;
        }

        .manager-sign-cell {
            width: 220px;
            float: right;
            text-align: center;
        }

        .manager-script {
            font-family: 'Zapfino', 'Brush Script MT', cursive;
            font-size: 22px;
            color: #1f2933;
        }

        .manager-line {
            border-top: 1px solid #1f2933;
            width: 200px;
            margin: 0 0 4px auto;
        }

        .manager-name {
            font-size: 10px;
            font-weight: bold;
            color: #1a2b4c;
        }

        .manager-title {
            font-size: 9px;
            color: #4b5563;
        }
    </style>
</head>
<body>

    {{-- ================= PAGE 1: ID CARD ================= --}}
    <div class="page">
        <table class="header-table">
            <tr>
                <td class="brand">True Life Dayongan</td>
                <td class="badge"><span>PRIMARY MEMBER</span></td>
            </tr>
        </table>
        <div class="divider"></div>

        <table class="info-table">
            <tr>
                <td class="photo-cell">
                    <div class="photo-box">
                        @if(!empty($user->photo_path) && file_exists(public_path($user->photo_path)))
                            <img src="{{ public_path($user->photo_path) }}" width="95" height="115">
                        @else
                            PHOTO
                        @endif
                    </div>
                </td>
                <td class="details-cell">
                    <span class="label">ID No.:</span> UL-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <span class="label">FULL NAME:</span> {{ strtoupper($user->full_name ?? $user->name) }}<br>
                    <span class="label">ADDRESS:</span> {{ strtoupper($user->address ?? 'N/A') }}<br>
                    <span class="label">SEX:</span> {{ strtoupper($user->sex ?? 'N/A') }}
                    &nbsp;&nbsp;&nbsp;
                    <span class="label">STATUS:</span> {{ strtoupper($user->civil_status ?? 'N/A') }}<br>
                    <span class="label">BIRTH DATE:</span> {{ \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') }}<br>
                    <span class="label">DATE JOINED:</span> {{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}
                </td>
                <td class="qr-cell">
                    @if(!empty($qrCode))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}">
                    @endif
                </td>
            </tr>
        </table>

        <div class="divider-light"></div>

        <div class="section-label">BENEFICIARIES:</div>
        @if(isset($beneficiaries) && $beneficiaries->count())
            <ul class="beneficiary-list">
                @foreach($beneficiaries as $beneficiary)
                    <li>{{ $beneficiary->full_name ?? $beneficiary->name }}</li>
                @endforeach
            </ul>
        @else
            <div class="beneficiary-empty">No beneficiaries added</div>
        @endif

        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-cell">
                        <div class="signature-line"></div>
                        <div class="signature-caption">SIGNATURE</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= PAGE 2: TERMS ================= --}}
    <div class="page">
        <div class="terms-title">TRUE LIFE DAYONGAN TERM AND TERMINATION</div>

        <div class="terms-intro">
            TRUE LIFE DAYONGAN may terminate this MEMBERSHIP, for the following grounds:
        </div>

        <ul class="terms-list">
            <li>Fraud or misrepresentation</li>
            <li>Misuse of True Life Dayongan name and materials</li>
            <li>Violation of company rules or applicable laws</li>
        </ul>

        <div class="terms-note">
            Termination shall be without prejudice to any liabilities incurred prior to termination.
        </div>

        <div class="divider-light"></div>

        <div class="emergency-title">IN CASE OF EMERGENCY:</div>
        <table class="emergency-table">
            <tr>
                <td class="emergency-label">NAME:</td>
                <td class="emergency-fill">&nbsp;</td>
            </tr>
            <tr>
                <td class="emergency-label">ADDRESS:</td>
                <td class="emergency-fill">&nbsp;</td>
            </tr>
            <tr>
                <td class="emergency-label">RELATIONSHIP:</td>
                <td class="emergency-fill">&nbsp;</td>
            </tr>
            <tr>
                <td class="emergency-label">CONTACT NO.:</td>
                <td class="emergency-fill">&nbsp;</td>
            </tr>
        </table>

        <table class="manager-sign-table">
            <tr>
                <td>
                    <div class="manager-sign-cell">
                        <div class="manager-script">Sey</div>
                        <div class="manager-line"></div>
                        <div class="manager-name">REYMARK P. BAUTISTA</div>
                        <div class="manager-title">GENERAL MANAGER</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>