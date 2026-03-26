<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\TechFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TechFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = TechFeature::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/TechFeature/Index', [
            'features' => $features
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tech_features',
        ]);

        TechFeature::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Technology feature added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TechFeature $techFeature)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tech_features,name,' . $techFeature->id,
            'status' => 'required|boolean',
        ]);

        $techFeature->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Technology feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TechFeature $techFeature)
    {
        $techFeature->delete();
        return redirect()->back()->with('success', 'Technology feature deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(TechFeature $techFeature)
    {
        $techFeature->status = !$techFeature->status;
        $techFeature->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
