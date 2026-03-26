<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\CarTrim;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trims = CarTrim::with(['model.make'])->latest()->paginate(10);
        $makes = CarMake::where('status', true)->get();
        
        return Inertia::render('Admin/CarListing/Trim/Index', [
            'trims' => $trims,
            'makes' => $makes
        ]);
    }

    /**
     * Get models for a specific make (for dynamic selects).
     */
    public function getModels(CarMake $make)
    {
        return response()->json($make->models()->where('status', true)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'car_model_id' => 'required|exists:car_models,id',
            'name' => 'required|string|max:255',
        ]);

        CarTrim::create([
            'car_model_id' => $request->car_model_id,
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Trim created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CarTrim $trim)
    {
        $request->validate([
            'car_model_id' => 'required|exists:car_models,id',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $trim->update([
            'car_model_id' => $request->car_model_id,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Trim updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarTrim $trim)
    {
        $trim->delete();
        return redirect()->back()->with('success', 'Trim deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(CarTrim $trim)
    {
        $trim->update(['status' => !$trim->status]);
        return redirect()->back()->with('success', 'Trim status updated.');
    }
}
