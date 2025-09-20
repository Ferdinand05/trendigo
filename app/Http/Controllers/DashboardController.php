<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now()->timezone('Asia/Jakarta');




        // total bulan ini
        $thisMonthRevenue = Order::whereYear('created_at', $now->year)
            ->where('status', 'paid')
            ->whereMonth('created_at', $now->month)
            ->sum('total');


        // total bulan lalu
        $lastMonthRevenue = Order::whereYear('created_at', $now->year)
            ->where('status', 'paid')
            ->whereMonth('created_at', $now->subMonth()->month)
            ->sum('total');


        // hitung persen perubahan
        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        $chartData = DB::table('orders')
            ->selectRaw('
        MONTH(created_at) as month,
        COUNT(id) as total_orders,
        SUM(total) as total_revenue
        ')
            ->whereYear('created_at', Carbon::now()->year) // filter tahun berjalan
            ->groupBy('month')
            ->orderBy('month')
            ->get();


        $productOutOfStock = Product::whereNot('is_active', 0)
            ->where('stock', '<=', 5)
            ->limit(3)
            ->orderBy('stock', 'asc')
            ->get()
            ->map(function ($product) {
                $product->name = Str::limit($product->name, 37);
                return $product;
            });

        $orderProcess = Order::where('status', 'paid')
            ->where('order_status', 'process')
            ->count();

        $todayOrder = Order::whereDate('created_at', today('Asia/Jakarta'))->get();

        return Inertia::render('Dashboard', [
            'totalRevenue' => Order::where('status', 'paid')->sum('total'),
            'percentage' => $percentageChange,
            'chartData' => $chartData,
            'orderProcess' => $orderProcess,
            'productOutOfStock' => $productOutOfStock,
            'todayOrder' => $todayOrder
        ]);
    }
}
