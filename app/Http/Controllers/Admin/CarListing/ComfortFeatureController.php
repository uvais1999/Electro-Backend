<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\ComfortFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComfortFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = ComfortFeature::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/ComfortFeature/Index', [
            'features' => $features
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:comfort_features',
        ]);

        ComfortFeature::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Comfort feature added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComfortFeature $comfortFeature)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:comfort_features,name,' . $comfortFeature->id,
            'status' => 'required|boolean',
        ]);

        $comfortFeature->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Comfort feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComfortFeature $comfortFeature)
    {
        $comfortFeature->delete();
        return redirect()->back()->with('success', 'Comfort feature deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(ComfortFeature $comfortFeature)
    {
        $comfortFeature->status = !$comfortFeature->status;
        $comfortFeature->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
