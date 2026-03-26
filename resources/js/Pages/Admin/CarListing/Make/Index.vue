<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import CreateMake from './Create.vue'
import EditMake from './Edit.vue'

const props = defineProps({
    makes: Object
})

const showingCreateModal = ref(false)
const showingEditModal = ref(false)
const selectedMake = ref(null)

const openCreateModal = () => showingCreateModal.value = true
const openEditModal = (make) => {
    selectedMake.value = make
    showingEditModal.value = true
}

const closeEditModal = () => {
    showingEditModal.value = false
    selectedMake.value = null
}

const deleteMake = (make) => {
    if (confirm(`Are you sure you want to delete ${make.name}?`)) {
        router.delete(route('admin.car-listings.make.destroy', make.id))
    }
}

const toggleStatus = (make) => {
    router.post(route('admin.car-listings.make.toggle-status', make.id))
}
</script>

<template>
    <AdminLayout>

        <Head title="Car Makes" />

        <div class="space-y-6">
            <header class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Car Makes</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage automotive brands and listings.</p>
                </div>
                <button @click="openCreateModal"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Add New Make
                </button>
            </header>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 w-24 text-center">SL NO</th>
                            <th class="px-6 py-4">Image</th>
                            <th class="px-6 py-4">Make Name</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(make, index) in makes.data" :key="make.id"
                            class="hover:bg-gray-50/50 transition duration-200">
                            <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">#{{ (makes.current_page -
                                1) * makes.per_page + index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-100 flex items-center justify-center overflow-hidden shadow-sm">
                                    <img v-if="make.image_url" :src="make.image_url"
                                        class="w-full h-full object-contain p-1" />
                                    <span v-else class="text-[10px] uppercase font-black text-gray-300">No Image</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800 tracking-tight">{{ make.name }}</td>
                            <td class="px-6 py-4 text-center">
                                <button @click="toggleStatus(make)"
                                    :class="[make.status ? 'text-emerald-600 bg-emerald-50 ring-emerald-100' : 'text-rose-600 bg-rose-50 ring-rose-100']"
                                    class="text-[10px] font-bold px-3 py-1.5 shadow-sm rounded-full uppercase tracking-tighter ring-1 transition hover:opacity-80">
                                    {{ make.status ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="openEditModal(make)"
                                        class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteMake(make)"
                                        class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <CreateMake :show="showingCreateModal" @close="showingCreateModal = false" />
        <EditMake v-if="selectedMake" :show="showingEditModal" :make="selectedMake" @close="closeEditModal" />
    </AdminLayout>
</template>
