<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderEmailJob;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MailController extends Controller
{
    public function index()
    {
        for ($i = 0; $i < 3; $i++) {

            $order = Order::findOrFail(rand(50, 101));

            SendOrderEmailJob::dispatch($order)->onQueue("email");
        }

        Log::info('Dispatched order ' . $order->id);
        return 'Dispatched order ' . $order->id;
    }
}
