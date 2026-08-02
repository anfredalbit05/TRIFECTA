<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Application Details' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
        .card { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        h2 { color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        h4 { color: #34495e; margin-top: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        ul { padding-left: 20px; }
        li { margin-bottom: 5px; }
        .creds { background: #eef2f7; padding: 10px 15px; border-radius: 4px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>{{ $title ?? 'Application Summary' }}</h2>
            <p>Here are the complete details submitted for the application:</p>

            <h4>Personal Information</h4>
            <ul>
                <li><strong>Full Name:</strong> {{ $first_name }} {{ $middle_name }} {{ $surname }}</li>
                <li><strong>Gender:</strong> {{ $gender }}</li>
                <li><strong>Civil Status:</strong> {{ $civil_status }}</li>
                <li><strong>Birthdate:</strong> {{ $birthdate }}</li>
                <li><strong>Address:</strong> {{ $address }}</li>
            </ul>

            <h4>Contact & Security</h4>
            <ul>
                <li><strong>Mobile Number:</strong> {{ $mobile }}</li>
                <li><strong>Email Address:</strong> {{ $email ?? 'N/A' }}</li>
                <li><strong>Username:</strong> {{ $username }}</li>
            </ul>

            @if(isset($includeCredentials) && $includeCredentials && isset($password))
                <div class="creds">
                    <strong>Your Account Password:</strong> {{ $password }}<br>
                    <small>Please keep this information secure.</small>
                </div>
            @endif

            <h4>Beneficiaries</h4>
            <ul>
                @forelse($beneficiaries as $beneficiary)
                    @if(!empty($beneficiary['fullname']))
                        <li>
                            {{ $beneficiary['fullname'] }}
                            ({{ $beneficiary['relationship'] ?? 'Relationship not specified' }})
                            - Born: {{ $beneficiary['birthdate'] ?? 'N/A' }}
                        </li>
                    @endif
                @empty
                    <li>No beneficiaries listed.</li>
                @endforelse
            </ul>

            <p style="margin-top: 30px; font-size: 12px; color: #777;">
                This is an automated notification message from True Life. Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
