<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function index()
    {
        return Inertia::render('checkout/Checkout');
    }

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }


    public function createCharge(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'province' => 'required|string',
            'province_id' => 'required|numeric',
            'city' => 'required|string',
            'city_id' => 'required|numeric',
            'district' => 'required|string',
            'district_id' => 'required|numeric',
            'sub_district' => 'required|string',
            'sub_district_id' => 'required|numeric',
            'postal_code' => 'required|string',
            'shipping_cost' => 'required|numeric',
            'shipping_service_name' => 'required|string',
            'shipping_etd' => 'required|string',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.notes' => 'nullable|string',
        ]);



        // 1. Simpan shipping address
        $shippingAddress = ShippingAddress::updateOrCreate(
            [
                'user_id' => auth()->id(), // patokannya user
            ],
            [
                'recipient_name' => $request->name,
                'phone' => $request->phone,
                'province' => $request->province,
                'province_id' => $request->province_id,
                'city' => $request->city,
                'city_id' => $request->city_id,
                'district' => $request->district,
                'district_id' => $request->district_id,
                'sub_district' => $request->sub_district,
                'sub_district_id' => $request->sub_district_id,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
            ]
        );



        // 2. Buat order dengan status pending
        $order = Order::create([
            'order_code' => uniqid('TDG-'),
            'user_id' => Auth::id(),
            'shipping_address_id' => $shippingAddress->id,
            'subtotal' => $request->total,
            'shipping_cost' => $request->shipping_cost,
            'shipping_service' => $request->shipping_service,
            'shipping_name' => $request->shipping_service_name,
            'total' => $request->total + $request->shipping_cost,
            'status' => 'pending',
            'shipping_etd' => $request->shipping_etd
        ]);


        // Broadcast order baru
        // broadcast(new OrderCreated($order));

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        // Data order (bisa dari database)
        $transaction_details = array(
            'order_id' => $order->order_code,
            'gross_amount' => $request->total + $request->shipping_cost
        );



        $item_details = [];
        foreach ($request->items as $item) {
            $product = Product::find($item["product_id"]);
            $item_details[] = [
                'id' => uniqid('item-'),
                'price' => $product->price,
                'product_id' => $item['product_id'],
                "quantity" => $item["quantity"],
                'name' => $product->name
            ];
        }




        // Tambahkan shipping cost
        $item_details[] = [
            'id' => uniqid('shipping-'),
            'price' => $request->shipping_cost,
            'quantity' => 1,
            'name' => $request->shipping_service_name
        ];


        // Optional
        $shipping_address = array(
            'first_name'    => $request->name,
            'address'       => $request->address,
            'city'          => $request->city,
            'postal_code'   => $request->postal_code,
            'phone'         => $request->phone,
            'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'shipping_address' => $shipping_address
        );

        // Fill SNAP API parameter
        $params = array(
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );



        // Generate Snap Token
        $snapToken = Snap::getSnapToken($params);

        $order->update([
            'midtrans_snap_token' => $snapToken
        ]);
        return response()->json([
            'snapToken' => $snapToken,
            'clientKey' => config('midtrans.client_key'),
        ]);
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
                        $order->status = 'expired';
                        break;

                    case 'cancel':
                        $order->status = 'canceled';
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
