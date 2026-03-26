<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = request()->is('admin/private-sellers*') ? User::ROLE_PRIVATE_SELLER : User::ROLE_BUYER;
        $title = $role === User::ROLE_PRIVATE_SELLER ? 'Private Sellers' : 'Buyers';
        
        $query = User::role($role)->latest();
        if ($request->date) $query->whereDate('created_at', $request->date);

        $users = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Admin/Dealers/Index', [
            'users' => $users,
            'title' => $title,
            'role' => $role,
            'filters' => $request->only(['date'])
        ]);
    }

    public function store(Request $request)
    {
        $role = request()->is('admin/private-sellers*') ? User::ROLE_PRIVATE_SELLER : User::ROLE_BUYER;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => true,
            'verification' => User::VERIFICATION_APPROVED,
        ]);

        $user->assignRole($role);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function approve(User $user)
    {
        $user->update(['verification' => User::VERIFICATION_APPROVED]);
        return redirect()->back()->with('success', 'User verified successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);
        return redirect()->back()->with('success', 'User status updated successfully.');
    }
}
