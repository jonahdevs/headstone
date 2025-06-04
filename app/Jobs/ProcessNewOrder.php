<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Number;
use Spatie\Browsershot\Browsershot;

class ProcessNewOrder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Order $order)
    {
        $order->load('customer', 'memorials', 'payment')->loadCount('memorials');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;

        // send a notification to system operators
        $operators = User::operators()->get();
        Notification::send($operators, new NewOrder($order));

        if (!$order->payment || $order->payment->status != 'success') {
            return;
        }

        $formattedOrder = (object) [
            'id' => $order->id,
            'items' => $order->memorials_count,
            'subtotal' => Number::currency($order->subtotal ?? 0, 'kes'),
            'discount' => Number::currency($order->discount ?? 0, 'kes'),
            'delivery_fee' => Number::currency(0, 'kes'),
            'total' => Number::currency($order->total ?? 0, 'kes'),
            'date' => $order->created_at->format('Y/m/d H:i:s'),
            'memorials' => $order->memorials->map(fn($memorial) => (object) [
                'id' => $memorial->id,
                'title' => $memorial->title,
                'quantity' => $memorial->pivot->quantity,
                'price' => Number::currency($memorial->pivot->price ?? 0, 'kes'),
                'total' => Number::currency($memorial->pivot->total ?? 0, 'kes'),
            ]),
            'customer' => (object) [
                'name' => $order->customer->name,
                'email' => $order->customer->email,
                'address' => $order->customer->address,
                'phone' => $order->customer->phone,
            ],
            'payment' => (object) [
                'transaction_id' => $order->payment->transaction_id,
                'amount' => Number::currency($order->payment->amount ?? 0, 'kes'),
                'payment_method' => $order->payment->payment_method,
                'status' => $order->payment->status,
            ]
        ];

        $payload = (object) [
            'name' => 'Everstone',
            'address' => 'Thika Town, Kiambu County, Kenya',
            'phone' => '+254 700 000 000',
            'email' => 'contact@everstone.co.ke',
            'date' => now()->format('d M Y'),
            'order' => $formattedOrder,
        ];

        $html = view('payment-receipt', ['payload' => $payload])->render();

        $filename = 'receipt-' . $order->id . '-' . now()->format('YmdHis') . '.pdf';
        Browsershot::html($html)
            ->showBackground()
            ->margins(8, 8, 8, 8)
            ->save(storage_path("/app/private/receipts/{$filename}"));

        $order->payment()->update([
            'payment_receipt' => "receipts/{$filename}"
        ]);

        Mail::to($order->customer->email)->send(new \App\Mail\NewOrder($order));
    }
}
