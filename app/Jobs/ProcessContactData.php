<?php

namespace App\Jobs;

use App\Mail\AutoReplyContact;
use App\Mail\ContactFormMail;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ProcessContactData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // send a notification to all admins
        $operators = User::operators()->get();
        Notification::send($operators, new NewMessage($this->data));

        // send an autoemail to the user
        Mail::to($this->data['email'])->send(new AutoReplyContact($this->data));

        //  send an email to the official email
        Mail::to('info@everstone.co.ke')->send(new ContactFormMail($this->data));
    }
}
