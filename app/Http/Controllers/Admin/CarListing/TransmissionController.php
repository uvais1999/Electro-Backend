<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transmissions = Transmission::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/Transmission/Index', [
            'transmissions' => $transmissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:transmissions',
        ]);

        Transmission::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Transmission added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transmission $transmission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:transmissions,name,' . $transmission->id,
            'status' => 'required|boolean',
        ]);

        $transmission->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Transmission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transmission $transmission)
    {
        $transmission->delete();
        return redirect()->back()->with('success', 'Transmission deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(Transmission $transmission)
    {
        $transmission->status = !$transmission->status;
        $transmission->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
