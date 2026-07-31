<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin: 0 auto; }
        h2 { color: #2d3748; border-bottom: 2px solid #edf2f7; padding-bottom: 10px; margin-top: 0; }
        .section-title { font-size: 16px; font-weight: bold; color: #4a5568; margin-top: 20px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        th { background-color: #f7fafc; color: #718096; width: 35%; }
        td { color: #2d3748; }
        .beneficiary-box { background: #fdfdfd; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 6px; margin-bottom: 8px; }
        .footer { margin-top: 30px; font-size: 12px; color: #a0aec0; text-align: center; border-top: 1px solid #edf2f7; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ $emailTitle }}</h2>
        <p>{{ $introMessage }}</p>

        <!-- Target User Info -->
        <div class="section-title">Target Account Details</div>
        <table>
            <tr><th>Full Name</th><td>{{ trim(($selectedUser->firstname ?? '') . ' ' . ($selectedUser->middlename ?? '') . ' ' . ($selectedUser->lastname ?? '')) ?: 'N/A' }}</td></tr>
            <tr><th>Contact No</th><td>{{ $selectedUser->contactno ?? 'N/A' }}</td></tr>
            <tr><th>Gender</th><td>{{ $selectedUser->gender ?? 'N/A' }} </td></tr>
            <tr><th>Civil Status</th><td>{{ $selectedUser->civilstatus ?? 'N/A' }}</td></tr>
            <tr><th>Birthdate</th><td>{{ $selectedUser->birthdate ?? 'N/A' }}</td></tr>
            <tr><th>Address</th><td>{{ $selectedUser->address ?? 'N/A' }}</td></tr>
            <tr><th>Referral Code</th><td>{{ $selectedUser->referralCode ?? 'N/A' }}</td></tr>
        </table>

        <!-- Processor / Submitter Info -->
        <div class="section-title">Submitted / Processed By</div>
        <table>
            <tr><th>Username</th><td>{{ $currentUser->username }}</td></tr>
            <tr><th>Full Name</th><td>{{ trim(($currentUser->firstname ?? '') . ' ' . ($currentUser->middlename ?? '') . ' ' . ($currentUser->lastname ?? '')) ?: 'N/A' }}</td></tr>
            <tr><th>Contact No</th><td>{{ $currentUser->contactno ?? 'N/A' }}</td></tr>
        </table>

        <!-- Payment Details -->
        <div class="section-title">Payment Details</div>
        <table>
            <tr><th>Payment Method</th><td><strong>{{ $payment_method }}</strong></td></tr>
            <tr><th>Amount</th><td>₱{{ number_format($amount, 2) }}</td></tr>
            <tr><th>Upline Code</th><td>{{ $upline_code }}</td></tr>
        </table>

        <!-- Beneficiaries -->
        <div class="section-title">Beneficiaries</div>
        @if(!empty($formattedBeneficiaries))
            @foreach($formattedBeneficiaries as $index => $b)
                <div class="beneficiary-box">
                    <strong>Beneficiary {{ $index + 1 }}</strong><br>
                    - Full Name: {{ $b['fullname'] ?? 'N/A' }}<br>
                    - Birthdate: {{ $b['birthdate'] ?? 'N/A' }}<br>
                    - Relationship: {{ $b['relationship'] ?? 'N/A' }}
                </div>
            @endforeach
        @else
            <p><em>No beneficiaries provided.</em></p>
        @endif

        <div class="footer">
            &copy; {{ date('Y') }} Your System Name. All rights reserved.
        </div>
    </div>
</body>
</html>
