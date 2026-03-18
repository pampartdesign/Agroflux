<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class SellDashboardController extends Controller
{
    public function index()
    {
        // ── KPI totals ────────────────────────────────────────────────────────
        $totalListings    = Listing::count();
        $activeListings   = Listing::where('is_active', true)->count();
        $totalOrders      = Order::count();
        $totalRevenue     = (float) Order::sum('total');

        $thisMonthOrders  = Order::whereYear('created_at', now()->year)
                                  ->whereMonth('created_at', now()->month)
                                  ->count();
        $thisMonthRevenue = (float) Order::whereYear('created_at', now()->year)
                                          ->whereMonth('created_at', now()->month)
                                          ->sum('total');

        $avgOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0;

        // ── Recent orders (last 5) ────────────────────────────────────────────
        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        // ── Top 5 listings by revenue ─────────────────────────────────────────
        $topListings = OrderItem::query()
            ->select(
                'listing_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(price * qty) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->with('listing.product')
            ->groupBy('listing_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // ── Monthly chart data (last 6 months) ───────────────────────────────
        $chartMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $month         = now()->subMonths($i);
            $chartMonths[] = [
                'label'   => $month->format('M Y'),
                'revenue' => (float) Order::whereYear('created_at', $month->year)
                                          ->whereMonth('created_at', $month->month)
                                          ->sum('total'),
                'orders'  => (int) Order::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count(),
            ];
        }

        return view('core.sell.dashboard', compact(
            'totalListings',
            'activeListings',
            'totalOrders',
            'totalRevenue',
            'thisMonthOrders',
            'thisMonthRevenue',
            'avgOrderValue',
            'recentOrders',
            'topListings',
            'chartMonths'
        ));
    }
}
