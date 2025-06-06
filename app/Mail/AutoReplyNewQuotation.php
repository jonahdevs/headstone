<?php

namespace App\Mail;

use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Number;

class AutoReplyNewQuotation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Quotation $quotation)
    {
        $quotation->load('customer', 'memorial', 'material');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Thank You for Your Quotation Request – We're Reviewing It!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $quotation = $this->quotation;
        return new Content(
            view: 'mails.quotation-reply',
            with: [
                'customer' => $quotation->customer->name ?? $quotation->name,
                'memorial' => $quotation->memorial->title ?? '',
                'material' => $quotation->material->name ?? '',
                'dimensions' => $quotation->dimensions,
                'inscription' => $quotation->inscription,
                'budget' => Number::currency($quotation->budget, 'kes'),
                'deadline' => Carbon::parse($quotation->deadline)->toFormattedDateString(),
                'instructions' => $quotation->note,
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
