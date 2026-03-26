<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, watch, computed } from 'vue'
import CreateUser from './Create.vue'
import ShowUser from './Show.vue'
import EditUser from './Edit.vue'

const props = defineProps({
    users: Object,
    title: String,
    role: String,
    filters: Object,
})

const routePrefix = computed(() => {
    if (props.role === 'dealer') return 'admin.dealers'
    if (props.role === 'private_seller') return 'admin.private-sellers'
    return 'admin.buyers'
})

const date = ref(props.filters?.date || '')

watch(date, (value) => {
    router.get(window.location.pathname, { date: value }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    })
})

const showingCreateUserModal = ref(false)
const showingShowUserModal = ref(false)
const showingEditUserModal = ref(false)
const selectedUser = ref(null)

const openCreateUserModal = () => {
    showingCreateUserModal.value = true
}

const openShowUserModal = (user) => {
    selectedUser.value = user
    showingShowUserModal.value = true
}

const openEditUserModal = (user) => {
    selectedUser.value = user
    showingEditUserModal.value = true
}

const closingShowUserModal = () => {
    showingShowUserModal.value = false
    selectedUser.value = null
}

const closingEditUserModal = () => {
    showingEditUserModal.value = false
    selectedUser.value = null
}

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${props.role.replace('_', ' ')} ${user.name}? This action cannot be undone.`)) {
        router.delete(route(`${routePrefix.value}.destroy`, user.id))
    }
}

const closeModal = () => {
    showingCreateUserModal.value = false
}

const timeAgo = (date) => {
    if (!date) return 'Never'
    const now = new Date()
    const diff = Math.floor((now - new Date(date)) / 1000)

    if (diff < 60) return `${diff} seconds ago`
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`
    if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })
}
</script>

<template>
    <AdminLayout>

        <Head :title="title" />

        <div class="space-y-6">
            <header class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ title }} Management</h1>
                    <p class="text-gray-500 text-sm mt-1">Showing all registered users with the role of {{ role }}.</p>
                </div>
                <button @click="openCreateUserModal"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Add New {{ title }}
                </button>
            </header>

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 w-24 text-center">SL NO</th>
                            <th class="px-6 py-4 text-center">User Details</th>
                            <th class="px-6 py-4 text-center">Registered On</th>
                            <th class="px-6 py-4 text-center">Last Used</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Verification</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(user, index) in users.data" :key="user.id"
                            class="hover:bg-gray-50 transition-colors h-24">
                            <td class="px-6 py-4 font-medium text-xs text-gray-400 text-center align-middle">
                                {{ users.from + index }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="flex items-center justify-center space-x-4">
                                    <div
                                        class="h-12 w-12 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-center flex-shrink-0">
                                        <!-- Image Placeholder -->
                                    </div>
                                    <div class="text-left min-w-[120px]">
                                        <div class="font-bold text-gray-900 text-sm">{{ user.name }}</div>
                                        <div class="text-[11px] text-gray-400 font-medium">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-500 text-center align-middle">
                                {{ new Date(user.created_at).toLocaleDateString('en-GB', {
                                    day: 'numeric', month:
                                        'short', year: 'numeric'
                                }) }}
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <span v-if="user.latest_activity" class="text-[11px] font-bold text-gray-700 whitespace-nowrap uppercase tracking-tight">
                                    {{ timeAgo(user.latest_activity.created_at) }}
                                </span>
                                <span v-else class="text-[10px] font-bold text-gray-300 uppercase italic">Never</span>
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <button @click="router.post(route(`${routePrefix}.toggle-status`, user.id))"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2"
                                    :class="[user.status ? 'bg-emerald-500 focus:ring-emerald-500' : 'bg-rose-500 focus:ring-rose-500']">
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="[user.status ? 'translate-x-5' : 'translate-x-0']"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <span v-if="user.verification === 'approved'"
                                    class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-tight ring-1 ring-indigo-100">
                                    Verified
                                </span>
                                <span v-else-if="user.verification === 'pending'"
                                    class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full uppercase tracking-tight ring-1 ring-amber-100">
                                    Pending
                                </span>
                                <span v-else
                                    class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full uppercase tracking-tight ring-1 ring-rose-100">
                                    {{ user.verification }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <div class="flex justify-center space-x-2">
                                    <button v-if="user.verification === 'pending'" title="Verify"
                                        @click="router.post(route(`${routePrefix}.approve`, user.id))"
                                        class="h-8 w-8 bg-emerald-50 text-emerald-600 hover:text-white hover:bg-emerald-600 border border-emerald-100 flex items-center justify-center rounded-lg transition-all shadow-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button title="View" @click="openShowUserModal(user)"
                                        class="h-8 w-8 bg-gray-50 text-gray-500 hover:text-indigo-600 hover:bg-white border border-gray-100 flex items-center justify-center rounded-lg transition-all shadow-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button title="Edit"
                                        @click="openEditUserModal(user)"
                                        class="h-8 w-8 bg-gray-50 text-gray-500 hover:text-indigo-600 hover:bg-white border border-gray-100 flex items-center justify-center rounded-lg transition-all shadow-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button title="Delete"
                                        @click="deleteUser(user)"
                                        class="h-8 w-8 bg-gray-50 text-gray-500 hover:text-red-500 hover:bg-white border border-gray-100 flex items-center justify-center rounded-lg transition-all shadow-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="p-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-gray-400 font-bold uppercase tracking-widest text-xs">No entries
                                        found</div>
                                    <div class="text-gray-300 text-[10px] mt-2">There are currently no {{ role }}s
                                        registered.</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="flex items-center justify-between mt-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100"
                v-if="users.links && users.links.length > 3">
                <div class="text-xs text-gray-400 font-medium">
                    Showing {{ users.from }} to {{ users.to }} of {{ users.total }} entries
                </div>
                <div class="flex space-x-1">
                    <Link v-for="(link, index) in users.links" :key="index" :href="link.url || '#'" v-html="link.label"
                        class="px-3 py-1.5 text-[11px] font-black uppercase tracking-tighter rounded-md transition-all duration-200 border"
                        :class="[
                            link.active ? 'bg-indigo-600 text-white border-indigo-600 shadow-md transform scale-110 z-10' : 'bg-white text-gray-400 border-gray-100 hover:border-indigo-300 hover:text-indigo-600',
                            !link.url ? 'opacity-30 cursor-not-allowed pointer-events-none' : ''
                        ]" preserve-scroll>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>

    <CreateUser :show="showingCreateUserModal" :title="title" :role="role" :routePrefix="routePrefix" @close="closeModal" />

    <ShowUser
        :show="showingShowUserModal"
        :user="selectedUser"
        :routePrefix="routePrefix"
        :role="role"
        @close="closingShowUserModal"
    />

    <EditUser
        :show="showingEditUserModal"
        :user="selectedUser"
        :routePrefix="routePrefix"
        @close="closingEditUserModal"
    />
</template>
