<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    listings: Object,
    conditionType: String
})

const getMainImage = (listing) => {
    if (!listing.images || listing.images.length === 0) return null
    const main = listing.images.find(img => img.is_main)
    return main ? main.image_url : listing.images[0].image_url
}
</script>

<template>
    <AdminLayout>

        <Head title="Car Listings" />

        <div class="space-y-6">
            <header class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Car Listings</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage and moderate vehicle advertisements.</p>
                </div>
                <Link :href="conditionType === 'new' ? route('admin.new-car-listings.create') : route('admin.used-car-listings.create')"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Create {{ conditionType === 'new' ? 'New' : (conditionType === 'used' ? 'Used' : '') }} Car Listing
                </Link>
            </header>

            <div v-if="listings.total > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 w-24 text-center">SL NO</th>
                            <th class="px-6 py-4">Car Details</th>
                            <th class="px-6 py-4 text-center">Price</th>
                            <th class="px-6 py-4 text-center">Owner</th>
                            <th class="px-6 py-4 text-center">Date</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(listing, index) in listings.data" :key="listing.id"
                            class="hover:bg-gray-50/50 transition duration-200">
                            <!-- SL NO -->
                            <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">
                                #{{ (listings.current_page - 1) * listings.per_page + index + 1 }}
                            </td>

                            <!-- Car Details -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-20 h-14 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden shadow-sm flex-shrink-0">
                                        <img v-if="getMainImage(listing)" :src="getMainImage(listing)" class="w-full h-full object-cover">
                                        <div v-else class="w-full h-full flex items-center justify-center text-[8px] font-black text-gray-300 uppercase">No Image</div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 truncate text-sm">{{ listing.title }}</p>
                                        <div class="flex items-center space-x-2 text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">
                                            <span>{{ listing.make?.name }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span>{{ listing.model?.name }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-indigo-600">{{ listing.year }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span :class="[listing.condition_type === 'new' ? 'text-rose-600' : 'text-emerald-600']">{{ listing.condition_type }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4 text-center">
                                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded text-xs font-black">
                                    AED {{ Number(listing.price).toLocaleString() }}
                                </span>
                            </td>

                            <!-- Owner -->
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-700">
                                <div>{{ listing.user?.name }}</div>
                                <div class="text-[9px] uppercase font-black text-gray-400 tracking-widest mt-0.5">{{ listing.seller_type || 'Buyer' }}</div>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-center text-xs text-gray-500 font-medium">
                                {{ new Date(listing.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <Link :href="route('admin.car-listings.show', listing.id)"
                                    class="text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-lg font-black uppercase text-[10px] tracking-widest hover:bg-indigo-100 transition inline-block">
                                    View Details
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white p-16 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center space-y-4">
                <div class="bg-indigo-50 p-6 rounded-full ring-8 ring-indigo-50/50">
                    <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-gray-900 font-bold text-lg">No Listings Found</p>
                    <p class="text-gray-500 text-sm">Automotive advertisements will appear here.</p>
                </div>
                <Link :href="conditionType === 'new' ? route('admin.new-car-listings.create') : route('admin.used-car-listings.create')" class="bg-indigo-600 text-white px-8 py-2.5 rounded-lg shadow-lg shadow-indigo-100 font-bold transition hover:bg-indigo-700">
                    Post My First {{ conditionType === 'new' ? 'New' : (conditionType === 'used' ? 'Used' : '') }} Ad
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
