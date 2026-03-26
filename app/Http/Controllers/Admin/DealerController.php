<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Inertia\Inertia;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('dealer')->with(['latestLogin', 'latestActivity', 'activityLogs' => fn($q) => $q->latest()])->latest();

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Dealers/Index', [
            'users' => $users,
            'title' => 'Dealers',
            'role' => 'dealer',
            'filters' => $request->only(['date'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:dealer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'Dealer created successfully.');
    }

    public function approve(User $user)
    {
        $user->update(['verification' => User::VERIFICATION_APPROVED]);

        return redirect()->back()->with('success', 'Dealer verified successfully.');
    }

    public function update(Request $request, User $dealer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $dealer->id,
            'password' => 'nullable|string|min:8',
        ]);

        $dealer->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $dealer->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'Dealer updated successfully.');
    }

    public function destroy(User $dealer)
    {
        $dealer->delete();

        return redirect()->back()->with('success', 'Dealer deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);

        return redirect()->back()->with('success', 'Dealer status updated successfully.');
    }
}
