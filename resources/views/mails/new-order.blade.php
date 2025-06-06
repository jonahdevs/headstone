<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        *,
        *::before,
        *::after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
    </style>
</head>

<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:Segoe UI, sans-serif;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;padding:32px;">
                    <!-- Header -->
                    <tr>
                        <td style="text-align:center;padding-bottom:24px;border-bottom:1px solid #e5e7eb;">
                            <h1 style="color:#047857;font-size:20px;margin-bottom:8px;">Order Successfully Placed</h1>
                            <p style="color:#4b5563;font-size:14px;">
                                Thank you, {{ $customer }}. Your memorial order has been received and is being
                                processed
                                with care.
                            </p>
                        </td>
                    </tr>

                    <!-- Order Summary -->
                    <tr>
                        <td style="padding-top:24px;color:#374151;font-size:14px;">
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                            <p><strong>Total Paid:</strong> {{ $order->total }}</p>
                        </td>
                    </tr>

                    <!-- Memorials -->
                    <tr>
                        <td style="padding-top:24px;">
                            <h2 style="font-size:16px;color:#111827;margin-bottom:12px;">Memorials</h2>

                            @foreach ($order->memorials as $memorial)
                                <div style="background:#f9fafb;padding:16px;border-radius:6px;margin-bottom:16px;">
                                    <p style="margin:0;"><strong>{{ $memorial->title }}</strong></p>
                                    <p style="margin:4px 0;color:#6b7280; font-size: 14px;">Material:
                                        {{ implode(', ', $memorial->materials) }}
                                    </p>
                                    <p style="margin:4px 0;color:#6b7280; font-size: 14px;">Quantity:
                                        {{ $memorial->quantity }}
                                    </p>
                                    <p style="margin:4px 0;color:#6b7280; font-size: 14px;">Estimated Delivery:
                                        {{ $memorial->estimated_delivery }}
                                    </p>
                                    <p style="margin:4px 0;"><strong>Total:</strong> {{ $memorial->total }}</p>
                                </div>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Next Steps -->
                    <tr>
                        <td style="padding-top:24px;border-top:1px solid #e5e7eb;">
                            <h2 style="font-size:16px;color:#111827;margin-bottom:8px;">Next Steps</h2>
                            <ul style="color:#6b7280;font-size:14px;padding-left:20px;margin-top:0;">
                                <li>Our team will review each headstone’s customization.</li>
                                <li>You will receive design proofs for approval within 48 hours.</li>
                                <li>We will coordinate delivery with cemetery staff after confirmation.</li>
                            </ul>
                        </td>
                    </tr>

                    <!-- Support -->
                    <tr>
                        <td style="padding-top:24px;">
                            <div
                                style="background:#f1f5f9;padding:16px;border-radius:6px;font-size:14px;color:#374151;">
                                Questions? Email
                                <a href="mailto:support@everstone.co.ke"
                                    style="color:#047857;text-decoration:underline;">
                                    support@everstone.co.ke
                                </a>
                                or call <strong>+254 7000000000</strong>.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
