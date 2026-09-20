<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin the Wheel Winner</title>
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

                            <!-- Logo -->
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"
                                        style="background-color: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 12px;">
                                        <img src="{{ asset('images/truelife-logo.png') }}" alt="Logo" width="64"
                                            height="64"
                                            style="display: block; width: 64px; height: 64px; object-fit: contain;">
                                    </td>
                                </tr>
                            </table>

                            <h1
                                style="margin: 20px 0 0 0; font-size: 26px; font-weight: 900; letter-spacing: -0.025em; color: #ffffff;">
                                🎉 You Won!</h1>

                            <p style="margin: 8px 0 0 0; font-size: 13px; color: #cbd5e1;">
                                Spin the Wheel · Pangkabuhayan Raffle
                            </p>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 12px 0; color: #334155;">
                                Hi {{ $user->firstname ?? $user->username ?? 'Member' }},
                            </p>

                            <p style="margin: 0 0 24px 0; color: #475569;">
                                Congratulations! Your name was drawn as a
                                <strong style="color: #0B2A5B;">winner</strong> in the
                                <strong style="color: #0B2A5B;">{{ $draw->draw_date->format('F j, Y') }}</strong>
                                Spin the Wheel draw of our Pangkabuhayan raffle.
                            </p>

                            <!-- Prize Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #3b82f6; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p
                                            style="margin: 0 0 4px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e40af; letter-spacing: 0.05em;">
                                            Your Prize</p>

                                        <p style="margin: 0 0 4px 0; font-size: 20px; font-weight: 700; color: #1e3a8a;">
                                            {{ $winner->prize_name }}
                                        </p>

                                        <p style="margin: 0 0 8px 0; font-size: 15px; font-weight: 700; color: #1e40af;">
                                            ₱{{ number_format((float) $winner->prize_amount, 2) }}
                                        </p>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                            style="font-size: 13px; color: #3b82f6;">
                                            <tr>
                                                <td style="padding: 2px 0;">
                                                    Draw: <strong>{{ $draw->draw_date->format('F j, Y') }}</strong>
                                                    (covering {{ $draw->period_start->format('M j') }} – {{ $draw->period_end->format('M j, Y') }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 2px 0;">
                                                    Spin #{{ $winner->spin_order }} of {{ $draw->winners_target }}
                                                    prize slot(s)
                                                    @if ($winNumber > 1)
                                                        · <strong>this is your {{ $winNumber }}th prize in this draw</strong>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 2px 0;">
                                                    You had {{ $winner->qualified_invites }}
                                                    qualifying activation(s) during this cycle.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- How to Claim -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #D4A017; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p
                                            style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #92400e; letter-spacing: 0.05em;">
                                            How to Claim Your Prize</p>

                                        <p style="margin: 0 0 12px 0; font-size: 14px; color: #78350f;">
                                            {!! nl2br(e($claimInstructions)) !!}
                                        </p>

                                        @if (! empty($company['name']) || ! empty($company['contact']) || ! empty($company['email']))
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                                style="border-top: 1px solid #fde68a; font-size: 13px; color: #92400e;">
                                                <tr>
                                                    <td style="padding: 10px 0 0 0; line-height: 1.7;">
                                                        @if (! empty($company['name']))
                                                            <strong>{{ $company['name'] }}</strong><br>
                                                        @endif
                                                        @if (! empty($company['contact']))
                                                            📞 {{ $company['contact'] }}<br>
                                                        @endif
                                                        @if (! empty($company['email']))
                                                            ✉️ {{ $company['email'] }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Sign in CTA -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding: 24px;">
                                        <p style="margin: 0 0 16px 0; font-size: 14px; color: #475569;">
                                            The full list of winners is also posted on the dashboard news feed.
                                        </p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" align="center"
                                            style="margin: 0 auto;">
                                            <tr>
                                                <td align="center" style="border-radius: 12px; background-color: #0B2A5B;">
                                                    <a href="{{ route('secret.login') }}" target="_blank"
                                                        style="display: inline-block; padding: 14px 34px; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 12px;">
                                                        Sign In to Your Account
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #475569;">
                                Thank you for helping our community grow — every member you invite adds to the
                                Pangkabuhayan fund that makes these draws possible.
                            </p>

                            <p style="margin: 24px 0 0 0; color: #475569;">
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
