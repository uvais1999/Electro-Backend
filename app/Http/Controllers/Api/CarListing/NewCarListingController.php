<?php

namespace App\Http\Controllers\Api\CarListing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarListing;
use App\Models\CarListingImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewCarListingController extends Controller
{
    /**
     * Display a listing of new car listings with filters.
     */
    public function index(Request $request)
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
     * Get master data for new car search filters.
     */
    public function filterData()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'emirates' => \App\Models\Emirate::orderBy('name')->get(),
                'makes_and_models' => $this->getMakesAndModels(),
                'regional_specs' => \App\Models\RegionalSpec::orderBy('name')->get(),
                'tech_features' => \App\Models\TechFeature::orderBy('name')->get(),
                'comfort_features' => \App\Models\ComfortFeature::orderBy('name')->get(),
                'safety_features' => \App\Models\SafetyFeature::orderBy('name')->get(),
                'exterior_features' => \App\Models\ExteriorFeature::orderBy('name')->get(),
                'body_types' => \App\Models\BodyType::orderBy('name')->get(),
                'seating_capacities' => \App\Models\SeatingCapacity::orderBy('name')->get(),
                'charging_types' => \App\Models\ChargingType::orderBy('name')->get(),
                'transmissions' => \App\Models\Transmission::orderBy('name')->get(),
                'engine_cylinders' => \App\Models\EngineCylinder::orderBy('name')->get(),
                'exterior_colors' => \App\Models\ExteriorColor::orderBy('name')->get(),
                'interior_colors' => \App\Models\InteriorColor::orderBy('name')->get(),
                'door_types' => \App\Models\DoorType::orderBy('name')->get(),
                'seller_types' => ['Private Seller', 'Dealer'],
                'warranty_options' => \App\Models\WarrantyOption::orderBy('name')->get(),
                'horsepowers' => \App\Models\Horsepower::orderBy('name')->get(),
                'engine_capacities' => \App\Models\EngineCapacity::orderBy('name')->get(),
                'steering_sides' => \App\Models\SteeringSide::orderBy('name')->get(),
                'ads_posted' => [
                    ['id' => 'today', 'name' => 'Today'],
                    ['id' => '3_days', 'name' => 'Within 3 days'],
                    ['id' => '1_week', 'name' => 'Within 1 week'],
                    ['id' => '2_weeks', 'name' => 'Within 2 weeks'],
                    ['id' => '1_month', 'name' => 'Within 1 month'],
                    ['id' => '3_months', 'name' => 'Within 3 months'],
                    ['id' => '6_months', 'name' => 'Within 6 months'],
                ]
            ]
        ]);
    }

    /**
     * Get data required for creating a new car listing form.
     */
    public function createData()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'makes' => \App\Models\CarMake::with('models.trims')->where('status', true)->orderBy('name')->get(),
                'emirates' => \App\Models\Emirate::where('status', true)->orderBy('name')->get(),
                'body_types' => \App\Models\BodyType::where('status', true)->orderBy('name')->get(),
                'regional_specs' => \App\Models\RegionalSpec::where('status', true)->orderBy('name')->get(),
                'transmissions' => \App\Models\Transmission::where('status', true)->orderBy('name')->get(),
                'charging_types' => \App\Models\ChargingType::where('status', true)->orderBy('name')->get(),
                'exterior_colors' => \App\Models\ExteriorColor::where('status', true)->orderBy('name')->get(),
                'interior_colors' => \App\Models\InteriorColor::where('status', true)->orderBy('name')->get(),
                'warranty_options' => \App\Models\WarrantyOption::where('status', true)->orderBy('name')->get(),
                'door_types' => \App\Models\DoorType::where('status', true)->orderBy('name')->get(),
                'engine_cylinders' => \App\Models\EngineCylinder::where('status', true)->orderBy('name')->get(),
                'seating_capacities' => \App\Models\SeatingCapacity::where('status', true)->orderBy('name')->get(),
                'horsepowers' => \App\Models\Horsepower::where('status', true)->orderBy('name')->get(),
                'steering_sides' => \App\Models\SteeringSide::where('status', true)->orderBy('name')->get(),
                'engine_capacities' => \App\Models\EngineCapacity::where('status', true)->orderBy('name')->get(),
                'safety_features' => \App\Models\SafetyFeature::where('status', true)->orderBy('name')->get(),
                'tech_features' => \App\Models\TechFeature::where('status', true)->orderBy('name')->get(),
                'comfort_features' => \App\Models\ComfortFeature::where('status', true)->orderBy('name')->get(),
                'exterior_features' => \App\Models\ExteriorFeature::where('status', true)->orderBy('name')->get(),
            ]
        ]);
    }

    /**
     * Display the specified new car listing details.
     */
    public function show(CarListing $new_car_listing)
    {
        // Ensure it's new car
        if ($new_car_listing->condition_type !== 'new') {
            return response()->json(['success' => false, 'message' => 'Listing not found.'], 404);
        }

        // Only show if approved
        if ($new_car_listing->status !== 'approved' && !request()->user()?->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Listing not found or not approved.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $new_car_listing->load([
                'images',
                'make',
                'model',
                'trim',
                'regionalSpec',
                'bodyType',
                'exteriorColor',
                'interiorColor',
                'warrantyOption',
                'chargingType',
                'doorType',
                'engineCylinder',
                'transmission',
                'seatingCapacity',
                'horsepower',
                'steeringSide',
                'engineCapacity',
                'emirate',
                'safetyFeatures',
                'techFeatures',
                'comfortFeatures',
                'exteriorFeatures',
                'user'
            ])
        ]);
    }

    /**
     * Store a newly created listing in storage via API.
     */
    public function store(Request $request)
    {
        if ($request->user()->verification !== \App\Models\User::VERIFICATION_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not verified. Only verified users can post listings.'
            ], 403);
        }

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
        $validated['user_id'] = $request->user()->id;
        $validated['condition_type'] = 'new';

        // Auto-verify if dealer
        $validated['is_verified'] = $request->user()->hasRole('dealer');
        $validated['status'] = $validated['is_verified'] ? 'approved' : 'pending';

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
                'message' => 'Car listing created successfully.',
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

    /**
     * Apply common filters to the listing query.
     */
    protected function applyFilters($query, Request $request)
    {
        // Combined Make/Model Filter (Supports Arrays)
        if ($request->filled('make_or_model')) {
            $vals = is_array($request->make_or_model) ? $request->make_or_model : explode(',', $request->make_or_model);

            $query->where(function ($q) use ($vals) {
                foreach ($vals as $val) {
                    if (str_starts_with($val, 'make_')) {
                        $q->orWhere('car_make_id', str_replace('make_', '', $val));
                    } elseif (str_starts_with($val, 'model_')) {
                        $q->orWhere('car_model_id', str_replace('model_', '', $val));
                    }
                }
            });
        }

        // Basic Metadata
        if ($request->filled('emirate_id')) $query->where('emirate_id', $request->emirate_id);

        // Year Range
        if ($request->filled('year_from')) $query->where('year', '>=', $request->year_from);
        if ($request->filled('year_to')) $query->where('year', '<=', $request->year_to);

        // Price Range
        if ($request->filled('price_from')) $query->where('price', '>=', $request->price_from);
        if ($request->filled('price_to')) $query->where('price', '<=', $request->price_to);

        // Kilometer Range
        if ($request->filled('km_min')) $query->where('kilometers', '>=', $request->km_min);
        if ($request->filled('km_max')) $query->where('kilometers', '<=', $request->km_max);

        // Regional spec (Supports Arrays)
        if ($request->filled('regional_spec_ids')) {
            $specs = is_array($request->regional_spec_ids) ? $request->regional_spec_ids : explode(',', $request->regional_spec_ids);
            $query->whereIn('regional_spec_id', $specs);
        }

        // Feature Filters (Many-to-Many)
        if ($request->filled('safety_features')) {
            $features = is_array($request->safety_features) ? $request->safety_features : explode(',', $request->safety_features);
            $query->whereHas('safetyFeatures', function ($q) use ($features) {
                $q->whereIn('safety_features.id', $features);
            });
        }
        if ($request->filled('comfort_features')) {
            $features = is_array($request->comfort_features) ? $request->comfort_features : explode(',', $request->comfort_features);
            $query->whereHas('comfortFeatures', function ($q) use ($features) {
                $q->whereIn('comfort_features.id', $features);
            });
        }
        if ($request->filled('tech_features')) {
            $features = is_array($request->tech_features) ? $request->tech_features : explode(',', $request->tech_features);
            $query->whereHas('techFeatures', function ($q) use ($features) {
                $q->whereIn('tech_features.id', $features);
            });
        }
        if ($request->filled('exterior_features')) {
            $features = is_array($request->exterior_features) ? $request->exterior_features : explode(',', $request->exterior_features);
            $query->whereHas('exteriorFeatures', function ($q) use ($features) {
                $q->whereIn('exterior_features.id', $features);
            });
        }

        // Technical Specs
        if ($request->filled('body_type_ids')) {
            $ids = is_array($request->body_type_ids) ? $request->body_type_ids : explode(',', $request->body_type_ids);
            $query->whereIn('body_type_id', $ids);
        }
        if ($request->filled('seating_capacity_ids')) {
            $ids = is_array($request->seating_capacity_ids) ? $request->seating_capacity_ids : explode(',', $request->seating_capacity_ids);
            $query->whereIn('seating_capacity_id', $ids);
        }
        if ($request->filled('charging_type_ids')) {
            $ids = is_array($request->charging_type_ids) ? $request->charging_type_ids : explode(',', $request->charging_type_ids);
            $query->whereIn('charging_type_id', $ids);
        }
        if ($request->filled('transmission_ids')) {
            $ids = is_array($request->transmission_ids) ? $request->transmission_ids : explode(',', $request->transmission_ids);
            $query->whereIn('transmission_id', $ids);
        }
        if ($request->filled('engine_cylinder_ids')) {
            $ids = is_array($request->engine_cylinder_ids) ? $request->engine_cylinder_ids : explode(',', $request->engine_cylinder_ids);
            $query->whereIn('engine_cylinder_id', $ids);
        }
        if ($request->filled('exterior_color_ids')) {
            $ids = is_array($request->exterior_color_ids) ? $request->exterior_color_ids : explode(',', $request->exterior_color_ids);
            $query->whereIn('exterior_color_id', $ids);
        }
        if ($request->filled('interior_color_ids')) {
            $ids = is_array($request->interior_color_ids) ? $request->interior_color_ids : explode(',', $request->interior_color_ids);
            $query->whereIn('interior_color_id', $ids);
        }
        if ($request->filled('door_type_ids')) {
            $ids = is_array($request->door_type_ids) ? $request->door_type_ids : explode(',', $request->door_type_ids);
            $query->whereIn('door_type_id', $ids);
        }

        if ($request->filled('seller_types')) {
            $types = is_array($request->seller_types) ? $request->seller_types : explode(',', $request->seller_types);
            $query->whereHas('user', function ($q) use ($types) {
                $q->where(function ($sq) use ($types) {
                    foreach ($types as $type) {
                        $role = null;
                        if (in_array(strtolower($type), ['owner', 'private seller'])) {
                            $role = User::ROLE_PRIVATE_SELLER;
                        } elseif (strtolower($type) === 'dealer') {
                            $role = User::ROLE_DEALER;
                        }

                        if ($role) {
                            $sq->orWhereHas('roles', function ($rq) use ($role) {
                                $rq->where('name', $role);
                            });
                        }
                    }
                });
            });
        }
        // Dealer Name search (via user relationship)
        if ($request->filled('dealer_name')) {
            $dealerName = $request->dealer_name;
            $query->whereHas('user', function ($q) use ($dealerName) {
                $q->where('name', 'like', "%{$dealerName}%");
            });
        }

        if ($request->filled('warranty_option_ids')) {
            $ids = is_array($request->warranty_option_ids) ? $request->warranty_option_ids : explode(',', $request->warranty_option_ids);
            $query->whereIn('warranty_option_id', $ids);
        }
        if ($request->filled('horsepower_ids')) {
            $ids = is_array($request->horsepower_ids) ? $request->horsepower_ids : explode(',', $request->horsepower_ids);
            $query->whereIn('horsepower_id', $ids);
        }
        if ($request->filled('engine_capacity_ids')) {
            $ids = is_array($request->engine_capacity_ids) ? $request->engine_capacity_ids : explode(',', $request->engine_capacity_ids);
            $query->whereIn('engine_capacity_id', $ids);
        }
        // Todo: export status
        if ($request->filled('steering_side_ids')) {
            $ids = is_array($request->steering_side_ids) ? $request->steering_side_ids : explode(',', $request->steering_side_ids);
            $query->whereIn('steering_side_id', $ids);
        }
        // Todo : Badges
        // Ads Posted (created_at)
        if ($request->filled('ads_posted')) {
            $val = $request->ads_posted;
            $date = null;

            if ($val === 'today') $date = now()->startOfDay();
            elseif ($val === '3_days') $date = now()->subDays(3);
            elseif ($val === '1_week') $date = now()->subWeeks(1);
            elseif ($val === '2_weeks') $date = now()->subWeeks(2);
            elseif ($val === '1_month') $date = now()->subMonths(1);
            elseif ($val === '3_months') $date = now()->subMonths(3);
            elseif ($val === '6_months') $date = now()->subMonths(6);

            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }


        // Todo : Location

        $query->where('is_verified', true);
        return $query;
    }

    /**
     * Helper to get combined makes and models list.
     */
    protected function getMakesAndModels()
    {
        $makes = \App\Models\CarMake::orderBy('name')->get()->map(function ($make) {
            return [
                'id' => "make_{$make->id}",
                'name' => $make->name,
                'type' => 'make',
                'original_id' => $make->id
            ];
        });

        $models = \App\Models\CarModel::with('make')->orderBy('name')->get()->map(function ($model) {
            return [
                'id' => "model_{$model->id}",
                'name' => ($model->make?->name ?? '') . ' ' . $model->name,
                'type' => 'model',
                'original_id' => $model->id,
                'make_id' => $model->car_make_id
            ];
        });

        return $makes->concat($models)->sortBy('name')->values();
    }
}
