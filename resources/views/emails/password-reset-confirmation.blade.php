<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful</title>
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
                                Password Reset Successful</h1>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 12px 0; color: #334155;">
                                Hi {{ $user->firstname ?? 'Member' }},
                            </p>
                            <p style="margin: 0 0 24px 0; color: #475569;">
                                This email confirms that the password for your
                                <strong style="color: #0B2A5B;">TRIFECTA member portal</strong> account has been
                                changed successfully.
                            </p>

                            <!-- New Password Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #3b82f6; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p
                                            style="margin: 0 0 4px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e40af; letter-spacing: 0.05em;">
                                            Your New Password</p>
                                        <p style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #1e3a8a; word-break: break-all;">
                                            {{ $newPassword }}</p>
                                        <p style="margin: 0; font-size: 13px; color: #3b82f6;">Please keep this
                                            information secure. You can change it anytime under
                                            <strong>My Profile &gt; Security</strong> after signing in.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Sign In CTA -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding: 24px;">
                                        <p style="margin: 0 0 16px 0; font-size: 14px; color: #475569;">
                                            Use your new password to sign in to your account.
                                        </p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" align="center"
                                            style="margin: 0 auto;">
                                            <tr>
                                                <td align="center" style="border-radius: 12px; background-color: #0B2A5B;">
                                                    <a href="{{ route('secret.login') }}" target="_blank"
                                                        style="display: inline-block; padding: 14px 34px; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 12px;">
                                                        Sign In Now
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #475569;">
                                If you did not request this change, please contact our support team or your nearest
                                TRIFECTA branch office immediately.
                            </p>

                            <p style="margin: 24px 0 0 0; color: #475569;">
                                Best regards,<br>
                                <strong style="color: #0B2A5B;">TRIFECTA</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td align="center"
                            style="background-color: #f8fafc; padding: 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9;">
                            <p style="margin: 0 0 4px 0;">This is an automated notification message from TRIFECTA.
                                Please do not reply directly to this email.</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} TRIFECTA. All rights reserved.</p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
