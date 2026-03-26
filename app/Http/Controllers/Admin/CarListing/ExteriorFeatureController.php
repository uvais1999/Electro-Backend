<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\ExteriorFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExteriorFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = ExteriorFeature::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/ExteriorFeature/Index', [
            'features' => $features
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exterior_features',
        ]);

        ExteriorFeature::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Exterior feature added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExteriorFeature $exteriorFeature)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exterior_features,name,' . $exteriorFeature->id,
            'status' => 'required|boolean',
        ]);

        $exteriorFeature->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Exterior feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExteriorFeature $exteriorFeature)
    {
        $exteriorFeature->delete();
        return redirect()->back()->with('success', 'Exterior feature deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(ExteriorFeature $exteriorFeature)
    {
        $exteriorFeature->status = !$exteriorFeature->status;
        $exteriorFeature->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
