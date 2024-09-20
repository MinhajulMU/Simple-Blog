<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;

class JobTrigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:job-trigger {email : The email address to send the email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch Job (currently only SendWelcomeEmailJob)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $email = $this->argument('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            return 1; // Return non-zero to indicate failure
        }

         // Send the email
        try {
            $user = User::where('email', $email)->first();
            if (empty($user)) {
                throw new \Exception('user not found');
            }
            SendWelcomeEmailJob::dispatch($user->id);
            $this->info('Success dispatch job');
        } catch (\Exception $e) {
            $this->error('Failed to dispatch job: ' . $e->getMessage());
            return 1; // Return non-zero to indicate failure
        }
        $this->info('halooo');
        return 0;
    }
}
