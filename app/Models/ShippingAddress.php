<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table = 'shipping_addresses';
    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'address',
        'province',
        'province_id',
        'city_id',
        'city',
        'district',
        'district_id',
        'sub_district_id',
        'sub_district',
        'postal_code',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
