<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import CreateEmirate from './Create.vue'
import EditEmirate from './Edit.vue'

const props = defineProps({
    emirates: Object
})

const showingCreateModal = ref(false)
const showingEditModal = ref(false)
const selectedEmirate = ref(null)

const openCreateModal = () => showingCreateModal.value = true
const openEditModal = (emirate) => {
    selectedEmirate.value = emirate
    showingEditModal.value = true
}

const closeEditModal = () => {
    showingEditModal.value = false
    selectedEmirate.value = null
}

const deleteEmirate = (emirate) => {
    if (confirm(`Are you sure you want to delete ${emirate.name}?`)) {
        router.delete(route('admin.car-listings.emirate.destroy', emirate.id))
    }
}

const toggleStatus = (emirate) => {
    router.post(route('admin.car-listings.emirate.toggle-status', emirate.id))
}
</script>

<template>
    <AdminLayout>
        <Head title="Emirates" />

        <div class="space-y-6">
            <header class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Emirates</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage UAE emirates for car listing regions.</p>
                </div>
                <button @click="openCreateModal"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Add New Emirate
                </button>
            </header>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 w-24 text-center">SL NO</th>
                            <th class="px-6 py-4">Emirate Name</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(emirate, index) in emirates.data" :key="emirate.id" class="hover:bg-gray-50/50 transition duration-200">
                            <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">#{{ (emirates.current_page - 1) * emirates.per_page + index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800 tracking-tight">{{ emirate.name }}</td>
                            <td class="px-6 py-4 text-center">
                                <button @click="toggleStatus(emirate)"
                                    :class="[emirate.status ? 'text-emerald-600 bg-emerald-50 ring-emerald-100' : 'text-rose-600 bg-rose-50 ring-rose-100']"
                                    class="text-[10px] font-bold px-3 py-1.5 shadow-sm rounded-full uppercase tracking-tighter ring-1 transition hover:opacity-80">
                                    {{ emirate.status ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="openEditModal(emirate)" class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteEmirate(emirate)" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <CreateEmirate :show="showingCreateModal" @close="showingCreateModal = false" />
        <EditEmirate v-if="selectedEmirate" :show="showingEditModal" :emirate="selectedEmirate" @close="closeEditModal" />
    </AdminLayout>
</template>
