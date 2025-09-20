<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class HandleNotificationController extends Controller
{


    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }



    public function notification(Request $request)
    {
        try {
            $notif = new Notification();

            $orderId = $notif->order_id;
            $transactionStatus = $notif->transaction_status;
            $paymentType = $notif->payment_type;
            $transactionId = $notif->transaction_id;



            $order = Order::where('order_code', $orderId)->first();

            // apakah ada order dengan code terkait ?
            if (!$order) {
                Log::warning("Midtrans Notification: Order not found", [
                    'order_id' => $orderId,
                    'request' => $request->all()
                ]);
                return response()->json(['message' => 'Order not found'], 404);
            }


            // cek signatur key dari midtrans
            $mySignature = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . config('midtrans.server_key'));

            if ($mySignature !== $notif->signature_key) {
                return response()->json(['message' => 'Invalid Signature Key'], 403);
            }





            // update transaksi
            DB::transaction(function () use ($order, $notif, $transactionStatus, $paymentType, $transactionId) {
                $order->midtrans_transaction_id = $transactionId;
                $order->midtrans_payment_type = $paymentType;
                $order->midtrans_transaction_status = $transactionStatus;
                $order->fraud_status = $notif->fraud_status;
                $order->midtrans_response = json_encode($notif->getResponse());



                // check status
                switch ($transactionStatus) {
                    case 'capture':
                    case 'settlement':
                        $order->status = 'paid';

                        // hapus cart ketika pembayaran berhasil 
                        Cart::where('user_id', $order->user_id)->delete();
                        break;

                    case 'pending':
                        $order->status = 'pending';
                        break;

                    case 'deny':
                        $order->status = 'failed';
                        break;

                    case 'expire':
                        $order->status = 'expire';
                        // TODO Kembalikan stock produk
                        break;

                    case 'cancel':
                        $order->status = 'cancel';
                        break;

                    case 'refund':
                    case 'partial_refund':
                        $order->status = 'refunded';
                        break;
                }

                $order->save();
            });

            return response()->json(['message' => 'Notification handled'], 200);
        } catch (\Exception $e) {
            Log::error("Midtrans Notification Error", [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
