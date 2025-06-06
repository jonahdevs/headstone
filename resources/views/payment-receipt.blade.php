<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    @vite(['resources/css/app.css'])
</head>

<body>
    <section class="container mx-auto max-w-5xl py-10  space-y-6">
        <div class="flex flex-col items-center">
            <h1 class="text-2xl font-bold">Order #{{ $payload->order->id }}</h1>


            <p class="test-sm text-slate-600">Date: {{ $payload->order->date }}</p>
            <p class="test-sm text-slate-600">Items: {{ $payload->order->items }}</p>
        </div>

        <div class="grid grid-cols-2 gap-8 border-t pt-6 text-sm">
            {{-- shipping &customer --}}
            <div>
                <h2 class="font-semibold text-slate-700 mb-1">Customer Info</h2>

                <p class="text-sm text-slate-600">{{ $payload->order->customer->name }}</p>
                <p class="text-sm text-slate-600">{{ $payload->order->customer->email }}</p>
                <p class="text-sm text-slate-600">{{ $payload->order->customer->phone }}</p>
                <p class="text-sm text-slate-600">{{ $payload->order->customer->address }}</p>
            </div>

            <div>
                <h3 class="mb-1 font-semibold text-slate-700">Payment Info</h3>
                <div class="text-sm text-slate-600">
                    <p>Transaction Id: {{ $payload->order->payment->transaction_id }} </p>
                    <p>Amount Paid: {{ $payload->order->payment->amount }}</p>
                    <p>Payment Method: {{ $payload->order->payment->payment_method }}</p>
                    <p>Status: {{ $payload->order->payment->status }} </p>
                </div>
            </div>

        </div>
        {{-- memorials --}}
        <div class="space-y-4 border-t pt-6">
            <h3 class="mb-4 font-semibold text-slate-700">Memorials</h3>

            <table class="w-full border-collapse">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="p-2 border-b">Memorial</th>
                        <th class="p-2 border-b">Qty</th>
                        <th class="p-2 border-b">Unit Price</th>
                        <th class="p-2 border-b">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payload->order->memorials as $memorial)
                        <tr class="border-b">
                            <td class="p-2">{{ $memorial->title }}</td>
                            <td class="p-2">{{ $memorial->quantity }}</td>
                            <td class="p-2">{{ $memorial->price }}</td>
                            <td class="p-2">{{ $memorial->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- summary -->
        <div class="space-y-1 border-t pt-4 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-600">Subtotal</span>
                <span>{{ $payload->order->subtotal }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-600">Discount</span>
                <span>{{ $payload->order->discount }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-600">Delivery fee</span>
                <span>{{ $payload->order->delivery_fee }}</span>
            </div>

            <div class="flex justify-between font-semibold">
                <span>Total</span>
                <span>{{ $payload->order->total }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t pt-4 text-center text-sm text-slate-600">
            <p class="text-sm italic">Thank you for choosing <strong>Everstone</strong>. We're honored to serve your
                family during this meaningful time.</p>
            <p class="mt-2">📧 support@everstone.co.ke | 📞 +254 712 000 000</p>
        </div>
    </section>

</body>

</html>
