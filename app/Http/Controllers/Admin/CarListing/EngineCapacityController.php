<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\EngineCapacity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EngineCapacityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $capacities = EngineCapacity::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/EngineCapacity/Index', [
            'capacities' => $capacities
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:engine_capacities',
        ]);

        EngineCapacity::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Engine capacity added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EngineCapacity $engineCapacity)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:engine_capacities,name,' . $engineCapacity->id,
            'status' => 'required|boolean',
        ]);

        $engineCapacity->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Engine capacity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EngineCapacity $engineCapacity)
    {
        $engineCapacity->delete();
        return redirect()->back()->with('success', 'Engine capacity deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(EngineCapacity $engineCapacity)
    {
        $engineCapacity->status = !$engineCapacity->status;
        $engineCapacity->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
