<?php

namespace App\Jobs;

use App\Mail\AutoReplyNewQuotation;
use App\Models\Quotation;
use App\Models\User;
use App\Notifications\NewQuotation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ProcessNewQuotation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Quotation $quotation)
    {
        $quotation->load('customer', 'memorial', 'material');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $quotation = $this->quotation;
        // send a notification to system admins
        $operators = User::operators()->get();
        Notification::send($operators, new NewQuotation($quotation));

        // send a auto reply email to the owner of the quotattion
        Mail::to($quotation->customer->email)->send(new AutoReplyNewQuotation($quotation));
    }
}
