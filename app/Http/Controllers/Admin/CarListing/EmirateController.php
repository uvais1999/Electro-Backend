<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\Emirate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmirateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emirates = Emirate::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/Emirate/Index', [
            'emirates' => $emirates
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:emirates',
        ]);

        Emirate::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Emirate added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Emirate $emirate)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:emirates,name,' . $emirate->id,
            'status' => 'required|boolean',
        ]);

        $emirate->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Emirate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Emirate $emirate)
    {
        $emirate->delete();
        return redirect()->back()->with('success', 'Emirate deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(Emirate $emirate)
    {
        $emirate->update(['status' => !$emirate->status]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
