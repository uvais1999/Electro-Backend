<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\ChargingType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChargingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = ChargingType::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/ChargingType/Index', [
            'types' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:charging_types',
        ]);

        ChargingType::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Charging type added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChargingType $chargingType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:charging_types,name,' . $chargingType->id,
            'status' => 'required|boolean',
        ]);

        $chargingType->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Charging type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChargingType $chargingType)
    {
        $chargingType->delete();
        return redirect()->back()->with('success', 'Charging type deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(ChargingType $chargingType)
    {
        $chargingType->status = !$chargingType->status;
        $chargingType->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
