<?php

namespace App\Jobs;

use App\Mail\NotifyUserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(array $payload): void
    {
        Log::emergency("ok done");
        if ($payload['type'] === 'invoice.payment_succeeded') {
            // Handle the incoming event...
            // dd("success");
            Mail::to('admin@gmail.com’')->send(new NotifyUserMail());
        }
    }
}
