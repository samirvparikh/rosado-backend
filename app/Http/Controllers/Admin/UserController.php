<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Customers = accounts created by signing up on the storefront (not admin users). */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::where('is_admin', false)
            // Default address first; it's the one checkout pre-fills and keeps updated.
            ->with(['addresses' => fn ($q) => $q->orderByDesc('is_default')->orderByDesc('id')])
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn ($q) => $q->where('status', '!=', 'CANCELLED')], 'final_price')
            ->withMax('orders as last_order_at', 'created_at')
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(fn ($q) => $q
                    ->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('mobile', 'like', $like)
                    ->orWhereHas('addresses', fn ($a) => $a
                        ->where('address_line', 'like', $like)
                        ->orWhere('city', 'like', $like)
                        ->orWhere('state', 'like', $like)
                        ->orWhere('pincode', 'like', $like)));
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }
}
