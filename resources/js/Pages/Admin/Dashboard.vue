<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    auth: Object,
})

const stats = [
    { name: 'Total Dealers', value: '1,200', change: '+12%', color: 'blue' },
    { name: 'Private Sellers', value: '3,450', change: '+5%', color: 'green' },
    { name: 'Active Buyers', value: '15,670', change: '+25%', color: 'indigo' },
]
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="space-y-8">
            <header>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-500 mt-2">Welcome back! Here's a quick look at how the platform is performing.</p>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="stat in stats" :key="stat.name" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-gray-500 font-medium">{{ stat.name }}</span>
                        <span class="text-sm font-bold" :class="[stat.change.startsWith('+') ? 'text-green-500' : 'text-red-500']">{{ stat.change }}</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ stat.value }}</div>
                    <div class="mt-4 flex items-center">
                        <div class="h-2 flex-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 rounded-full" :style="{ width: '60%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Placeholder -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 font-display">System Activity</h3>
                <div class="space-y-6">
                    <div v-for="i in 3" :key="i" class="flex items-start space-x-4">
                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-800">New dealer registration approved</div>
                            <div class="text-xs text-gray-500 mt-1">2 hours ago • Automated System</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
