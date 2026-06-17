<?php
// filepath: c:\laragon\www\perfume-client\app\Http\Controllers\Admin\DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Products;
use App\Models\Order;
use App\Models\Categories;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                // Users
                'total_users' => User::count(),
                'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'users_growth' => $this->calculateUsersGrowth(),

                // Products
                'total_products' => Products::count(),
                'active_products' => Products::where('status', 'active')->count(),
                'low_stock_products' => Products::where('stock', '<=', 10)->count(),

                // Categories
                'total_categories' => Categories::count(),

                // Orders
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'this_month_orders' => Order::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),

                // Revenue
                'total_revenue' => Order::where('status', 'completed')->orWhere('payment_status', 'paid')->sum('total') ?? 0,
                'today_revenue' => Order::where('status', 'completed')
                    ->whereDate('created_at', today())->sum('total') ?? 0,
                'this_month_revenue' => Order::where('status', 'completed')->orWhere('payment_status', 'paid')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total') ?? 0,
                'avg_order_value' => Order::where('status', 'completed')->orWhere('payment_status', 'paid')->avg('total') ?? 0,
                'revenue_growth' => $this->calculateRevenueGrowth(),

                // Payment Methods
                'cod_orders' => Order::where('payment_method', 'COD')->count(),
                'online_orders' => Order::where('payment_method', 'ONLINE')->count(),
            ];

            // Real Recent Orders (last 10) - Fix mapping
            $recent_orders = Order::with('user')
                ->latest()
                ->take(10)
                ->get();

            // Real Top Selling Products - Fix query
            $top_products = Products::withCount(['orderItems as order_items_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'completed');
                });
            }])->get();

            // Real Monthly Sales Data (last 12 months)
            $monthly_sales = collect();
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $total = Order::where('status', 'completed')->orWhere('payment_status', 'paid')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total') ?? 0;

                $monthly_sales->push((object)[
                    'month' => $date->month,
                    'total' => (float)$total,
                    'month_name' => $date->format('M Y'),
                    'year' => $date->year
                ]);
            }

            // Category Performance - Fix query
            $category_stats = Categories::select([
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT products.id) as product_count'),
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ])
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->leftJoin('orders', function ($join) {
                    $join->on('order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', '=', 'completed');
                })
                ->where('categories.status', 'active')
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get();

            // dd($stats, $recent_orders, $top_products, $monthly_sales, $category_stats);

            return view('admin.dashboard', compact(
                'stats',
                'recent_orders',
                'top_products',
                'monthly_sales',
                'category_stats'
            ));
        } catch (\Exception $e) {
            // Log detailed error
            \Log::error('Dashboard Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return fallback data with error info in debug mode
            $errorData = [
                'stats' => $this->getFallbackStats(),
                'recent_orders' => collect(),
                'top_products' => collect(),
                'monthly_sales' => $this->getFallbackMonthlySales(),
                'category_stats' => collect()
            ];

            if (config('app.debug')) {
                $errorData['debug_error'] = $e->getMessage();
            }

            return view('admin.dashboard', $errorData);
        }
    }

    private function calculateUsersGrowth()
    {
        try {
            $thisMonth = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();

            if ($lastMonth == 0) {
                return $thisMonth > 0 ? 100 : 0;
            }

            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateRevenueGrowth()
    {
        try {
            $thisMonth = Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total') ?? 0;

            $lastMonth = Order::where('status', 'completed')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('total') ?? 0;

            if ($lastMonth == 0) {
                return $thisMonth > 0 ? 100 : 0;
            }

            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getFallbackStats()
    {
        return [
            'total_users' => 0,
            'new_users_this_month' => 0,
            'users_growth' => 0,
            'total_products' => 0,
            'active_products' => 0,
            'low_stock_products' => 0,
            'total_categories' => 0,
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'processing_orders' => 0,
            'cancelled_orders' => 0,
            'today_orders' => 0,
            'this_month_orders' => 0,
            'total_revenue' => 0,
            'today_revenue' => 0,
            'this_month_revenue' => 0,
            'avg_order_value' => 0,
            'revenue_growth' => 0,
            'cod_orders' => 0,
            'online_orders' => 0,
        ];
    }

    private function getFallbackMonthlySales()
    {
        $sales = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $sales->push((object)[
                'month' => $date->month,
                'total' => 0,
                'month_name' => $date->format('M Y'),
                'year' => $date->year
            ]);
        }
        return $sales;
    }

    // API endpoint for AJAX
    public function getStats()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => User::count(),
                    'total_products' => Products::count(),
                    'total_orders' => Order::count(),
                    'total_revenue' => Order::where('status', 'completed')->sum('total'),
                    'pending_orders' => Order::where('status', 'pending')->count(),
                    'today_orders' => Order::whereDate('created_at', today())->count(),
                    'today_revenue' => Order::where('status', 'completed')
                        ->whereDate('created_at', today())->sum('total'),
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stats',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
