<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitee Approved</title>
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
                                        <img src="{{ asset('images/dayongan-logo.png') }}" alt="Logo" width="64" height="64" style="display: block; width: 64px; height: 64px; object-fit: contain;">
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 20px 0 0 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; color: #ffffff;">Invitee Approved!</h1>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">
                            <p style="margin: 0 0 16px 0; color: #475569; font-size: 16px;">Hello, <strong style="color: #1e293b;">{{ $referralUser->firstname }}</strong>!</p>

                            <p style="margin: 0 0 24px 0; color: #475569;">Good news! Your invitee has been approved successfully and has joined your network.</p>

                            <!-- Invitee Success Card -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #22c55e; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #166534; letter-spacing: 0.05em;">Approved Invitee</p>
                                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #14532d;">{{ $activatedUser->firstname }} {{ $activatedUser->lastname }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Activation Information Card -->
                            <h3 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">Activation Details</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Activation ID</td>
                                                <td align="right" style="padding-bottom: 12px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $activation->ActivationID }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Amount</td>
                                                <td align="right" style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #16a34a;">₱{{ number_format($activation->amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Status</td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #16a34a;">{{ $activation->status }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px 0; color: #475569;">Thank you for inviting new members and expanding your network.</p>

                            <p style="margin: 0; color: #475569;">
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
