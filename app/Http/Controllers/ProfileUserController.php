<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileUserController extends Controller
{
    public function index()
    {
        return Inertia::render('profile/Index');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email') || $request->user()->isDirty('name')) {
            $request->user()->save();
        }

        return to_route('profile.user.index');
    }


    public function showOrder()
    {
        $order = Order::where('user_id', Auth::id())->get();
        return Inertia::render('profile/UserOrder', [
            'orders' => OrderResource::collection($order)
        ]);
    }

    public function showSetting()
    {
        return Inertia::render('profile/UserSetting');
    }
}
