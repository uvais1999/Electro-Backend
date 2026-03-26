<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\SafetyFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SafetyFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = SafetyFeature::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/SafetyFeature/Index', [
            'features' => $features
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:safety_features',
        ]);

        SafetyFeature::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Safety feature added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SafetyFeature $safetyFeature)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:safety_features,name,' . $safetyFeature->id,
            'status' => 'required|boolean',
        ]);

        $safetyFeature->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Safety feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SafetyFeature $safetyFeature)
    {
        $safetyFeature->delete();
        return redirect()->back()->with('success', 'Safety feature deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(SafetyFeature $safetyFeature)
    {
        $safetyFeature->status = !$safetyFeature->status;
        $safetyFeature->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
