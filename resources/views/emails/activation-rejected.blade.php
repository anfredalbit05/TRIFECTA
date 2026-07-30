<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body style="font-family: Arial, sans-serif;">

    <h2>Hello, {{ $user->firstname }} {{ $user->lastname }}</h2>

    <p>
        We regret to inform you that your account activation request has been rejected.
    </p>

    <h3>Member Information</h3>

    <ul>
        <li>
            <strong>Full Name:</strong>
            {{ $user->firstname }}
            {{ $user->middlename }}
            {{ $user->lastname }}
        </li>

        <li>
            <strong>Email:</strong>
            {{ $user->email }}
        </li>

        <li>
            <strong>Contact Number:</strong>
            {{ $user->contactno }}
        </li>

        <li>
            <strong>Payment Method:</strong>
            {{ $activation->payment_method }}
        </li>

        <li>
            <strong>Amount:</strong>
            ₱{{ number_format($activation->amount, 2) }}
        </li>

        <li>
            <strong>Reference Number:</strong>
            {{ $activation->payment_reference ?? 'Cash Payment' }}
        </li>

        <li>
            <strong>Upline Code:</strong>
            {{ $activation->upline_code }}
        </li>
    </ul>

    <h3>Beneficiaries</h3>

    <ul>
        @foreach ($beneficiaries as $beneficiary)
            <li>
                {{ $beneficiary['fullname'] }}
                —
                {{ $beneficiary['relationship'] }}
                —
                {{ $beneficiary['birthdate'] }}
            </li>
        @endforeach
    </ul>

    <h3 style="color: red;">
        Reason for Rejection
    </h3>

    <p>
        {{ $remarks }}
    </p>

    <br>

    <p>
        Please edit your activation details and submit a new activation request.
    </p>

    <p>
        Thank you,<br>
        {{ env('MAIL_FROM_NAME') }}
    </p>

</body>

</html>
