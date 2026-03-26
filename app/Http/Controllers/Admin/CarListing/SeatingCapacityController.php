<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\SeatingCapacity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeatingCapacityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $capacities = SeatingCapacity::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/SeatingCapacity/Index', [
            'capacities' => $capacities
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:seating_capacities',
        ]);

        SeatingCapacity::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Seating capacity added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeatingCapacity $seatingCapacity)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:seating_capacities,name,' . $seatingCapacity->id,
            'status' => 'required|boolean',
        ]);

        $seatingCapacity->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Seating capacity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeatingCapacity $seatingCapacity)
    {
        $seatingCapacity->delete();
        return redirect()->back()->with('success', 'Seating capacity deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(SeatingCapacity $seatingCapacity)
    {
        $seatingCapacity->status = !$seatingCapacity->status;
        $seatingCapacity->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
