<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    protected $user;
    /**
     * Create a new job instance.
     */
    public function __construct($id_user)
    {
        //
        $this->user = User::find($id_user);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
    }
}
