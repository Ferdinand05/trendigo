<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'user_id',
        'shipping_address_id',
        'subtotal',
        'shipping_cost',
        'shipping_service',
        'shipping_etd',
        'total',
        'status',
        'payment_method',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_transaction_status',
        'midtrans_response',
        'fraud_status',
        'shipping_name',
        'order_status',
        'midtrans_snap_token'
    ];


    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
