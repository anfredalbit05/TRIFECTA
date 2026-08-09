<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Card</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 10px;
            color: #111;
        }

        .wrapper {
            width: 100%;
        }

        .card {
            width: 100%;
            max-width: 760px;
            margin: 0 auto 20px auto;
            background: #fff;
            border: 1px solid #d9d9d9;
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        /* HEADER */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            color: #0c2c6b;
        }

        .badge {
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 10px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* BODY */

        .content {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .photo {
            width: 82px;
            height: 92px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            background: #f1f5f9;
        }

        .details {
            flex: 1;
            font-size: 12px;
            line-height: 1.5;
            color: #111;
        }

        .details p {
            margin: 2px 0;
        }

        .details strong {
            font-weight: 700;
            color: #111;
        }

        .details .val {
            color: #1d4ed8;
            font-weight: 600;
        }

        .qrcode-top {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .qrcode-top img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-divider {
            border: 0;
            border-top: 1.5px solid #000;
            margin: 14px 0 12px 0;
        }

        /* FOOTER */

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .beneficiaries {
            width: 55%;
            font-size: 11px;
        }

        .beneficiaries strong {
            display: block;
            margin-bottom: 4px;
            color: #334155;
        }

        .beneficiaries p {
            margin: 1px 0;
            color: #64748b;
        }

        .signature-section {
            width: 40%;
            text-align: center;
        }

        .signature-img {
            height: 26px;
            object-fit: contain;
            margin-bottom: -3px;
        }

        .signature-placeholder {
            font-style: italic;
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
        }

        .signature-section small {
            font-size: 8px;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        /* --- BACK CARD STYLES --- */
        .back-title {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #0c2c6b;
            margin-top: 0;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }

        .back-content {
            font-size: 11px;
            line-height: 1.5;
        }

        .back-content > p.intro {
            margin: 0 0 6px 0;
            font-weight: 700;
            color: #111;
        }

        .back-content ul {
            margin: 2px 0 8px 0;
            padding-left: 18px;
        }

        .back-content ul li {
            margin-bottom: 3px;
            color: #475569;
        }

        .termination-note {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .emergency-section {
            margin-top: 6px;
        }

        .emergency-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: #111;
        }

        .emergency-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .emergency-fields {
            width: 60%;
            font-size: 11px;
        }

        .emergency-fields p {
            margin: 6px 0;
            color: #111;
        }

        .emergency-fields strong {
            font-weight: 700;
        }

        .manager-section {
            width: 35%;
            text-align: center;
        }

        .manager-signature-script {
            font-family: 'Brush Script MT', cursive, sans-serif;
            font-size: 20px;
            color: #111;
        }

        .manager-line {
            border-top: 1px solid #0C2C6B;
            margin-top: 22px;
            margin-bottom: 3px;
        }

        .manager-name {
            font-size: 10px;
            font-weight: 700;
            color: #0c2c6b;
        }

        .manager-title {
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="wrapper">

        <!-- ==================== FRONT CARD ==================== -->
        <div class="card">
            <div class="header">
                <div class="title">True Life Dayongan</div>
                <div class="badge">PRIMARY MEMBER</div>
            </div>

            <div class="content">

                <div class="photo-box">
                    <img class="photo"
                         src="{{ !empty($user->profilepic)
                                   ? public_path('storage/'.$user->profilepic)
                                   : public_path('images/profiles/default-avatar.png') }}">
                </div>

                <div class="details">
                    <p><strong>ID No.:</strong>
                        <span class="val">UL-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </p>

                    <p>
                        <strong>FULL NAME:</strong>
                        <span class="val">{{ strtoupper($user->firstname.' '.$user->middlename.' '.$user->lastname) }}</span>
                    </p>

                    <p>
                        <strong>ADDRESS:</strong>
                        <span class="val">{{ strtoupper($user->address ?? 'N/A') }}</span>
                    </p>

                    <p>
                        <strong>SEX:</strong> <span class="val">{{ strtoupper($user->sex ?? 'N/A') }}</span>

                        &nbsp;&nbsp;

                        <strong>STATUS:</strong>
                        <span class="val">{{ strtoupper($user->civilStatus ?? 'N/A') }}</span>
                    </p>

                    <p>
                        <strong>BIRTH DATE:</strong>
                        <span class="val">{{ $user->birthdate
                            ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y')
                            : 'N/A' }}</span>
                    </p>

                    <p>
                        <strong>DATE JOINED:</strong>
                        <span class="val">{{ $user->created_at
                            ? $user->created_at->format('F d, Y')
                            : 'N/A' }}</span>
                    </p>
                </div>

                <div class="qrcode-top">
                    @if(!empty($user->qrcode) && file_exists(public_path($user->qrcode)))
                        <img src="{{ public_path($user->qrcode) }}" alt="QR Code">
                    @else
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=UL-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}" alt="QR Code">
                    @endif
                </div>

            </div>

            <hr class="card-divider">

            <div class="footer">
                <div class="beneficiaries">
                    <strong>BENEFICIARIES:</strong>
                    @if($user->beneficiaries && $user->beneficiaries->count() > 0)
                        @foreach($user->beneficiaries->take(3) as $b)
                            <p>• {{ $b->fullname }}</p>
                        @endforeach
                    @else
                        <p>No beneficiaries added</p>
                    @endif
                </div>

                <div class="signature-section">
                    @if(!empty($user->signature) && file_exists(public_path($user->signature)))
                        <img class="signature-img" src="{{ public_path($user->signature) }}" alt="Signature">
                    @else
                        <div class="signature-placeholder">No Signature</div>
                    @endif
                    <div class="signature-line"></div>
                    <small>SIGNATURE</small>
                </div>
            </div>
        </div>

        <!-- ==================== BACK CARD ==================== -->
        <div class="card">
            <h3 class="back-title">TRUE LIFE DAYONGAN TERM AND TERMINATION</h3>

            <div class="back-content">
                <p class="intro">TRUE LIFE DAYONGAN may terminate this MEMBERSHIP, for the following grounds:</p>
                <ul>
                    <li>Fraud or misrepresentation</li>
                    <li>Misuse of True Life Dayongan name and materials</li>
                    <li>Violation of company rules or applicable laws</li>
                </ul>
                <div class="termination-note">Termination shall be without prejudice to any liabilities incurred prior to termination.</div>

                <hr class="card-divider">

                <div class="emergency-section">
                    <div class="emergency-title">IN CASE OF EMERGENCY:</div>

                    <div class="emergency-grid">
                        <div class="emergency-fields">
                            <p><strong>NAME:</strong> {{ !empty($user->emergencyName) ? strtoupper($user->emergencyName) : '_________________________' }}</p>
                            <p><strong>ADDRESS:</strong> {{ !empty($user->emergencyAddress) ? strtoupper($user->emergencyAddress) : '_________________________' }}</p>
                            <p><strong>RELATIONSHIP:</strong> {{ !empty($user->emergencyRelationship) ? strtoupper($user->emergencyRelationship) : '_________________________' }}</p>
                            <p><strong>CONTACT NO.:</strong> {{ !empty($user->emergencyContactno) ? strtoupper($user->emergencyContactno) : '_________________________' }}</p>
                        </div>

                        <div class="manager-section">
                            @if(!empty($managerSignature) && file_exists(public_path($managerSignature)))
                                <img src="{{ public_path($managerSignature) }}" alt="Manager Signature" style="height: 25px; object-fit: contain; display: block; margin: 0 auto;">
                            @else
                                <div class="manager-signature-script">Sey</div>
                            @endif
                            <div class="manager-line"></div>
                            <div class="manager-name">REYMARK P. BAUTISTA</div>
                            <div class="manager-title">GENERAL MANAGER</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
