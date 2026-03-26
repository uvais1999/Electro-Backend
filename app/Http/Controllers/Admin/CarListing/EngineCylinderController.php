<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\EngineCylinder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EngineCylinderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cylinders = EngineCylinder::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/EngineCylinder/Index', [
            'cylinders' => $cylinders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:engine_cylinders',
        ]);

        EngineCylinder::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Engine cylinder added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EngineCylinder $engineCylinder)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:engine_cylinders,name,' . $engineCylinder->id,
            'status' => 'required|boolean',
        ]);

        $engineCylinder->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Engine cylinder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EngineCylinder $engineCylinder)
    {
        $engineCylinder->delete();
        return redirect()->back()->with('success', 'Engine cylinder deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(EngineCylinder $engineCylinder)
    {
        $engineCylinder->status = !$engineCylinder->status;
        $engineCylinder->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
