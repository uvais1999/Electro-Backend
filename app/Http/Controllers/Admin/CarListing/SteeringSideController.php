<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\SteeringSide;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SteeringSideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sides = SteeringSide::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/SteeringSide/Index', [
            'sides' => $sides
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:steering_sides',
        ]);

        SteeringSide::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Steering side added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SteeringSide $steeringSide)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:steering_sides,name,' . $steeringSide->id,
            'status' => 'required|boolean',
        ]);

        $steeringSide->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Steering side updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SteeringSide $steeringSide)
    {
        $steeringSide->delete();
        return redirect()->back()->with('success', 'Steering side deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(SteeringSide $steeringSide)
    {
        $steeringSide->status = !$steeringSide->status;
        $steeringSide->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
