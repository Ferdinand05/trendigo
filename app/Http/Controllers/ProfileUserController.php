<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $order = Order::where('user_id', Auth::id())
            ->whereNot('order_status', 'cancel')
            ->latest()->get();
        return Inertia::render('profile/UserOrder', [
            'orders' => OrderResource::collection($order),
            'clientKey' => config('midtrans.client_key')
        ]);
    }

    public function showSetting()
    {
        return Inertia::render('profile/UserSetting');
    }

    public function formPassword()
    {
        return Inertia::render('profile/UserPassword');
    }

    public function printOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order) {
            $pdf = Pdf::loadView('pdf.userOrder', ['order' => $order]);

            return $pdf->download('order.pdf');
        }


        return redirect()->back()->with('message', 'Gagal mencetak Order');
    }
}
