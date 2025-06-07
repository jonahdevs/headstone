<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        section.container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 40px 20px;
            padding-bottom: 80px;
        }

        .heading {
            text-align: center;
            margin-bottom: 32px;
        }

        .heading h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .heading p {
            font-size: 14px;
            color: #475569;
            margin: 2px 0;
        }



        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }

        thead {
            background-color: #f8fafc;
            text-align: left;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .summary {
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            margin-bottom: 32px;
        }

        .summary .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .summary .row:last-child {
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: 1hite;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            text-align: center;
            font-size: 14px;
            color: #475569;
        }

        .footer p {
            margin: 4px 0;
        }

        .footer strong {
            font-weight: bold;
        }

        .footer .italic {
            font-style: italic;
        }
    </style>
</head>

<body>
    <section class="container">
        <div class="heading">
            <h1>Order #{{ $payload->order->id }}</h1>
            <p>Date: {{ $payload->order->date }}</p>
            <p>Items: {{ $payload->order->items }}</p>
        </div>

        <table style="width: 100%; border: 0; border-collapse: collapse; font-size: 14px; margin-bottom: 32px;">
            <tr valign="top">
                <td style="width: 50%; padding-right: 20px; border: none;">
                    <h2 style="font-weight: bold; color: #1e293b; margin-bottom: 4px; font-size: 16px;">Customer Info
                    </h2>
                    <p style="color: #475569; margin: 2px 0;">{{ $payload->order->customer->name }}</p>
                    <p style="color: #475569; margin: 2px 0;">{{ $payload->order->customer->email }}</p>
                    <p style="color: #475569; margin: 2px 0;">{{ $payload->order->customer->phone }}</p>
                    <p style="color: #475569; margin: 2px 0;">{{ $payload->order->customer->address }}</p>
                </td>
                <td style="width: 50%; border: none;">
                    <h2 style="font-weight: bold; color: #1e293b; margin-bottom: 4px; font-size: 16px;">Payment Info
                    </h2>
                    <p style="color: #475569; margin: 2px 0;">Transaction Id:
                        {{ $payload->order->payment->transaction_id }}</p>
                    <p style="color: #475569; margin: 2px 0;">Amount Paid: {{ $payload->order->payment->amount }}</p>
                    <p style="color: #475569; margin: 2px 0;">Payment Method:
                        {{ $payload->order->payment->payment_method }}</p>
                    <p style="color: #475569; margin: 2px 0;">Status: {{ $payload->order->payment->status }}</p>
                </td>
            </tr>
        </table>

        <div class="memorials">
            <h3 style="margin-bottom: 16px; font-weight: 600; color: #1e293b;">Memorials</h3>
            <table>
                <thead>
                    <tr>
                        <th>Memorial</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payload->order->memorials as $memorial)
                        <tr>
                            <td>{{ $memorial->title }}</td>
                            <td>{{ $memorial->quantity }}</td>
                            <td>{{ $memorial->price }}</td>
                            <td>{{ $memorial->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <table style="width: 100%; font-size: 14px; border-collapse: collapse; margin-top: 20px; border: none;">
            <tr style="border: none;">
                <td style="padding: 4px 0; color: #475569; border: none;">Subtotal</td>
                <td style="padding: 4px 0; text-align: right; border: none;">{{ $payload->order->subtotal }}</td>
            </tr>
            <tr style="border: none;">
                <td style="padding: 4px 0; color: #475569; border: none;">Discount</td>
                <td style="padding: 4px 0; text-align: right; border: none;">{{ $payload->order->discount }}</td>
            </tr>
            <tr style="border: none;">
                <td style="padding: 4px 0; color: #475569; border: none;">Delivery Fee</td>
                <td style="padding: 4px 0; text-align: right; border: none;">{{ $payload->order->delivery_fee }}</td>
            </tr>
            <tr style="font-weight: bold; border: none;">
                <td style="padding: 6px 0; border: none;">Total</td>
                <td style="padding: 6px 0; text-align: right; border: none;">{{ $payload->order->total }}</td>
            </tr>
        </table>

        <div class="footer">
            <p class="italic">Thank you for choosing <strong>Everstone</strong>. We're honored to serve your family
                during this meaningful time.</p>
            <p>📧 support@everstone.co.ke | 📞 +254 712 000 000</p>
        </div>
    </section>
</body>

</html>
