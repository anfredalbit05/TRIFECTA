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
    margin: 0 auto;
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
    margin-bottom: 12px;
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
    gap: 12px;
    align-items: flex-start;
}

.photo {
    width: 82px;
    height: 92px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #d1d5db;
}

.details {
    flex: 1;
    font-size: 12px;
    line-height: 1.35;
}

.details p {
    margin: 2px 0;
}

.details strong {
    font-weight: 700;
}

.card-divider {
    border: 0;
    border-top: 1px solid #d1d5db;
    margin: 12px 0;
}

/* FOOTER */

.footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.beneficiaries {
    width: 48%;
    font-size: 10px;
}

.beneficiaries strong {
    display: block;
    margin-bottom: 4px;
}

.beneficiaries p {
    margin: 1px 0;
}

.signature-section {
    width: 24%;
    text-align: center;
}

.signature-img {
    height: 24px;
    object-fit: contain;
    margin-bottom: -3px;
}

.signature-line {
    border-top: 1px solid #3b82f6;
}

.signature-section small {
    font-size: 7px;
    color: #777;
}

.qrcode-section {
    width: 15%;
    text-align: right;
}

.qrcode-img {
    width: 50px;
    height: 50px;
}

        /* --- BACK CARD STYLES --- */
        .back-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #0C2C6B;
            margin-top: 0;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .back-content {
            font-size: 11px;
            line-height: 1.4;
        }

        .back-content > p {
            margin: 0 0 4px 0;
        }

        .back-content ul {
            margin: 2px 0 8px 0;
            padding-left: 18px;
        }

        .back-content ul li {
            margin-bottom: 2px;
        }

        .termination-note {
            font-size: 10px;
            color: #555;
            margin-bottom: 10px;
        }

        .emergency-section {
            margin-top: 5px;
        }

        .emergency-title {
            font-weight: bold;
            margin-bottom: 6px;
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
            margin: 5px 0;
        }

        .manager-section {
            width: 35%;
            text-align: center;
        }

        .manager-line {
            border-top: 1px solid #0C2C6B;
            margin-top: 25px;
            margin-bottom: 2px;
        }

        .manager-name {
            font-size: 10px;
            font-weight: bold;
        }

        .manager-title {
            font-size: 8px;
            color: #333;
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
            UL-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
        </p>

        <p>
            <strong>FULL NAME:</strong>
            {{ strtoupper($user->firstname.' '.$user->middlename.' '.$user->lastname) }}
        </p>

        <p>
            <strong>ADDRESS:</strong>
            {{ strtoupper($user->address ?? 'N/A') }}
        </p>

        <p>
            <strong>SEX:</strong> {{ strtoupper($user->sex ?? 'N/A') }}

            &nbsp;&nbsp;

            <strong>STATUS:</strong>
            {{ strtoupper($user->civilStatus ?? 'N/A') }}
        </p>

        <p>
            <strong>BIRTH DATE:</strong>

            {{ $user->birthdate
                ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y')
                : 'N/A' }}
        </p>

        <p>
            <strong>DATE JOINED:</strong>

            {{ $user->created_at
                ? $user->created_at->format('F d, Y')
                : 'N/A' }}
        </p>

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
                        <p>• None specified</p>
                    @endif
                </div>

                <div class="signature-section">
                    @if(!empty($user->signature) && file_exists(public_path($user->signature)))
                        <img class="signature-img" src="{{ public_path($user->signature) }}" alt="Signature">
                    @else
                        <div style="height: 30px;"></div>
                    @endif
                    <div class="signature-line"></div>
                    <small>SIGNATURE</small>
                </div>

                <div class="qrcode-section">
                    @if(!empty($user->qrcode) && file_exists(public_path($user->qrcode)))
                        <img class="qrcode-img" src="{{ public_path($user->qrcode) }}" alt="QR Code">
                    @else
                        <img class="qrcode-img" src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=UL-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}" alt="QR Code">
                    @endif
                </div>
            </div>
        </div>

        <!-- ==================== BACK CARD ==================== -->
        <div class="card">
            <h3 class="back-title">UNITYLIFE TERM AND TERMINATION</h3>

            <div class="back-content">
                <p>UNITYLIFE may terminate this MEMBERSHIP, for the following grounds:</p>
                <ul>
                    <li>Fraud or misrepresentation</li>
                    <li>Misuse of Unitylife name and materials</li>
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
                                <div style="font-family: 'Brush Script MT', cursive, sans-serif; font-size: 20px; color: #111;">Sey</div>
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
