<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class OrderController extends Controller
{

    public function index(Request $request)
    {



        $orders = Order::query()
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('order_status', 'like', "%{$keyword}%")
                    ->orWhere('order_code', 'like', "%{$keyword}%")
                    ->orWhere('midtrans_payment_type', 'like', "%{$keyword}%")
                    ->orWhere('shipping_name', 'like', "%{$keyword}%")
                    ->orWhere('total', 'like', "%{$keyword}%")
                    ->orWhere('shipping_etd', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "{$keyword}%");
                    });
            })
            ->when($request->col, function ($query, $column) {
                $query->orderBy($column, 'asc');
            })
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('orders/Index', [
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function destroy(Request $request, $id)
    {

        $order = Order::find($id);

        if ($order) {
            $order->delete();

            return redirect()->back()->with('message', 'Order has been deleted');
        }

        return redirect()->back();
    }

    public function orderDone(Request $request)
    {

        $order = Order::findOrFail($request->orderId);

        if ($order->status == 'paid') {
            $order->update([
                'order_status' => 'done'
            ]);

            return redirect()->back()->with('message', 'Order Done!');
        } else {
            return redirect()->back()->with('message', 'Order must be paid!');
        }
    }
    public function orderCancel(Request $request)
    {

        $order = Order::findOrFail($request->orderId);

        if ($order->status == 'expire' || $order->status == 'pending') {
            $order->update([
                'order_status' => 'cancel'
            ]);

            return redirect()->back()->with('message', 'Order Cancel!');
        } else {
            return redirect()->back()->with('message', 'You cant do this!');
        }
    }


    public function printOrdersPdf(Request $request)
    {

        $orders = Order::query()
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->get();
        $pdf = Pdf::loadView('pdf.orders', ['orders' => $orders]);
        return $pdf->stream('orders-report.pdf');
    }


    public function printDetailOrder($id)
    {
        $order = Order::find($id);
        if ($order) {
            $pdf = Pdf::loadView('pdf.detailOrder', ['order' => $order]);
            return $pdf->stream('detail-order.pdf');
        }

        return redirect()->back();
    }

    public function printShippingOrder($id)
    {
        $order = Order::find($id);
        if ($order) {
            $pdf = Pdf::loadView('pdf.detailShipping', ['order' => $order]);
            $pdf->setPaper('A6', 'portrait');

            return $pdf->stream('shipping-order.pdf');
        }

        return redirect()->back();
    }
}
