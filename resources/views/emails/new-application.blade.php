<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Application Details' }}</title>
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
                                {{ $title ?? 'Application Summary' }}</h1>
                        </td>
                    </tr>

                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 40px; font-size: 15px; line-height: 1.6;">
                            <p style="margin: 0 0 24px 0; color: #475569;">Here are the complete details submitted for
                                your application:</p>

                            <!-- Credentials Box (Conditional) -->
                            @if (isset($includeCredentials) && $includeCredentials && isset($password))
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                    style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #3b82f6; border-radius: 16px; margin-bottom: 24px;">
                                    <tr>
                                        <td style="padding: 20px;">
                                            <p
                                                style="margin: 0 0 4px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e40af; letter-spacing: 0.05em;">
                                                Your Account Password</p>
                                            <p
                                                style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #1e3a8a;">
                                                {{ $password }}</p>
                                            <p style="margin: 0; font-size: 13px; color: #3b82f6;">Please keep this
                                                information secure.</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <!-- Personal Information Card -->
                            <h3
                                style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">
                                Personal Information</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td
                                                    style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Full Name</td>
                                                <td align="right"
                                                    style="padding-bottom: 12px; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $first_name }} {{ $middle_name }} {{ $surname }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Gender</td>
                                                <td align="right"
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $gender }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Civil Status</td>
                                                <td align="right"
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $civil_status }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Birthdate</td>
                                                <td align="right"
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $birthdate }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Address</td>
                                                <td align="right"
                                                    style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $address }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Contact & Security Card -->
                            <h3
                                style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">
                                Contact & Security</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td
                                                    style="padding-bottom: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Mobile Number</td>
                                                <td align="right"
                                                    style="padding-bottom: 12px; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $mobile }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Email Address</td>
                                                <td align="right"
                                                    style="padding: 12px 0; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $email ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                                                    Username</td>
                                                <td align="right"
                                                    style="padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    {{ $username }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Beneficiaries Card -->
                            <h3
                                style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">
                                Beneficiaries</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            @forelse($beneficiaries as $beneficiary)
                                                @if (!empty($beneficiary['fullname']))
                                                    <tr>
                                                        <td
                                                            style="padding: 8px 0; {{ !$loop->first ? 'border-top: 1px solid #e2e8f0;' : '' }} font-size: 14px; color: #1e293b;">
                                                            <strong>{{ $beneficiary['fullname'] }}</strong>
                                                            <span
                                                                style="color: #64748b; display: block; font-size: 12px; margin-top: 2px;">
                                                                Relationship:
                                                                {{ $beneficiary['relationship'] ?? 'Relationship not specified' }}
                                                                &bull; Born: {{ $beneficiary['birthdate'] ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">No
                                                        beneficiaries listed.</td>
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
