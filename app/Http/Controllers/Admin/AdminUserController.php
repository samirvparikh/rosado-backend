<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Back-office accounts (users.is_admin = true). Storefront sign-ups are
 * customers and live under Customers; the two lists never mix.
 */
class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = User::where('is_admin', true)->orderBy('name')->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.form', ['admin' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);

        User::create([...$data, 'is_admin' => true]);

        return redirect()->route('admin.admins.index')->with('status', 'Admin user created.');
    }

    public function edit(User $admin): View
    {
        abort_unless($admin->is_admin, 404);

        return view('admin.admins.form', compact('admin'));
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->is_admin, 404);

        $data = $this->validated($request, $admin);
        if (empty($data['password'])) {
            unset($data['password']); // blank = keep current password
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')->with('status', 'Admin user updated.');
    }

    public function destroy(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->is_admin, 404);

        if ($admin->is($request->user())) {
            return back()->withErrors(['admin' => 'You cannot delete your own account.']);
        }
        if (User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['admin' => 'At least one admin user must remain.']);
        }

        $admin->tokens()->delete();
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('status', 'Admin user deleted.');
    }

    private function validated(Request $request, ?User $admin): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                // Unique across all users -- an email can't be both a customer and an admin.
                Rule::unique('users', 'email')->ignore($admin?->id),
            ],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => [$admin ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'This email is already used by another account (admin or customer).',
        ]);
    }
}
