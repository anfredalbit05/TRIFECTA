<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation Request Rejected</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155;">

    <!-- Email Wrapper Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">

                    <!-- Header Section -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #0B2A5B 0%, #123e82 100%); padding: 40px 30px; color: #ffffff;">

                            <!-- Logo Image -->
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="background-color: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 12px; backdrop-filter: blur(8px);">
                                        <img src="{{ asset('images/dayongan-logo.png') }}" alt="Dayongan Logo" width="64" height="64" style="display: block; width: 64px; height: 64px; object-fit: contain;">
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 20px 0 0 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; color: #ffffff;">Activation Request Rejected</h1>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">
                            <p style="margin: 0 0 16px 0;">Hello, <strong>{{ $user->firstname }} {{ $user->lastname }}</strong></p>

                            <p style="margin: 0 0 24px 0; color: #475569;">We regret to inform you that your account activation request has been rejected.</p>

                            <!-- Reason for Rejection Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #991b1b; letter-spacing: 0.05em;">Reason for Rejection</p>
                                        <p style="margin: 0; font-size: 14px; color: #7f1d1d; font-weight: 500;">{{ $remarks }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Member Information Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #1e293b;">Member Information</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Full Name</td>
                                                <td align="right" style="padding-bottom: 12px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Email</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Contact Number</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $user->contactno }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Payment Method</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $activation->payment_method }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Amount</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #1e293b;">₱{{ number_format($activation->amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Reference Number</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $activation->payment_reference ?? 'Cash Payment' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Upline Code</td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $activation->upline_code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Beneficiaries Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #1e293b;">Beneficiaries</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            @foreach ($beneficiaries as $beneficiary)
                                                <tr>
                                                    <td style="padding: 8px 0; {{ !$loop->first ? 'border-top: 1px solid #e2e8f0;' : '' }} font-size: 14px; color: #1e293b;">
                                                        <strong>{{ $beneficiary['fullname'] }}</strong>
                                                        <span style="color: #64748b; display: block; font-size: 12px; margin-top: 2px;">
                                                            Relationship: {{ $beneficiary['relationship'] }} &bull; Birthdate: {{ $beneficiary['birthdate'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px 0; color: #475569;">Please edit your activation details and submit a new activation request.</p>

                            <p style="margin: 0;">
                                Best regards,<br>
                                <strong style="color: #0B2A5B;">True Life Dayongan</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9;">
                            <p style="margin: 0;">&copy; {{ date('Y') }} True Life Dayongan. All rights reserved.</p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
