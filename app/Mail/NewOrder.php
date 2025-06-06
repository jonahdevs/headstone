<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Number;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Order $order)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order Confirmation - {$this->order->id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.new-order',
            with: [
                'customer' => $this->order->customer->name,
                'memorials' => $this->order->memorials,
                'payment_method' => $this->order->payment->payment_method,
                'order' => (object) [
                    'id' => $this->order->id,
                    'total' => "KES {$this->order->total}",
                    'payment_method' => $this->order->payment->payment_method,
                    'memorials' => $this->order->memorials->map(
                        fn($memorial) => (object) [
                            'id' => $memorial->id,
                            'title' => $memorial->title,
                            'estimated_delivery' => $memorial->estimated_delivery_time,
                            'quantity' => $memorial->pivot->quantity,
                            'total' => Number::currency($this->order->total, 'kes'),
                            'materials' => $memorial->materials
                                ? $memorial->materials->pluck('name')->toArray()
                                : []
                        ],
                    ),
                ]
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
