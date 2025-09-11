<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{

    public function index()
    {

        $order = Order::latest()->get();

        return Inertia::render('orders/Index', [
            'orders' => OrderResource::collection($order)
        ]);
    }
}
