<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\DoorType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoorTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = DoorType::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/DoorType/Index', [
            'types' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:door_types',
        ]);

        DoorType::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Door type added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DoorType $doorType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:door_types,name,' . $doorType->id,
            'status' => 'required|boolean',
        ]);

        $doorType->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Door type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DoorType $doorType)
    {
        $doorType->delete();
        return redirect()->back()->with('success', 'Door type deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(DoorType $doorType)
    {
        $doorType->status = !$doorType->status;
        $doorType->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
