<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to Everstone</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'sans', serif; background-color: #f4f4f5; color: #2d2d2d;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                    style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 40px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px; color: #ffffff;">Everstone</h1>
                            <p style="margin: 10px 0 0; font-size: 16px; color: #d1d5db;">Honoring Memories, Preserving
                                Legacies</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="font-size: 18px; margin-bottom: 25px;">Dear {{ $user->name }},</p>
                            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                                Thank you for joining Everstone. We are here to help you commemorate your loved ones
                                with dignity, care, and personalized memorials.
                            </p>
                            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                                You can begin your journey with us by logging into your account and exploring our
                                services.
                            </p>
                            <div style="text-align: center; margin-bottom: 30px;">
                                <a href="https://everstone_03.test/login"
                                    style="background-color: #1f2937; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: bold; display: inline-block;">
                                    Access Your Account
                                </a>
                            </div>
                            <p style="font-size: 14px; color: #6b7280;">
                                Need assistance? We're here for you. Reach out to us at <a
                                    href="mailto:support@everstone.co.ke"
                                    style="color: #1f2937;">support@everstone.co.ke</a>.
                            </p>
                            <p style="font-size: 14px; margin-top: 30px;">Warm regards,<br>The Everstone Team</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f3f4f6; text-align: center; padding: 20px; font-size: 12px; color: #6b7280;">
                            &copy; {{ now()->year }} Everstone. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
