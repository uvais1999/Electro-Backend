<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    listing: Object
})

const activeImage = ref(props.listing.images.find(img => img.is_main)?.image_url || props.listing.images[0]?.image_url)

const setActiveImage = (url) => {
    activeImage.value = url
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <AdminLayout>
        <Head :title="listing.title" />

        <div class="space-y-6 max-w-7xl mx-auto pb-12">
            <!-- Header -->
            <header class="flex flex-col md:flex-row md:items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <Link :href="route('admin.car-listings.index')" class="text-gray-400 hover:text-indigo-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <div v-if="listing.condition_type" :class="[listing.condition_type === 'new' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600']" 
                             class="inline-block px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest mb-1.5 border border-current opacity-70">
                            {{ listing.condition_type }} Car
                        </div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ listing.title }}</h1>
                    </div>
                    <div class="flex items-center space-x-3 text-sm text-gray-500 font-medium">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ listing.emirate?.name }}
                        </span>
                        <span>•</span>
                        <span>Posted on {{ formatDate(listing.created_at) }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest leading-none mb-1">Price</p>
                        <p class="text-3xl font-black text-indigo-600 tracking-tighter">AED {{ Number(listing.price).toLocaleString() }}</p>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Media Gallery -->
                    <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                        <div class="aspect-[16/9] rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 relative group">
                            <img :src="activeImage" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-md text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                                {{ listing.images.length }} Photos
                            </div>
                        </div>
                        <div class="flex space-x-3 overflow-x-auto pb-2 scrollbar-hide">
                            <button v-for="img in listing.images" :key="img.id" @click="setActiveImage(img.image_url)"
                                :class="[activeImage === img.image_url ? 'ring-2 ring-indigo-600' : 'opacity-70 hover:opacity-100']"
                                class="w-24 h-16 rounded-xl overflow-hidden flex-shrink-0 transition-all active:scale-95 border border-gray-100">
                                <img :src="img.image_url" class="w-full h-full object-cover">
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                        <h3 class="text-lg font-black text-gray-900 border-l-4 border-indigo-600 pl-4 uppercase tracking-tight">Vehicle Description</h3>
                        <p class="text-gray-600 leading-relaxed text-sm whitespace-pre-line">{{ listing.description }}</p>
                    </div>

                    <!-- Features -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-8">
                        <h3 class="text-lg font-black text-gray-900 border-l-4 border-indigo-600 pl-4 uppercase tracking-tight">Features & Amenities</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <!-- Safety -->
                            <div v-if="listing.safety_features?.length > 0" class="space-y-4">
                                <h4 class="text-[10px] uppercase font-black text-indigo-400 tracking-widest flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Safety & Security
                                </h4>
                                <ul class="grid grid-cols-1 gap-2">
                                    <li v-for="f in listing.safety_features" :key="f.id" class="flex items-center text-sm text-gray-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-200 mr-2"></span>
                                        {{ f.name }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Technology -->
                            <div v-if="listing.tech_features?.length > 0" class="space-y-4">
                                <h4 class="text-[10px] uppercase font-black text-indigo-400 tracking-widest flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Technology
                                </h4>
                                <ul class="grid grid-cols-1 gap-2">
                                    <li v-for="f in listing.tech_features" :key="f.id" class="flex items-center text-sm text-gray-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-200 mr-2"></span>
                                        {{ f.name }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Comfort -->
                            <div v-if="listing.comfort_features?.length > 0" class="space-y-4">
                                <h4 class="text-[10px] uppercase font-black text-indigo-400 tracking-widest flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Comfort & Convenience
                                </h4>
                                <ul class="grid grid-cols-1 gap-2">
                                    <li v-for="f in listing.comfort_features" :key="f.id" class="flex items-center text-sm text-gray-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-200 mr-2"></span>
                                        {{ f.name }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Exterior -->
                            <div v-if="listing.exterior_features?.length > 0" class="space-y-4">
                                <h4 class="text-[10px] uppercase font-black text-indigo-400 tracking-widest flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    Exterior Features
                                </h4>
                                <ul class="grid grid-cols-1 gap-2">
                                    <li v-for="f in listing.exterior_features" :key="f.id" class="flex items-center text-sm text-gray-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-200 mr-2"></span>
                                        {{ f.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <!-- Owner Info -->
                    <div class="bg-indigo-600 p-6 rounded-3xl shadow-lg ring-4 ring-indigo-50">
                        <h4 class="text-[10px] uppercase font-black text-indigo-200 tracking-widest mb-4">Posted By</h4>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20">
                                <span class="text-white font-black text-lg">{{ listing.user?.name.charAt(0) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-bold">{{ listing.user?.name }}</p>
                                <p class="text-indigo-200 text-[10px] uppercase font-bold tracking-widest mt-0.5">{{ listing.seller_type }}</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-2">
                            <a :href="'tel:' + listing.phone_number" class="w-full flex items-center justify-center bg-white text-indigo-600 font-black py-3 rounded-xl hover:bg-gray-50 transition active:scale-95 shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ listing.phone_number }}
                            </a>
                        </div>
                    </div>

                    <!-- Core Specs -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                        <h4 class="text-[10px] uppercase font-black text-gray-400 tracking-widest border-l-4 border-indigo-600 pl-4">Specifications</h4>
                        <div class="space-y-4">
                            <div v-for="spec in [
                                { label: 'Make', value: listing.make?.name },
                                { label: 'Model', value: listing.model?.name },
                                { label: 'Trim', value: listing.trim?.name },
                                { label: 'Year', value: listing.year },
                                { label: 'Kilometers', value: Number(listing.kilometers).toLocaleString() + ' km' },
                                { label: 'Body Type', value: listing.body_type?.name },
                                { label: 'Charging Type', value: listing.charging_type?.name },
                                { label: 'Transmission', value: listing.transmission?.name },
                                { label: 'Regional Spec', value: listing.regional_spec?.name },
                                { label: 'Exterior Color', value: listing.exterior_color?.name },
                                { label: 'Interior Color', value: listing.interior_color?.name },
                                { label: 'Doors', value: listing.door_type?.name },
                                { label: 'Cylinders', value: listing.engine_cylinder?.name },
                                { label: 'Horsepower', value: listing.horsepower?.name },
                                { label: 'Capacity (cc)', value: listing.engine_capacity?.name },
                                { label: 'Steering', value: listing.steering_side?.name },
                                { label: 'Seating', value: listing.seating_capacity?.name },
                                { label: 'Warranty', value: listing.warranty_option?.name },
                                { label: 'Insured', value: listing.is_insured ? 'Yes' : 'No' }
                            ]" :key="spec.label" class="flex justify-between items-center text-sm py-1 border-b border-gray-50 last:border-0">
                                <span class="text-gray-400 font-medium">{{ spec.label }}</span>
                                <span class="text-gray-900 font-bold tracking-tight">{{ spec.value || 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                        <h4 class="text-[10px] uppercase font-black text-gray-400 tracking-widest border-l-4 border-indigo-600 pl-4">Location</h4>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-900">{{ listing.building_street }}</p>
                            <p class="text-[10px] text-gray-500 font-medium">{{ listing.location }}</p>
                            <div class="flex items-center space-x-3 mt-2">
                                <div class="bg-indigo-50 px-2 py-0.5 rounded text-[8px] font-black text-indigo-600 uppercase tracking-tighter border border-indigo-100 italic">
                                    LAT: {{ listing.latitude }}
                                </div>
                                <div class="bg-indigo-50 px-2 py-0.5 rounded text-[8px] font-black text-indigo-600 uppercase tracking-tighter border border-indigo-100 italic">
                                    LNG: {{ listing.longitude }}
                                </div>
                            </div>
                        </div>
                        <div class="aspect-square bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center p-8 text-center space-y-2 opacity-60 grayscale">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">MAP INTEGRATION PENDING</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
