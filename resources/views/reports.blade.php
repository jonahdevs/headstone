<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reports</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fff; color: #333;">

    <header style="border-bottom: 1px solid #ccc; padding: 20px; background: linear-gradient(to bottom, white, #ccc)">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="50" height="50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 112.5">
                    <!-- (Your SVG content here remains unchanged) -->
                    <path fill="brown" d="..." />
                    <!-- additional paths and groups -->
                </svg>
                <p style="margin: 0; font-weight: bold; font-size: 18px;">{{ $payload->name }}</p>
            </div>
            <div style="text-align: right; font-size: 14px;">
                <p style="margin: 4px 0;">Date Generated: {{ $payload->date }}</p>
                <p style="margin: 4px 0;">Address: {{ $payload->address }}</p>
                <p style="margin: 4px 0;">Phone: {{ $payload->phone }}</p>
                <p style="margin: 4px 0;">Email: {{ $payload->email }}</p>
            </div>
        </div>
    </header>

    <section style="padding: 20px;">
        @if ($payload->resource === 'customers' || $payload->resource === 'users')
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f3f3f3;">
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">Name</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">Email</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">Phone</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">Status</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">Joined On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payload->data as $customer)
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $customer->name }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $customer->email }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $customer->phone }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $customer->status }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $customer->joined_on }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif ($payload->resource === 'orders')
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f3f3f3;">
                        <th style="border: 1px solid #ccc; padding: 10px;">Order ID</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Customer</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">FullFillment Status</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Payment Status</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Items</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Total</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payload->data as $order)
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 10px;">#{{ $order->id }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $order->customer }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $order->status }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $order->payment_status }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $order->items }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $order->total }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $order->date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif ($payload->resource === 'memorials')
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f3f3f3;">
                        <th style="border: 1px solid #ccc; padding: 10px;">ID</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Title</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Price</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Sale Price</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payload->data as $memorial)
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $memorial->id }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $memorial->title }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $memorial->price }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $memorial->sale_price ?? '--' }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $memorial->sales }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif ($payload->resource === 'transactions')
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f3f3f3;">
                        <th style="border: 1px solid #ccc; padding: 10px;">ID</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Transaction ID</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Order ID</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Customer</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Status</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Amount</th>
                        <th style="border: 1px solid #ccc; padding: 10px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payload->data as $transaction)
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->id }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->transaction_id }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->order_id }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->customer }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->status }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->amount }}</td>
                            <td style="border: 1px solid #ccc; padding: 10px;">{{ $transaction->date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

</body>

</html>
