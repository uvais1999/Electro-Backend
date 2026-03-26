<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    users: Array,
    emirates: Array,
    makes: Array,
    regionalSpecs: Array,
    bodyTypes: Array,
    exteriorColors: Array,
    interiorColors: Array,
    chargingTypes: Array,
    doorTypes: Array,
    cylinders: Array,
    transmissions: Array,
    seatingCapacities: Array,
    horsepowers: Array,
    steeringSides: Array,
    engineCapacities: Array,
    safetyFeatures: Array,
    techFeatures: Array,
    comfortFeatures: Array,
    exteriorFeatures: Array,
    warrantyOptions: Array,
})

const form = useForm({
    user_id: '',
    emirate_id: '',
    car_make_id: '',
    car_model_id: '',
    car_trim_id: '',
    regional_spec_id: '',
    year: '',
    kilometers: '',
    body_type_id: '',
    is_insured: null,
    price: '',
    phone_number: '',
    images: [],
    title: '',
    description: '',
    exterior_color_id: '',
    interior_color_id: '',
    warranty_option_id: '',
    charging_type_id: '',
    door_type_id: '',
    engine_cylinder_id: '',
    transmission_id: '',
    seating_capacity_id: '',
    horsepower_id: '',
    steering_side_id: '',
    engine_capacity_id: '',
    safety_features: [],
    tech_features: [],
    comfort_features: [],
    exterior_features: [],
    location: '',
    building_street: '',
    latitude: 25.1972,
    longitude: 55.2744,
})

const mapContainer = ref(null)
const locationInput = ref(null)
let map = null
let marker = null
let autocomplete = null

const initMap = () => {
    // If google is not loaded, we load it
    if (typeof google === 'undefined') {
        const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || 'YOUR_API_KEY_HERE'
        const script = document.createElement('script')
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places`
        script.async = true
        script.defer = true
        script.onload = setupMap
        document.head.appendChild(script)
    } else {
        setupMap()
    }
}

const setupMap = () => {
    if (!mapContainer.value) return

    const center = { lat: form.latitude, lng: form.longitude }
    map = new google.maps.Map(mapContainer.value, {
        zoom: 12,
        center: center,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
    })

    marker = new google.maps.Marker({
        position: center,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP
    })

    // Update coordinates on drag end
    marker.addListener('dragend', () => {
        const position = marker.getPosition()
        form.latitude = position.lat()
        form.longitude = position.lng()
        reverseGeocode(position.lat(), position.lng())
    })

    // Update marker on map click
    map.addListener('click', (e) => {
        marker.setPosition(e.latLng)
        form.latitude = e.latLng.lat()
        form.longitude = e.latLng.lng()
        reverseGeocode(e.latLng.lat(), e.latLng.lng())
    })

    // Autocomplete for location input
    if (locationInput.value) {
        autocomplete = new google.maps.places.Autocomplete(locationInput.value, {
            fields: ['geometry', 'formatted_address'],
            componentRestrictions: { country: 'ae' }
        })

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace()
            if (!place.geometry || !place.geometry.location) return

            const pos = place.geometry.location
            map.setCenter(pos)
            marker.setPosition(pos)
            form.latitude = pos.lat()
            form.longitude = pos.lng()
            form.location = place.formatted_address
        })
    }
}

const reverseGeocode = (lat, lng) => {
    const geocoder = new google.maps.Geocoder()
    geocoder.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === 'OK' && results[0]) {
            form.location = results[0].formatted_address
        }
    })
}

onMounted(() => {
    initMap()
})

// Computed for dependent dropdowns
const selectedMake = computed(() => props.makes.find(m => m.id === form.car_make_id))
const availableModels = computed(() => selectedMake.value ? selectedMake.value.models : [])
const selectedModel = computed(() => availableModels.value.find(m => m.id === form.car_model_id))
const availableTrims = computed(() => selectedModel.value ? selectedModel.value.trims : [])

// Years list (current year back to 1950)
const years = Array.from({ length: new Date().getFullYear() - 1950 + 1 }, (_, i) => new Date().getFullYear() - i)

const submit = () => {
    form.post(route('admin.new-car-listings.store'), {
        onSuccess: () => {
            // success handling
        }
    })
}

const toggleFeature = (category, featureId) => {
    const key = `${category}_features`
    const index = form[key].indexOf(featureId)
    if (index > -1) {
        form[key].splice(index, 1)
    } else {
        form[key].push(featureId)
    }
}

const fileInput = ref(null)
const previews = ref([])

const triggerFileInput = () => fileInput.value.click()

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files)
    form.images = [...form.images, ...files]

    files.forEach(file => {
        const reader = new FileReader()
        reader.onload = (e) => previews.value.push(e.target.result)
        reader.readAsDataURL(file)
    })
}

const removeImage = (index) => {
    form.images.splice(index, 1)
    previews.value.splice(index, 1)
}
</script>

<template>
    <AdminLayout>

        <Head title="Create New Car Listing" />

        <div class="max-w-4xl mx-auto space-y-8 pb-20">
            <header class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Post Your Car Ad</h1>
                    <p class="text-gray-500 text-sm mt-1 uppercase tracking-widest font-black text-[10px]">Standard
                        Listing Form</p>
                </div>
                <button type="submit" form="listingForm"
                    class="bg-indigo-600 text-white px-8 py-2.5 rounded-lg hover:bg-indigo-700 transition font-bold shadow-lg shadow-indigo-100">
                    Submit Ad
                </button>
            </header>

            <form id="listingForm" @submit.prevent="submit" class="space-y-8">

                <!-- Section 0: User Selection -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Owner
                        Assignment</h3>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Select User (Ad
                            Owner) *</label>
                        <select v-model="form.user_id"
                            class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Select User</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">
                                {{ u.name }} ({{ u.role_name || 'No Role' }})
                            </option>
                        </select>
                        <p v-if="form.errors.user_id" class="text-xs text-rose-500 mt-1">{{ form.errors.user_id }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">Assign this vehicle advertisement to a registered
                            user.</p>
                    </div>
                </div>

                <!-- Section 1: Core Details -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Core
                        Specifications</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Emirate -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Emirate
                                *</label>
                            <select v-model="form.emirate_id"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">Select Emirate</option>
                                <option v-for="e in emirates" :key="e.id" :value="e.id">{{ e.name }}</option>
                            </select>
                            <p v-if="form.errors.emirate_id" class="text-xs text-rose-500 mt-1">{{ form.errors.emirate_id }}</p>
                        </div>

                        <!-- Make -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Make *</label>
                            <select v-model="form.car_make_id" @change="form.car_model_id = ''; form.car_trim_id = ''"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">Select Make</option>
                                <option v-for="m in makes" :key="m.id" :value="m.id">{{ m.name }}</option>
                            </select>
                            <p v-if="form.errors.car_make_id" class="text-xs text-rose-500 mt-1">{{ form.errors.car_make_id }}</p>
                        </div>

                        <!-- Model -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Model *</label>
                            <select v-model="form.car_model_id" @change="form.car_trim_id = ''"
                                :disabled="!form.car_make_id"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition disabled:bg-gray-50">
                                <option value="">Select Model</option>
                                <option v-for="m in availableModels" :key="m.id" :value="m.id">{{ m.name }}</option>
                            </select>
                            <p v-if="form.errors.car_model_id" class="text-xs text-rose-500 mt-1">{{ form.errors.car_model_id }}</p>
                        </div>

                        <!-- Trim -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Trim *</label>
                            <select v-model="form.car_trim_id" :disabled="!form.car_model_id"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition disabled:bg-gray-50">
                                <option value="">Select Trim</option>
                                <option v-for="t in availableTrims" :key="t.id" :value="t.id">{{ t.name }}</option>
                            </select>
                            <p v-if="form.errors.car_trim_id" class="text-xs text-rose-500 mt-1">{{ form.errors.car_trim_id }}</p>
                        </div>

                        <!-- Regional Specs -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Regional Specs
                                *</label>
                            <select v-model="form.regional_spec_id"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">Select Specs</option>
                                <option v-for="s in regionalSpecs" :key="s.id" :value="s.id">{{ s.name }}</option>
                            </select>
                            <p v-if="form.errors.regional_spec_id" class="text-xs text-rose-500 mt-1">{{ form.errors.regional_spec_id }}</p>
                        </div>

                        <!-- Year -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Year *</label>
                            <select v-model="form.year"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">Select Year</option>
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                            <p v-if="form.errors.year" class="text-xs text-rose-500 mt-1">{{ form.errors.year }}</p>
                        </div>

                        <!-- Kilometers -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Kilometers
                                *</label>
                            <div class="relative">
                                <input type="number" v-model="form.kilometers"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="e.g. 15000">
                                <span
                                    class="absolute right-4 top-2.5 text-xs text-gray-400 font-bold uppercase">km</span>
                            </div>
                            <p v-if="form.errors.kilometers" class="text-xs text-rose-500 mt-1">{{ form.errors.kilometers }}</p>
                        </div>

                        <!-- Body Type -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Body Type
                                *</label>
                            <select v-model="form.body_type_id"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">Select Body Type</option>
                                <option v-for="b in bodyTypes" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                            <p v-if="form.errors.body_type_id" class="text-xs text-rose-500 mt-1">{{ form.errors.body_type_id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pricing & Insurance -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Value &
                        Contact</h3>

                    <div class="space-y-6">
                        <!-- Insurance -->
                        <div class="space-y-3">
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Is your car insured
                                in UAE? *</p>
                            <div class="flex space-x-3">
                                <button type="button" @click="form.is_insured = true"
                                    :class="[form.is_insured === true ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-6 py-2 rounded-lg border font-bold text-sm transition transition-all shadow-sm">Yes</button>
                                <button type="button" @click="form.is_insured = false"
                                    :class="[form.is_insured === false ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-6 py-2 rounded-lg border font-bold text-sm transition transition-all shadow-sm">No</button>
                            </div>
                            <p v-if="form.errors.is_insured" class="text-xs text-rose-500 mt-1">{{ form.errors.is_insured }}</p>
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Price -->
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Price
                                    *</label>
                                <div class="relative">
                                    <input type="number" v-model="form.price"
                                        class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        placeholder="e.g. 50000">
                                    <span
                                        class="absolute right-4 top-2.5 text-xs text-gray-400 font-bold uppercase">AED</span>
                                </div>
                                <p v-if="form.errors.price" class="text-xs text-rose-500 mt-1">{{ form.errors.price }}</p>
                            </div>
                            <!-- Phone -->
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Phone Number
                                    *</label>
                                <input type="text" v-model="form.phone_number"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="e.g. 050 123 4567">
                                <p v-if="form.errors.phone_number" class="text-xs text-rose-500 mt-1">{{ form.errors.phone_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Media & Description -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Media &
                        Narrative</h3>

                    <div class="space-y-6">
                        <!-- Images -->
                        <div class="space-y-4">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Listing Pictures
                                *</label>

                            <input type="file" ref="fileInput" multiple @change="handleImageUpload" class="hidden"
                                accept="image/*">

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Preview Cards -->
                                <div v-for="(preview, index) in previews" :key="index"
                                    class="relative group aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                                    <img :src="preview" class="w-full h-full object-cover">
                                    <button @click="removeImage(index)" type="button"
                                        class="absolute top-2 right-2 p-1 bg-white/90 text-rose-500 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <div v-if="index === 0"
                                        class="absolute bottom-0 inset-x-0 bg-indigo-600/90 text-[8px] text-white font-black uppercase tracking-widest py-1 text-center">
                                        Main Image</div>
                                </div>

                                <!-- Upload Button -->
                                <button type="button" @click="triggerFileInput"
                                    class="aspect-square border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center space-y-2 hover:border-indigo-400 transition bg-gray-50/50">
                                    <div class="bg-white p-2 rounded-full shadow-xs">
                                        <svg class="w-6 h-6 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 9a3 3 0 100 6 3 3 0 000-6z" />
                                            <path fill-rule="evenodd"
                                                d="M10.134 4c.316.035.539.117.75.304l.972.864c.22.196.347.308.528.375.18.067.318.083.585.083h2.062c.267 0 .405-.016.585-.083.181-.067.308-.18.528-.375l.972-.864c.211-.187.434-.269.75-.304l.327-.036A3.49 3.49 0 0122 7.75v10.5A3.75 3.75 0 0118.25 22H5.75A3.75 3.75 0 012 18.25V7.75A3.49 3.49 0 015.007 4.29l.327.036c.316.035.539.117.75.304l.972.864c.22.196.347.308.528.375.18.067.318.083.585.083H10.2c.267 0 .405-.016.585-.083.181-.067.308-.18.528-.375l.972-.864c.211-.187.434-.269.75-.304z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="text-[10px] text-rose-500 font-black uppercase tracking-widest">Add
                                        Images</span>
                                </button>
                            </div>
                            <p v-if="form.errors.images" class="text-xs text-rose-500 mt-1">{{ form.errors.images }}</p>
                        </div>

                        <!-- Title -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Title *</label>
                            <input type="text" v-model="form.title"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition"
                                placeholder="e.g. 2021 Toyota Camry SE Special Edition">
                            <p v-if="form.errors.title" class="text-xs text-rose-500 mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Describe your
                                car *</label>
                            <div class="relative">
                                <textarea v-model="form.description" rows="6"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"
                                    placeholder="Enter details about history, condition, and modifications..."></textarea>
                                <span class="absolute right-4 bottom-4 text-[10px] font-bold text-gray-400">{{
                                    form.description.length }}/16000</span>
                            </div>
                            <p v-if="form.errors.description" class="text-xs text-rose-500 mt-1">{{ form.errors.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Technical & Mechanical -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-8">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Technical
                        Profile</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Colors -->
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Exterior
                                    Color *</label>
                                <select v-model="form.exterior_color_id"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="">Select Color</option>
                                    <option v-for="c in exteriorColors" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                                <p v-if="form.errors.exterior_color_id" class="text-xs text-rose-500 mt-1">{{ form.errors.exterior_color_id }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Interior
                                    Color *</label>
                                <select v-model="form.interior_color_id"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="">Select Color</option>
                                    <option v-for="c in interiorColors" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                                <p v-if="form.errors.interior_color_id" class="text-xs text-rose-500 mt-1">{{ form.errors.interior_color_id }}</p>
                            </div>
                        </div>

                        <!-- Engine Stuff -->
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">No. of
                                    Cylinders *</label>
                                <select v-model="form.engine_cylinder_id"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="">Select Cylinders</option>
                                    <option v-for="c in cylinders" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                                <p v-if="form.errors.engine_cylinder_id" class="text-xs text-rose-500 mt-1">{{ form.errors.engine_cylinder_id }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Engine
                                    Capacity (cc)</label>
                                <select v-model="form.engine_capacity_id"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="">Select CC</option>
                                    <option v-for="c in engineCapacities" :key="c.id" :value="c.id">{{ c.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.engine_capacity_id" class="text-xs text-rose-500 mt-1">{{ form.errors.engine_capacity_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Toggles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-6">
                        <!-- Warranty -->
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Warranty
                                *</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" v-for="w in warrantyOptions" :key="w.id"
                                    @click="form.warranty_option_id = w.id"
                                    :class="[form.warranty_option_id === w.id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-5 py-2 rounded-lg border font-bold text-xs transition transition-all shadow-sm">
                                    {{ w.name }}
                                </button>
                            </div>
                            <p v-if="form.errors.warranty_option_id" class="text-xs text-rose-500 mt-1">{{ form.errors.warranty_option_id }}</p>
                        </div>

                        <!-- Charging Type -->
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Charging Type
                                *</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" v-for="f in chargingTypes" :key="f.id"
                                    @click="form.charging_type_id = f.id"
                                    :class="[form.charging_type_id === f.id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-5 py-2 rounded-lg border font-bold text-xs transition transition-all shadow-sm">
                                    {{ f.name }}
                                </button>
                            </div>
                            <p v-if="form.errors.charging_type_id" class="text-xs text-rose-500 mt-1">{{ form.errors.charging_type_id }}</p>
                        </div>

                        <!-- Doors -->
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Doors *</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" v-for="d in doorTypes" :key="d.id"
                                    @click="form.door_type_id = d.id"
                                    :class="[form.door_type_id === d.id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-5 py-2 rounded-lg border font-bold text-xs transition transition-all shadow-sm">
                                    {{ d.name }}
                                </button>
                            </div>
                            <p v-if="form.errors.door_type_id" class="text-xs text-rose-500 mt-1">{{ form.errors.door_type_id }}</p>
                        </div>

                        <!-- Transmission -->
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Transmission
                                Type *</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" v-for="t in transmissions" :key="t.id"
                                    @click="form.transmission_id = t.id"
                                    :class="[form.transmission_id === t.id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-5 py-2 rounded-lg border font-bold text-xs transition transition-all shadow-sm">
                                    {{ t.name }}
                                </button>
                            </div>
                            <p v-if="form.errors.transmission_id" class="text-xs text-rose-500 mt-1">{{ form.errors.transmission_id }}</p>
                        </div>

                        <!-- Steering -->
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Steering Side
                                *</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" v-for="s in steeringSides" :key="s.id"
                                    @click="form.steering_side_id = s.id"
                                    :class="[form.steering_side_id === s.id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-500']"
                                    class="px-5 py-2 rounded-lg border font-bold text-xs transition transition-all shadow-sm">
                                    {{ s.name }}
                                </button>
                            </div>
                            <p v-if="form.errors.steering_side_id" class="text-xs text-rose-500 mt-1">{{ form.errors.steering_side_id }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Seating -->
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Seating
                                    Capacity</label>
                                <select v-model="form.seating_capacity_id"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="">Select Capacity</option>
                                    <option v-for="s in seatingCapacities" :key="s.id" :value="s.id">{{ s.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.seating_capacity_id" class="text-xs text-rose-500 mt-1">{{ form.errors.seating_capacity_id }}</p>
                            </div>
                            <!-- HP -->
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Horsepower
                                    *</label>
                                <select v-model="form.horsepower_id"
                                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="">Select Power</option>
                                    <option v-for="h in horsepowers" :key="h.id" :value="h.id">{{ h.name }}</option>
                                </select>
                                <p v-if="form.errors.horsepower_id" class="text-xs text-rose-500 mt-1">{{ form.errors.horsepower_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Dynamic Features -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-10">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Features &
                        Amenities</h3>

                    <!-- Safety -->
                    <div class="space-y-4">
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider flex items-center">
                            <span class="w-2 h-2 bg-rose-500 rounded-full mr-2"></span>
                            Driver Assistance & Safety
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" v-for="f in safetyFeatures" :key="f.id"
                                @click="toggleFeature('safety', f.id)"
                                :class="[form.safety_features.includes(f.id) ? 'bg-indigo-50 border-indigo-600 text-indigo-700' : 'bg-gray-50 border-transparent text-gray-600 hover:bg-gray-100']"
                                class="px-4 py-2 rounded-full border text-xs font-bold transition flex items-center">
                                {{ f.name }}
                                <svg v-if="form.safety_features.includes(f.id)" class="w-4 h-4 ml-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="w-4 h-4 ml-2 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Tech -->
                    <div class="space-y-4">
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider flex items-center">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                            Entertainment & Technology
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" v-for="f in techFeatures" :key="f.id"
                                @click="toggleFeature('tech', f.id)"
                                :class="[form.tech_features.includes(f.id) ? 'bg-indigo-50 border-indigo-600 text-indigo-700' : 'bg-gray-50 border-transparent text-gray-600 hover:bg-gray-100']"
                                class="px-4 py-2 rounded-full border text-xs font-bold transition flex items-center">
                                {{ f.name }}
                                <svg v-if="form.tech_features.includes(f.id)" class="w-4 h-4 ml-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="w-4 h-4 ml-2 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Comfort -->
                    <div class="space-y-4">
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider flex items-center">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Comfort & Convenience
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" v-for="f in comfortFeatures" :key="f.id"
                                @click="toggleFeature('comfort', f.id)"
                                :class="[form.comfort_features.includes(f.id) ? 'bg-indigo-50 border-indigo-600 text-indigo-700' : 'bg-gray-50 border-transparent text-gray-600 hover:bg-gray-100']"
                                class="px-4 py-2 rounded-full border text-xs font-bold transition flex items-center">
                                {{ f.name }}
                                <svg v-if="form.comfort_features.includes(f.id)" class="w-4 h-4 ml-2"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="w-4 h-4 ml-2 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Exterior -->
                    <div class="space-y-4">
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider flex items-center">
                            <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                            Exterior
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" v-for="f in exteriorFeatures" :key="f.id"
                                @click="toggleFeature('exterior', f.id)"
                                :class="[form.exterior_features.includes(f.id) ? 'bg-indigo-50 border-indigo-600 text-indigo-700' : 'bg-gray-50 border-transparent text-gray-600 hover:bg-gray-100']"
                                class="px-4 py-2 rounded-full border text-xs font-bold transition flex items-center">
                                {{ f.name }}
                                <svg v-if="form.exterior_features.includes(f.id)" class="w-4 h-4 ml-2"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="w-4 h-4 ml-2 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Location -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-900 border-b pb-4">Select
                        Location</h3>

                    <div class="space-y-4">
                        <div class="relative">
                            <input type="text" v-model="form.location" ref="locationInput"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition h-14 px-6"
                                placeholder="Locate your item">
                            <span
                                class="absolute right-6 top-4.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Optional</span>
                        </div>
                        <div class="relative">
                            <input type="text" v-model="form.building_street"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition h-14 px-6"
                                placeholder="Building or Street name">
                            <span
                                class="absolute right-6 top-4.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Optional</span>
                        </div>

                        <div class="space-y-4 pt-4">
                            <h4 class="text-sm font-bold text-gray-900">Is the pin in the right location?</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">Click and drag the pin to the exact spot.
                                Users are more likely to respond to ads that are correctly shown on the map.</p>

                            <!-- Google Map -->
                            <div class="aspect-video bg-gray-100 rounded-2xl border border-gray-200 overflow-hidden relative shadow-inner">
                                <div ref="mapContainer" class="w-full h-full"></div>
                                <div class="absolute bottom-4 left-4 flex space-x-2 pointer-events-none">
                                    <div class="bg-white/80 backdrop-blur-sm px-2 py-1 rounded shadow-sm text-[8px] font-bold uppercase tracking-tighter text-gray-500 border border-gray-100 italic">
                                        Google Maps API Integrated
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-6">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-bold text-gray-900">Choose how you want to label your
                                    location</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">(Optional)</span>
                            </div>
                            <button type="button"
                                class="border border-gray-300 rounded-lg px-6 py-2.5 font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm text-sm">Add
                                Custom Label</button>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    <button type="button" class="text-gray-500 font-bold px-6 py-2">Discard Changes</button>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-10 py-3 rounded-xl hover:bg-indigo-700 transition font-black uppercase tracking-widest text-[10px] shadow-xl shadow-indigo-100">
                        Create Car Listing
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 1rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>
