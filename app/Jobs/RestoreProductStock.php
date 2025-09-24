<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RestoreProductStock implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->order->order_items as $item) {
            $product = Product::where('id', $item->product_id)
                // preventing race condition
                ->lockForUpdate()
                ->first();

            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }
    }
}
