<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BottleInventory;
use App\Models\CapInventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'customers' => User::where('is_admin', false)->count(),
            'revenue' => (float) Order::sum('final_price'),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();

        $lowStockBottles = BottleInventory::with('bottle')
            ->whereColumn('available_stock', '<=', 'reorder_level')
            ->take(5)->get();

        $lowStockCaps = CapInventory::with('cap')
            ->whereColumn('available_stock', '<=', 'reorder_level')
            ->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockBottles', 'lowStockCaps'));
    }
}
