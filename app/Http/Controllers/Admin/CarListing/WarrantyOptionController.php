<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\WarrantyOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WarrantyOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $options = WarrantyOption::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/WarrantyOption/Index', [
            'options' => $options
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warranty_options',
        ]);

        WarrantyOption::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Warranty option added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WarrantyOption $warrantyOption)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warranty_options,name,' . $warrantyOption->id,
            'status' => 'required|boolean',
        ]);

        $warrantyOption->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Warranty option updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WarrantyOption $warrantyOption)
    {
        $warrantyOption->delete();
        return redirect()->back()->with('success', 'Warranty option deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(WarrantyOption $warrantyOption)
    {
        $warrantyOption->status = !$warrantyOption->status;
        $warrantyOption->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
