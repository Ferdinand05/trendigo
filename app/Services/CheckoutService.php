<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingAddress;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;

class CheckoutService
{

    public function processCheckout($request)
    {
        return  DB::transaction(function () use ($request) {
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


            // process order item
            $item_details = [];
            foreach ($request->items as $item) {

                $product = Product::lockForUpdate()
                    ->find($item['product_id']);

                // jika produk tidak ada
                if (!$product) {
                    throw new Exception("Product tidak ditemukan");
                }

                // cek stok produk, jika tidak cukup 
                if ($product->stock < $item['quantity']) {
                    throw new Exception("Stok produk {$product->name} tidak mencukupi", 333);
                }


                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);

                // kurangi stock
                $product->decrement('stock', $item['quantity']);


                $item_details[] = [
                    'id' => uniqid('item-'),
                    'price' => $product->price,
                    'product_id' => $item['product_id'],
                    "quantity" => $item["quantity"],
                    'name' => $product->name
                ];
            }

            // Data order (bisa dari database)
            $transaction_details = array(
                'order_id' => $order->order_code,
                'gross_amount' => $request->total + $request->shipping_cost
            );

            // Tambahkan shipping cost ke item detail
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



            return [
                'snapToken' => $snapToken,
                'clientKey' => config('midtrans.client_key'),
            ];
        });
    }
}
