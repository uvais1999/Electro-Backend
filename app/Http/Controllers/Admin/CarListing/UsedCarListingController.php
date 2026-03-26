<?php

namespace App\Http\Controllers\Admin\CarListing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
// Import specs models
use App\Models\Emirate;
use App\Models\CarMake;
use App\Models\RegionalSpec;
use App\Models\BodyType;
use App\Models\ExteriorColor;
use App\Models\InteriorColor;
use App\Models\ChargingType;
use App\Models\DoorType;
use App\Models\EngineCylinder;
use App\Models\Transmission;
use App\Models\SeatingCapacity;
use App\Models\Horsepower;
use App\Models\SteeringSide;
use App\Models\EngineCapacity;
use App\Models\SafetyFeature;
use App\Models\TechFeature;
use App\Models\ComfortFeature;
use App\Models\ExteriorFeature;
use App\Models\WarrantyOption;
use App\Models\CarListing;
use App\Models\CarListingImage;
use App\Models\User;

class UsedCarListingController extends Controller
{
    /**
     * Display a listing of car ads.
     */
    public function index(Request $request)
    {
        $query = CarListing::with(['make', 'model', 'emirate', 'user', 'user.roles', 'images'])
            ->where('condition_type', 'used');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $listings = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/UsedCarListing/Index', [
            'listings' => $listings,
            'filters' => $request->only(['user_id'])
        ]);
    }

    /**
     * Show the form for creating a new car ad.
     */
    public function create()
    {
        return Inertia::render('Admin/UsedCarListing/Create', [
            'users' => User::with('roles')->role([User::ROLE_PRIVATE_SELLER, User::ROLE_DEALER])
                ->where('status', true)
                ->where('verification', User::VERIFICATION_APPROVED)
                ->get(['id', 'name', 'email']),
            'emirates' => Emirate::where('status', true)->get(),
            'makes' => CarMake::with('models.trims')->where('status', true)->get(),
            'regionalSpecs' => RegionalSpec::where('status', true)->get(),
            'bodyTypes' => BodyType::where('status', true)->get(),
            'exteriorColors' => ExteriorColor::where('status', true)->get(),
            'interiorColors' => InteriorColor::where('status', true)->get(),
            'chargingTypes' => ChargingType::where('status', true)->get(),
            'doorTypes' => DoorType::where('status', true)->get(),
            'cylinders' => EngineCylinder::where('status', true)->get(),
            'transmissions' => Transmission::where('status', true)->get(),
            'seatingCapacities' => SeatingCapacity::where('status', true)->get(),
            'horsepowers' => Horsepower::where('status', true)->get(),
            'steeringSides' => SteeringSide::where('status', true)->get(),
            'engineCapacities' => EngineCapacity::where('status', true)->get(),
            'safetyFeatures' => SafetyFeature::where('status', true)->get(),
            'techFeatures' => TechFeature::where('status', true)->get(),
            'comfortFeatures' => ComfortFeature::where('status', true)->get(),
            'exteriorFeatures' => ExteriorFeature::where('status', true)->get(),
            'warrantyOptions' => WarrantyOption::where('status', true)->get(),
        ]);
    }

    /**
     * Store a newly created listing in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'emirate_id' => 'required|exists:emirates,id',
            'car_make_id' => 'required|exists:car_makes,id',
            'car_model_id' => 'required|exists:car_models,id',
            'car_trim_id' => 'required|exists:car_trims,id',
            'regional_spec_id' => 'required|exists:regional_specs,id',
            'year' => 'required|integer|min:1900|max:' . (now()->year + 1),
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
            'tech_features' => 'array',
            'comfort_features' => 'array',
            'exterior_features' => 'array',
        ]);

        DB::beginTransaction();

        try {
            // Create Listing
            $user = User::find($request->user_id);

            if ($user->verification !== User::VERIFICATION_APPROVED) {
                return redirect()->back()->with('error', 'The selected user is not verified. Only verified users can have listings.');
            }

            $isVerified = $user->hasRole(User::ROLE_DEALER);

            $listingData = array_merge($validated, [
                'condition_type' => 'used',
                'is_verified' => $isVerified,
                'status' => $isVerified ? 'approved' : 'pending'
            ]);
            $listing = CarListing::create($listingData);

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
            $listing->safetyFeatures()->sync($request->safety_features);
            $listing->techFeatures()->sync($request->tech_features);
            $listing->comfortFeatures()->sync($request->comfort_features);
            $listing->exteriorFeatures()->sync($request->exterior_features);

            DB::commit();

            return redirect()->route('admin.used-car-listings.index')->with('success', 'Car listing created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified car listing.
     */
    public function show(CarListing $usedCarListing)
    {
        // Ensure it's used
        if ($usedCarListing->condition_type !== 'used') {
            abort(404);
        }

        $usedCarListing->load([
            'user',
            'emirate',
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
            'images',
            'safetyFeatures',
            'techFeatures',
            'comfortFeatures',
            'exteriorFeatures'
        ]);

        return Inertia::render('Admin/UsedCarListing/Show', [
            'listing' => $usedCarListing
        ]);
    }

    /**
     * Remove the specified car listing from storage.
     */
    public function destroy(CarListing $carListing)
    {
        // Ensure it's used
        if ($carListing->condition_type !== 'used') {
            abort(404);
        }

        // Delete images from s3
        foreach ($carListing->images as $image) {
            Storage::disk('s3')->delete($image->image);
        }

        $carListing->delete();

        return redirect()->route('admin.used-car-listings.index')->with('success', 'Car listing deleted successfully.');
    }

    /**
     * Verify the specified car listing.
     */
    public function verify(Request $request, CarListing $carListing)
    {
        $carListing->update([
            'is_verified' => $request->is_verified,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Listing status updated.');
    }
}
