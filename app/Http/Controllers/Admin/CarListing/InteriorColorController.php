<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\InteriorColor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InteriorColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = InteriorColor::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/InteriorColor/Index', [
            'colors' => $colors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:interior_colors',
        ]);

        InteriorColor::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Interior color created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InteriorColor $interiorColor)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:interior_colors,name,' . $interiorColor->id,
            'status' => 'required|boolean',
        ]);

        $interiorColor->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Interior color updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InteriorColor $interiorColor)
    {
        $interiorColor->delete();
        return redirect()->back()->with('success', 'Interior color deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(InteriorColor $interiorColor)
    {
        $interiorColor->status = !$interiorColor->status;
        $interiorColor->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
