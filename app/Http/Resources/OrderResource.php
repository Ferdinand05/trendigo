<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'user_id' => $this->user_id,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'shipping_address_id' => $this->shipping_address_id,
            'shipping_address' => [
                'recipient_name' => $this->shippingAddress->recipient_name,
                'phone' => $this->shippingAddress->phone,
                'address' => $this->shippingAddress->address,
                'province' => $this->shippingAddress->province,
                'city' => $this->shippingAddress->city,
                'district' => $this->shippingAddress->district,
                'sub_district' => $this->shippingAddress->sub_district,
                'postal_code' => $this->shippingAddress->postal_code
            ],
            'shipping_cost' => $this->shipping_cost,
            'shipping_service' => $this->shipping_service,
            'shipping_etd' => $this->shipping_etd,
            'shipping_name' => $this->shipping_name,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'status' => $this->status,
            'midtrans_transaction_id' => $this->midtrans_transaction_id,
            'midtrans_payment_type' => $this->midtrans_payment_type,
            'midtrans_transaction_status' => $this->midtrans_transaction_status,
            'midtrans_response' => $this->midtrans_response,
            'fraud_status' => $this->fraud_status,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'order_status' => $this->order_status,
            'order_items' => $this->order_items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                    'notes' => $item->notes
                ];
            })
        ];
    }
}
