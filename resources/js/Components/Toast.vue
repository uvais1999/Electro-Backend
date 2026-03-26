<script setup>
import { useToast } from '@/Composables/useToast'
import { watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const { toasts, success, error } = useToast()
const page = usePage()

// Watch Inertia flash shared props
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        success(flash.success)
        flash.success = null // Clear immediately to allow consecutive identical messages
    }
    if (flash?.error) {
        error(flash.error)
        flash.error = null // Clear immediately to allow consecutive identical messages
    }
}, { deep: true, immediate: true })

const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id)
}
</script>

<template>
    <div class="fixed top-20 right-8 z-[60] flex flex-col items-end space-y-3 pointer-events-none">
        <TransitionGroup
            enter-active-class="transform ease-out duration-300 transition-all"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transform ease-in duration-200 transition-all"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-full opacity-0"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="flex items-center min-w-[300px] border px-4 py-3 rounded-xl shadow-xl backdrop-blur-md transition-all duration-300 pointer-events-auto"
                :class="[
                    toast.type === 'success' 
                        ? 'bg-emerald-50/90 border-emerald-100 text-emerald-800 ring-4 ring-emerald-500/10'
                        : 'bg-rose-50/90 border-rose-100 text-rose-800 ring-4 ring-rose-500/10'
                ]"
            >
                <!-- Icon Success -->
                <div v-if="toast.type === 'success'" class="bg-emerald-500 p-1.5 rounded-lg mr-3 shadow-md flex-shrink-0 animate-bounce">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <!-- Icon Error -->
                <div v-else class="bg-rose-500 p-1.5 rounded-lg mr-3 shadow-md flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>

                <div class="flex-1">
                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-50 mb-0.5">
                        {{ toast.type === 'success' ? 'Success' : 'Attention' }}
                    </div>
                    <div class="text-xs font-black tracking-tight leading-tight">
                        {{ toast.message }}
                    </div>
                </div>

                <button @click="removeToast(toast.id)" class="ml-4 hover:opacity-100 opacity-30 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
