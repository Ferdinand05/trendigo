<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpiredOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expired-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expire for pending orders over 1 day';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $expireOrder = Order::where('status', 'pending')
            ->whereDate('created_at', '<', now('Asia/Jakarta')->subDay())
            ->update(['status' => 'expire']);

        $this->info("Expired {$expireOrder} pending orders");
        Log::info('Updated pending orders to expire');
    }
}
