<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhookJob;
use App\Mail\NotifyUserMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController;

class StribeWebhookController extends Controller
{
    protected function handle(Request $request)
    {
        // if ($payload['type'] === 'invoice.payment_succeeded') {
        //     // Handle the incoming event...
        //     // dd("success");
        //     Mail::to('admin@gmail.com’')->send(new NotifyUserMail());
        // }

        ProcessWebhookJob::dispatch($request->all());

        return response()->json(['success' => true]);
    }
}
