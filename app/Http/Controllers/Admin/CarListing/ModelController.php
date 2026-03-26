<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\CarMake;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $models = CarModel::with('make')->latest()->paginate(10);
        $makes = CarMake::where('status', true)->get();
        
        return Inertia::render('Admin/CarListing/Model/Index', [
            'models' => $models,
            'makes' => $makes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'car_make_id' => 'required|exists:car_makes,id',
            'name' => 'required|string|max:255',
        ]);

        CarModel::create([
            'car_make_id' => $request->car_make_id,
            'name' => $request->name,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Model created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CarModel $model)
    {
        $request->validate([
            'car_make_id' => 'required|exists:car_makes,id',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $model->update([
            'car_make_id' => $request->car_make_id,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Model updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarModel $model)
    {
        $model->delete();
        return redirect()->back()->with('success', 'Model deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(CarModel $model)
    {
        $model->update(['status' => !$model->status]);
        return redirect()->back()->with('success', 'Model status updated.');
    }
}
