<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
    isOpen: Boolean
})

const emit = defineEmits(['toggle'])

const openSubmenu = ref(null)
const page = usePage()

const toggleSubmenu = (menuName) => {
    if (openSubmenu.value === menuName) {
        openSubmenu.value = null
    } else {
        openSubmenu.value = menuName
        if (!props.isOpen) emit('toggle')
    }
}

onMounted(() => {
    const currentPath = page.url
    menuItems.forEach(item => {
        if (item.subItems) {
            const isActive = item.subItems.some(sub => sub.href === currentPath)
            if (isActive) {
                openSubmenu.value = item.name
            }
        }
    })
})

const menuItems = [
    {
        name: 'Dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        href: '/admin/dashboard'
    },
    {
        name: 'Used Car Listings',
        icon: 'M11 5.882V19.297A2.453 2.453 0 019.209 21H4.5a2.5 2.5 0 010-5h2V8H5a2.5 2.5 0 010-5h1.5A2.453 2.453 0 019 5.882h2z',
        href: '/admin/used-car-listings'
    },
    {
        name: 'New Car Listings',
        icon: 'M11 5.882V19.297A2.453 2.453 0 019.209 21H4.5a2.5 2.5 0 010-5h2V8H5a2.5 2.5 0 010-5h1.5A2.453 2.453 0 019 5.882h2z',
        href: '/admin/new-car-listings'
    },
    {
        name: 'Car Listing Specs',
        icon: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
        subItems: [
            { name: 'Emirates', href: '/admin/car-listings/emirate' },
            { name: 'Make', href: '/admin/car-listings/make' },
            { name: 'Model', href: '/admin/car-listings/model' },
            { name: 'Trim', href: '/admin/car-listings/trim' },
            { name: 'Regional specifications', href: '/admin/car-listings/regional-spec' },
            { name: 'Body Types', href: '/admin/car-listings/body-type' },
            { name: 'Exterior Color', href: '/admin/car-listings/exterior-color' },
            { name: 'Interior Color', href: '/admin/car-listings/interior-color' },
            { name: 'Warranties', href: '/admin/car-listings/warranty-option' },
            { name: 'Charging Types', href: '/admin/car-listings/charging-type' },
            { name: 'Doors', href: '/admin/car-listings/door-type' },
            { name: 'Cylinders', href: '/admin/car-listings/engine-cylinder' },
            { name: 'Transmission', href: '/admin/car-listings/transmission' },
            { name: 'Seating Capacity', href: '/admin/car-listings/seating-capacity' },
            { name: 'Horsepower', href: '/admin/car-listings/horsepower' },
            { name: 'Steering Side', href: '/admin/car-listings/steering-side' },
            { name: 'Engine Capacity', href: '/admin/car-listings/engine-capacity' },
            { name: 'Safety Features', href: '/admin/car-listings/safety-feature' },
            { name: 'Tech Features', href: '/admin/car-listings/tech-feature' },
            { name: 'Comfort Features', href: '/admin/car-listings/comfort-feature' },
            { name: 'Exterior Features', href: '/admin/car-listings/exterior-feature' },
        ]
    },
    { name: 'Dealers', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', href: '/admin/dealers' },
    { name: 'Private Sellers', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', href: '/admin/private-sellers' },
    { name: 'Buyers', icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', href: '/admin/buyers' },
]
</script>

<template>
    <aside :class="[isOpen ? 'w-64' : 'w-20']"
        class="bg-indigo-900 transition-all duration-300 ease-in-out fixed h-full z-20 flex flex-col overflow-hidden">
        <div class="p-6 flex items-center justify-between flex-shrink-0">
            <span v-if="isOpen" class="text-white font-bold text-xl tracking-wider">ELECTRO</span>
            <button @click="emit('toggle')" class="text-indigo-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <nav class="mt-8 px-4 space-y-1 flex-1 overflow-y-auto pb-8 scrollbar-hide">
            <template v-for="item in menuItems" :key="item.name">
                <!-- Regular Link -->
                <Link v-if="!item.subItems" :href="item.href"
                    :class="[$page.url.startsWith(item.href) ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white']"
                    class="flex items-center p-3 rounded-lg group transition-all duration-200">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span v-if="isOpen" class="ml-4 font-medium">{{ item.name }}</span>
                </Link>

                <!-- Submenu Item -->
                <div v-else class="space-y-1">
                    <button @click="toggleSubmenu(item.name)"
                        :class="[openSubmenu === item.name || $page.url.startsWith(item.href) ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white']"
                        class="w-full flex items-center p-3 rounded-lg group transition-all duration-200 focus:outline-none">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <div v-if="isOpen" class="ml-4 flex-1 flex items-center justify-between">
                            <span class="font-medium">{{ item.name }}</span>
                            <svg :class="[openSubmenu === item.name ? 'rotate-180' : '']"
                                class="w-4 h-4 transform transition-transform duration-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Sub-items links -->
                    <div v-if="openSubmenu === item.name && isOpen"
                        class="ml-10 space-y-1 overflow-hidden transition-all duration-300">
                        <Link v-for="sub in item.subItems" :key="sub.name" :href="sub.href"
                            :class="[$page.url === sub.href ? 'text-white' : 'text-indigo-300 hover:text-white']"
                            class="block py-2 text-sm font-medium transition-colors">
                            {{ sub.name }}
                        </Link>
                    </div>
                </div>
            </template>
        </nav>
    </aside>
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
