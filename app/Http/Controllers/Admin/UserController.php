<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('orders')->orderByDesc('created_at')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate(['is_admin' => ['sometimes', 'boolean']]);

        if ($user->id === $request->user()->id && ! $request->boolean('is_admin')) {
            return back()->withErrors(['is_admin' => 'You cannot remove your own admin access.']);
        }

        $user->update(['is_admin' => $request->boolean('is_admin')]);

        return redirect()->route('admin.users.index')->with('status', 'Customer updated.');
    }
}
