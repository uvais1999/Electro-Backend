<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\RegionalSpec;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegionalSpecController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specs = RegionalSpec::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/RegionalSpec/Index', [
            'specs' => $specs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:regional_specs',
        ]);

        RegionalSpec::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Regional Specification created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegionalSpec $regionalSpec)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:regional_specs,name,' . $regionalSpec->id,
            'status' => 'required|boolean',
        ]);

        $regionalSpec->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Regional Specification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegionalSpec $regionalSpec)
    {
        $regionalSpec->delete();
        return redirect()->back()->with('success', 'Regional Specification deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(RegionalSpec $regionalSpec)
    {
        $regionalSpec->update(['status' => !$regionalSpec->status]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
