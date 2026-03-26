<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use App\Models\CarMake;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $makes = CarMake::latest()->paginate(10);
        return Inertia::render('Admin/CarListing/Make/Index', [
            'makes' => $makes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:car_makes',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('makes', 's3');
        }

        CarMake::create([
            'name' => $request->name,
            'image' => $imagePath,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Make created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CarMake $make)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:car_makes,name,' . $make->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
        ]);

        $make->name = $request->name;
        $make->status = $request->status;

        if ($request->hasFile('image')) {
            if ($make->image) {
                Storage::disk('s3')->delete($make->image);
            }
            $make->image = $request->file('image')->store('makes', 's3');
        }

        $make->save();

        return redirect()->back()->with('success', 'Make updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarMake $make)
    {
        if ($make->image) {
            Storage::disk('s3')->delete($make->image);
        }
        $make->delete();

        return redirect()->back()->with('success', 'Make deleted successfully.');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(CarMake $make)
    {
        $make->update(['status' => !$make->status]);
        return redirect()->back()->with('success', 'Make status updated.');
    }
}
