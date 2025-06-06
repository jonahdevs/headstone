<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Form Message</title>
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
                            <h3 style="font-size: 1.25rem; color: #222; text-transform: capitalize;">Ref:
                                {{ $data['subject'] }}</h3>
                            <p style="color: #555; line-height: 1.6;">
                                {{ $data['message'] }}
                            </p>

                            <p style="font-size: 14px; margin-top: 30px;">Warm regards,<br>{{ $data['name'] }} <br>
                                {{ $data['email'] }} <br></p>
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
