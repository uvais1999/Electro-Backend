<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\BodyType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BodyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = BodyType::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/BodyType/Index', [
            'types' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:body_types',
        ]);

        BodyType::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Body Type created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BodyType $bodyType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:body_types,name,' . $bodyType->id,
            'status' => 'required|boolean',
        ]);

        $bodyType->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Body Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BodyType $bodyType)
    {
        $bodyType->delete();
        return redirect()->back()->with('success', 'Body Type deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(BodyType $bodyType)
    {
        $bodyType->update(['status' => !$bodyType->status]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
