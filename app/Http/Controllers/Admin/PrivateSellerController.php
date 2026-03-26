<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Inertia\Inertia;

class PrivateSellerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('private_seller')->with(['latestLogin', 'latestActivity', 'activityLogs' => fn($q) => $q->latest()])->latest();

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Dealers/Index', [
            'users' => $users,
            'title' => 'Private Sellers',
            'role' => 'private_seller',
            'filters' => $request->only(['date'])
        ]);
    }

    public function store(Request $request)
    {
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

        $user->assignRole('private_seller');

        return redirect()->back()->with('success', 'Private seller created successfully.');
    }

    public function update(Request $request, User $private_seller)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $private_seller->id,
            'password' => 'nullable|string|min:8',
        ]);

        $private_seller->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $private_seller->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'Private seller updated successfully.');
    }

    public function destroy(User $private_seller)
    {
        $private_seller->delete();

        return redirect()->back()->with('success', 'Private seller deleted successfully.');
    }

    public function approve(User $user)
    {
        $user->update(['verification' => User::VERIFICATION_APPROVED]);

        return redirect()->back()->with('success', 'Private seller verified successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);

        return redirect()->back()->with('success', 'Private seller status updated successfully.');
    }
}
