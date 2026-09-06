<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155;">

    <!-- Email Wrapper Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">

                    <!-- Header Section -->
                    <tr>
                        <td align="center"
                            style="background: linear-gradient(135deg, #0B2A5B 0%, #123e82 100%); padding: 40px 30px; color: #ffffff;">

                            <!-- Logo Image / Placeholder Container -->
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"
                                        style="background-color: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 12px; backdrop-filter: blur(8px);">
                                        <img src="{{ asset('images/truelife-logo.png') }}" alt="Logo" width="64"
                                            height="64"
                                            style="display: block; width: 64px; height: 64px; object-fit: contain;">
                                    </td>
                                </tr>
                            </table>

                            <h1
                                style="margin: 20px 0 0 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; color: #ffffff;">
                                Reset Your Password</h1>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 12px 0; color: #334155;">
                                Hi {{ $user->firstname ?? 'Member' }},
                            </p>
                            <p style="margin: 0 0 28px 0; color: #475569;">
                                We received a request to reset the password for your
                                <strong style="color: #0B2A5B;">Truelife member portal</strong> account. Click the
                                button below to choose a new password.
                            </p>

                            <!-- Reset Button Card -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding: 32px 24px;">

                                        <!-- Primary Action Button -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" align="center"
                                            style="margin: 0 auto;">
                                            <tr>
                                                <td align="center"
                                                    style="border-radius: 12px; background-color: #0B2A5B;">
                                                    <a href="{{ $resetUrl }}" target="_blank"
                                                        style="display: inline-block; padding: 15px 36px; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 12px;">
                                                        Reset My Password
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Fallback Link -->
                                        <p style="margin: 20px 0 0 0; font-size: 12px; color: #94a3b8;">
                                            If the button above does not work, copy and paste this link into your
                                            browser:
                                        </p>
                                        <p style="margin: 6px 0 0 0; font-size: 12px; color: #2563eb;">
                                            <a href="{{ $resetUrl }}" target="_blank"
                                                style="color: #2563eb; text-decoration: underline; word-break: break-all;">
                                                {{ $resetUrl }}
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice Card -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #D4A017; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p
                                            style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #92400e; letter-spacing: 0.05em;">
                                            Need Help?</p>
                                        <p style="margin: 0; font-size: 13px; color: #78350f; line-height: 1.6;">
                                            This reset link will expire in <strong>60 minutes</strong>. If you did not
                                            request a password reset, no further action is needed and your password
                                            will remain unchanged. For assistance, please contact our support team or
                                            your nearest Truelife branch office.
                                        </p>
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
                        <td align="center"
                            style="background-color: #f8fafc; padding: 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9;">
                            <p style="margin: 0 0 4px 0;">This is an automated notification message from True Life.
                                Please do not reply directly to this email.</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} True Life. All rights reserved.</p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
