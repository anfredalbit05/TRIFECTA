<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailTitle ?? 'Application Details' }}</title>
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

                            <!-- Logo Placeholder Container -->
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="background-color: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 12px; backdrop-filter: blur(8px);">
                                        <img src="{{ asset('images/truelife-logo.png') }}" alt="Logo" width="64" height="64" style="display: block; width: 64px; height: 64px; object-fit: contain;">
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 20px 0 0 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; color: #ffffff;">{{ $emailTitle ?? 'Application Details' }}</h1>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">

                            <!-- Intro Message -->
                            <p style="margin: 0 0 28px 0; color: #475569; font-size: 16px;">{{ $introMessage }}</p>

                            <!-- Target Account Details Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">Target Account Details</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Full Name</td>
                                                <td align="right" style="padding-bottom: 12px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ trim(($selectedUser->firstname ?? '') . ' ' . ($selectedUser->middlename ?? '') . ' ' . ($selectedUser->lastname ?? '')) ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Contact No</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $selectedUser->contactno ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Gender</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $selectedUser->gender ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Civil Status</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $selectedUser->civilstatus ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Birthdate</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $selectedUser->birthdate ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Address</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $selectedUser->address ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Referral Code</td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #0284c7;">{{ $selectedUser->referralCode ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Submitted / Processed By Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">Submitted / Processed By</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Username</td>
                                                <td align="right" style="padding-bottom: 12px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $currentUser->username }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Full Name</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ trim(($currentUser->firstname ?? '') . ' ' . ($currentUser->middlename ?? '') . ' ' . ($currentUser->lastname ?? '')) ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Contact No</td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $currentUser->contactno ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Payment Details Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">Payment Details</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #166534; letter-spacing: 0.05em;">Payment Method</td>
                                                <td align="right" style="padding-bottom: 12px; font-size: 14px; font-weight: 700; color: #14532d;">{{ $payment_method }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #bbf7d0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #166534; letter-spacing: 0.05em;">Amount</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #bbf7d0; font-size: 16px; font-weight: 800; color: #16a34a;">₱{{ number_format($amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #bbf7d0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #166534; letter-spacing: 0.05em;">Upline Code</td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #bbf7d0; font-size: 14px; font-weight: 600; color: #14532d;">{{ $upline_code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Beneficiaries Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">Beneficiaries</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            @forelse($formattedBeneficiaries as $index => $b)
                                                <tr>
                                                    <td style="padding: 12px 0; {{ !$loop->first ? 'border-top: 1px solid #e2e8f0;' : '' }} font-size: 14px; color: #1e293b;">
                                                        <strong style="color: #0B2A5B;">Beneficiary {{ $index + 1 }}</strong><br>
                                                        <span style="display: block; font-size: 14px; font-weight: 600; margin-top: 4px; color: #1e293b;">{{ $b['fullname'] ?? 'N/A' }}</span>
                                                        <span style="color: #64748b; display: block; font-size: 12px; margin-top: 2px;">
                                                            Relationship: {{ $b['relationship'] ?? 'N/A' }} &bull; Born: {{ $b['birthdate'] ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td style="padding: 12px 0; font-size: 14px; color: #64748b; font-style: italic;">No beneficiaries provided.</td>
                                                </tr>
                                            @endforelse
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #475569;">
                                Best regards,<br>
                                <strong style="color: #0B2A5B;">True Life</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9;">
                            <p style="margin: 0;">&copy; {{ date('Y') }} True Life. All rights reserved.</p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>