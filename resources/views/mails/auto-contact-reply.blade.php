<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Auto Reply Contact Form Message</title>
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
                        <td style="padding: 40px 20px;">
                            <h3 style="font-size: 1.25rem; color: #222;">Dear {{ $name }},</h3>
                            <p style="color: #555; line-height: 1.6;">
                                We’ve received your message and want to thank you for contacting Everstone. Our team is
                                currently reviewing your inquiry and will get back to you shortly.
                            </p>
                            <p style="color: #555; line-height: 1.6;">
                                In the meantime, feel free to explore our memorial collections or learn more about our
                                process through our website.
                            </p>

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
