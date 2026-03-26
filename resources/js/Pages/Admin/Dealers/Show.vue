<script setup>
import { router, Link } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    user: Object,
    routePrefix: String,
    role: String,
})

const emit = defineEmits(['close'])

const closeModal = () => {
    emit('close')
}

const verifyUser = () => {
    router.post(route(`${props.routePrefix}.approve`, props.user.id), {}, {
        onSuccess: () => closeModal(),
    })
}
</script>

<template>
    <Modal :show="show" @close="closeModal" maxWidth="md">
        <div class="p-8" v-if="user">
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4">
                {{ user.name }}'s Details
            </h2>

            <div class="mt-6 space-y-5">
                <!-- User Basic Info -->
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email Address</div>
                    <div class="col-span-2 text-sm font-medium text-gray-700">{{ user.email }}</div>
                </div>

                <div class="grid grid-cols-3 gap-2 border-t pt-4">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Account Status</div>
                    <div class="col-span-2">
                        <span v-if="user.status"
                            class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-tight ring-1 ring-emerald-100">
                            Active
                        </span>
                        <span v-else
                            class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full uppercase tracking-tight ring-1 ring-rose-100">
                            Inactive
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 border-t pt-4">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Verification</div>
                    <div class="col-span-2">
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
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 border-t pt-4">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Joined On</div>
                    <div class="col-span-2 text-sm font-medium text-gray-700">
                        {{ new Date(user.created_at).toLocaleDateString('en-GB', {
                            day: 'numeric', month: 'long', year: 'numeric'
                        }) }}
                    </div>
                </div>

                <!-- User Listings Section -->
                <div class="border-t pt-6" v-if="role === 'dealer' || role === 'private_seller'">
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Car Listings</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <Link v-if="role === 'dealer' || role === 'private_seller'"
                            :href="route('admin.used-car-listings.index', { user_id: user.id })"
                            class="flex flex-col items-center justify-center p-3 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-600 hover:text-white transition-all group shadow-sm">
                            <svg class="h-6 w-6 text-blue-600 group-hover:text-white mb-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 17a2 2 0 11-4 0 2 2 0 014 0zM9 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                            </svg>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-blue-700 group-hover:text-white">Used
                                Cars</span>
                        </Link>
                        <Link v-if="role === 'dealer'"
                            :href="route('admin.new-car-listings.index', { user_id: user.id })"
                            class="flex flex-col items-center justify-center p-3 bg-purple-50 border border-purple-100 rounded-xl hover:bg-purple-600 hover:text-white transition-all group shadow-sm">
                            <svg class="h-6 w-6 text-purple-600 group-hover:text-white mb-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                            </svg>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-purple-700 group-hover:text-white">New
                                Cars</span>
                        </Link>
                    </div>
                </div>

                <!-- Activity Logs Section -->
                <div class="border-t pt-6">
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Latest Activity Logs
                    </h3>
                    <div v-if="user.activity_logs?.length"
                        class="space-y-3 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                        <div v-for="log in user.activity_logs" :key="log.id"
                            class="flex items-start justify-between bg-gray-50 p-2.5 rounded-lg border border-gray-100 shadow-sm">
                            <div class="flex flex-col space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span :class="[
                                        'px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter shadow-sm',
                                        log.method === 'POST' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' :
                                            log.method === 'GET' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' :
                                                log.method === 'DELETE' ? 'bg-rose-100 text-rose-700 border border-rose-200' :
                                                    'bg-amber-100 text-amber-700 border border-amber-200'
                                    ]">
                                        {{ log.method }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-600 truncate max-w-[150px]"
                                        :title="log.route">
                                        {{ log.action || log.route }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-[9px] font-medium text-gray-400 uppercase italic">
                                {{ new Date(log.created_at).toLocaleDateString('en-GB', {
                                    day: 'numeric', month: 'short'
                                }) }}
                            </span>
                        </div>
                    </div>
                    <div v-else
                        class="text-[10px] font-bold text-gray-300 uppercase italic text-center py-4 bg-gray-50 rounded-lg">
                        No activity recorded yet
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-between space-x-3 pt-6 border-t font-semibold">
                <SecondaryButton @click="closeModal" class="flex-1 justify-center py-2.5">
                    Close Details
                </SecondaryButton>

                <PrimaryButton v-if="user.verification === 'pending'" @click="verifyUser"
                    class="flex-1 justify-center py-2.5 bg-indigo-600 hover:bg-indigo-700 border-none shadow-md">
                    Verify Account
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
