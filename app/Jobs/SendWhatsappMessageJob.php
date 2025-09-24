<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public Order $order;
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        $phone = $order->shippingAddress->phone;
        $name = $order->shippingAddress->recipient_name;
        $total = number_format($order->total, '0', ',', '.');
        $code = $order->order_code;

        $message = "Halo {$name}, terima kasih sudah melakukan pemesanan.\n\n"
            . "Detail Order:\n"
            . "Kode Pesanan: {$code}\n"
            . "Total: Rp " . $total . "\n"
            . "Pesanan Anda akan segera kami proses. \n"
            . "-Trendigo.";

        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN')
        ])
            ->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

        Log::info("Fonnte Response", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
    }
}
