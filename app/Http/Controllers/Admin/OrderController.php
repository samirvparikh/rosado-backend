<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('items') // product thumbnails in the list
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($q) => $q
                    ->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_full_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:PLACED,CONFIRMED,SHIPPED,DELIVERED,CANCELLED'],
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Order status updated.');
    }
}
