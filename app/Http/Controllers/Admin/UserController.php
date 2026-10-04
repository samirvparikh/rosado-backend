<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/** Customers = accounts created by signing up on the storefront (not admin users). */
class UserController extends Controller
{
    public function index(): View
    {
        $users = User::where('is_admin', false)->withCount('orders')->orderByDesc('created_at')->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}
