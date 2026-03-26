<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\Horsepower;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HorsepowerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $horsepowers = Horsepower::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/Horsepower/Index', [
            'horsepowers' => $horsepowers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:horsepowers',
        ]);

        Horsepower::create([
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Horsepower added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Horsepower $horsepower)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:horsepowers,name,' . $horsepower->id,
            'status' => 'required|boolean',
        ]);

        $horsepower->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Horsepower updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Horsepower $horsepower)
    {
        $horsepower->delete();
        return redirect()->back()->with('success', 'Horsepower deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(Horsepower $horsepower)
    {
        $horsepower->status = !$horsepower->status;
        $horsepower->save();
        return redirect()->back()->with('success', 'Status updated.');
    }
}
