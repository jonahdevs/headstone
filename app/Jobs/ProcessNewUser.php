<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ProcessNewUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // send a notification to all system operators
        $operators = User::operators()->get();
        Notification::send($operators, new NewUser($this->user));

        // send a welcome email to the new user
        Mail::to($this->user->email)->send(new \App\Mail\NewUser($this->user));
    }
}
