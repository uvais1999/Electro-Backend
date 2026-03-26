<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarListing;
use App\Models\CarListingImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarListingController extends Controller
{
    /**
     * Display a listing of car listings with filters.
     */
    public function index(Request $request)
    {
        $query = CarListing::with(['images', 'make', 'model', 'emirate', 'user'])
            ->where('status', 'approved');

        $this->applyFilters($query, $request);

        $listings = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $listings
        ]);
    }

    /**
     * Get used car listings.
     */
    public function usedCars(Request $request)
    {
        $query = CarListing::with(['images', 'make', 'model', 'emirate', 'user'])
            ->where('status', 'approved')
            ->where('condition_type', 'used');

        $this->applyFilters($query, $request);

        $listings = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $listings
        ]);
    }

    /**
     * Get new car listings.
     */
    public function newCars(Request $request)
    {
        $query = CarListing::with(['images', 'make', 'model', 'emirate', 'user'])
            ->where('status', 'approved')
            ->where('condition_type', 'new');

        $this->applyFilters($query, $request);

        $listings = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $listings
        ]);
    }

    /**
     * Display the specified car listing details.
     */
    public function show(CarListing $carListing)
    {
        // Only show if approved
        if ($carListing->status !== 'approved' && !request()->user()?->hasRole('admin')) {
             return response()->json(['success' => false, 'message' => 'Listing not found or not approved.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $carListing->load([
                'images', 'make', 'model', 'trim', 'regionalSpec', 'bodyType',
                'exteriorColor', 'interiorColor', 'warrantyOption', 'chargingType',
                'doorType', 'engineCylinder', 'transmission', 'seatingCapacity',
                'horsepower', 'steeringSide', 'engineCapacity', 'emirate',
                'safetyFeatures', 'techFeatures', 'comfortFeatures', 'exteriorFeatures',
                'user'
            ])
        ]);
    }

    /**
     * Apply common filters to the listing query.
     */
    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('make_id')) {
            $query->where('car_make_id', $request->make_id);
        }

        if ($request->filled('model_id')) {
            $query->where('car_model_id', $request->model_id);
        }

        if ($request->filled('emirate_id')) {
            $query->where('emirate_id', $request->emirate_id);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', filter_var($request->is_verified, FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }

    /**
     * Store a newly created listing in storage via API.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emirate_id' => 'required|exists:emirates,id',
            'car_make_id' => 'required|exists:car_makes,id',
            'car_model_id' => 'required|exists:car_models,id',
            'car_trim_id' => 'required|exists:car_trims,id',
            'regional_spec_id' => 'required|exists:regional_specs,id',
            'year' => 'required|integer|min:1950|max:' . (now()->year + 1),
            'kilometers' => 'required|integer|min:0',
            'body_type_id' => 'required|exists:body_types,id',
            'is_insured' => 'required|boolean',
            'price' => 'required|numeric|min:0',
            'phone_number' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'exterior_color_id' => 'required|exists:exterior_colors,id',
            'interior_color_id' => 'required|exists:interior_colors,id',
            'warranty_option_id' => 'required|exists:warranty_options,id',
            'charging_type_id' => 'required|exists:charging_types,id',
            'door_type_id' => 'required|exists:door_types,id',
            'engine_cylinder_id' => 'required|exists:engine_cylinders,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'seating_capacity_id' => 'nullable|exists:seating_capacities,id',
            'horsepower_id' => 'required|exists:horsepowers,id',
            'steering_side_id' => 'required|exists:steering_sides,id',
            'engine_capacity_id' => 'nullable|exists:engine_capacities,id',
            'location' => 'nullable|string',
            'building_street' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'condition_type' => 'required|in:new,used',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'safety_features' => 'array',
            'safety_features.*' => 'exists:safety_features,id',
            'tech_features' => 'array',
            'tech_features.*' => 'exists:tech_features,id',
            'comfort_features' => 'array',
            'comfort_features.*' => 'exists:comfort_features,id',
            'exterior_features' => 'array',
            'exterior_features.*' => 'exists:exterior_features,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = $request->user()->id; // Automatic user id from auth
        $validated['status'] = 'pending';

        DB::beginTransaction();

        try {
            // Create Listing
            $listing = CarListing::create($validated);

            // Handle Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('car-listings', 's3');
                    CarListingImage::create([
                        'car_listing_id' => $listing->id,
                        'image' => $path,
                        'is_main' => $index === 0,
                    ]);
                }
            }

            // Sync Features
            if ($request->has('safety_features')) $listing->safetyFeatures()->sync($request->safety_features);
            if ($request->has('tech_features')) $listing->techFeatures()->sync($request->tech_features);
            if ($request->has('comfort_features')) $listing->comfortFeatures()->sync($request->comfort_features);
            if ($request->has('exterior_features')) $listing->exteriorFeatures()->sync($request->exterior_features);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Car listing created successfully and is now pending approval.',
                'data' => $listing->load('images')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Could not create listing: ' . $e->getMessage()
            ], 500);
        }
    }
}
