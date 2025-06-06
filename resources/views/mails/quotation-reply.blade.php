<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quotation Request Received</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 30px;">
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
                            <h3 style="font-size: 1.25rem; color: #222;">Dear {{ $customer }}</h3>
                            <p style="color: #555; line-height: 1.6;">
                            <p style="color: #1f2937">Thank you for reaching out to us with your quotation request.
                                We're currently reviewing
                                your details and will
                                get back to you shortly with a customized offer.</p>
                            </p>

                            <h4 style="color: #111827;">Here’s what we received:</h4>
                            <ul style="line-height: 1.7;">
                                <li style="color: #1f2937;"><strong>Memorial Type:</strong> {{ $memorial }}</li>
                                <li style="color: #1f2937;"><strong>Material:</strong> {{ $material }}</li>
                                <li style="color: #1f2937;"><strong>Dimensions:</strong> {{ $dimensions }}</li>
                                <li style="color: #1f2937;"><strong>Inscription:</strong> {{ $inscription }}</li>
                                <li style="color: #1f2937;"><strong>Preferred Budget:</strong> {{ $budget }}</li>
                                <li style="color: #1f2937;"><strong>Deadline:</strong> {{ $deadline }}</li>
                                <li style="color: #1f2937;"><strong>Instructions:</strong> {{ $instructions }}</li>
                            </ul>

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
