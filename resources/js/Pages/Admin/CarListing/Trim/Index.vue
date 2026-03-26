<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import CreateTrim from './Create.vue'
import EditTrim from './Edit.vue'

const props = defineProps({
    trims: Object,
    makes: Array
})

const showingCreateModal = ref(false)
const showingEditModal = ref(false)
const selectedTrim = ref(null)

const openCreateModal = () => showingCreateModal.value = true
const openEditModal = (trim) => {
    selectedTrim.value = trim
    showingEditModal.value = true
}

const closeEditModal = () => {
    showingEditModal.value = false
    selectedTrim.value = null
}

const deleteTrim = (trim) => {
    if (confirm(`Are you sure you want to delete ${trim.name}?`)) {
        router.delete(route('admin.car-listings.trim.destroy', trim.id))
    }
}

const toggleStatus = (trim) => {
    router.post(route('admin.car-listings.trim.toggle-status', trim.id))
}
</script>

<template>
    <AdminLayout>
        <Head title="Car Trims" />

        <div class="space-y-6">
            <header class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Car Trims</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage automotive trims linked to brands and models.</p>
                </div>
                <button @click="openCreateModal"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Add New Trim
                </button>
            </header>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 w-24 text-center">SL NO</th>
                            <th class="px-6 py-4">Brand & Model</th>
                            <th class="px-6 py-4">Trim Name</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(trim, index) in trims.data" :key="trim.id" class="hover:bg-gray-50/50 transition duration-200">
                            <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">#{{ (trims.current_page - 1) * trims.per_page + index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 leading-none mb-1">{{ trim.model.make.name }}</span>
                                    <span class="font-bold text-gray-900 text-sm">{{ trim.model.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-indigo-600 tracking-tight">{{ trim.name }}</td>
                            <td class="px-6 py-4 text-center">
                                <button @click="toggleStatus(trim)"
                                    :class="[trim.status ? 'text-emerald-600 bg-emerald-50 ring-emerald-100' : 'text-rose-600 bg-rose-50 ring-rose-100']"
                                    class="text-[10px] font-bold px-3 py-1.5 shadow-sm rounded-full uppercase tracking-tighter ring-1 transition hover:opacity-80">
                                    {{ trim.status ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="openEditModal(trim)" class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteTrim(trim)" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
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

        <CreateTrim :show="showingCreateModal" :makes="makes" @close="showingCreateModal = false" />
        <EditTrim v-if="selectedTrim" :show="showingEditModal" :trim="selectedTrim" :makes="makes" @close="closeEditModal" />
    </AdminLayout>
</template>
