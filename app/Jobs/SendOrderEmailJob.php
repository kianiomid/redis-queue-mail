<?php

namespace App\Jobs;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        // Allow only 2 emails every 1 second
        Redis::throttle("any_key")->allow(2)->every(1)
            ->then(function () {

                $recipient = 'kianiomid11@gmail.com';
                Mail::to($recipient)->send(new OrderShippedMail($this->order));
                Log::info('Emailed order ' . $this->order->id);

            }, function () {
                // Could not obtain lock; this job will be re-queued
                return $this->release(2);
            });
    }
}
