<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();


        // TODO where status = paid

        // total bulan ini
        $thisMonthRevenue = Order::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total');

        // total bulan lalu
        $lastMonthRevenue = Order::whereYear('created_at', $now->subMonth()->year)
            ->whereMonth('created_at', $now->subMonth()->month)
            ->sum('total');

        // hitung persen perubahan
        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        return Inertia::render('Dashboard', [
            'totalUser' => User::count(),
            'totalProduct' => Product::count(),
            'totalRevenue' => Order::where('status', 'paid')->sum('total'),
            'percentage' => $percentageChange
        ]);
    }
}
