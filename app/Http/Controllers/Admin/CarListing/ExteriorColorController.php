<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\ExteriorColor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExteriorColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = ExteriorColor::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/ExteriorColor/Index', [
            'colors' => $colors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exterior_colors',
        ]);

        ExteriorColor::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Exterior color created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExteriorColor $exteriorColor)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exterior_colors,name,' . $exteriorColor->id,
            'status' => 'required|boolean',
        ]);

        $exteriorColor->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Exterior color updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExteriorColor $exteriorColor)
    {
        $exteriorColor->delete();
        return redirect()->back()->with('success', 'Exterior color deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(ExteriorColor $exteriorColor)
    {
        $exteriorColor->status = !$exteriorColor->status;
        $exteriorColor->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
