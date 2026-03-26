<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    user: Object,
    routePrefix: String,
})

const emit = defineEmits(['close'])

const form = useForm({
    name: '',
    email: '',
    password: '',
})

watch(() => props.user, (user) => {
    if (user) {
        form.name = user.name
        form.email = user.email
        form.password = ''
    }
}, { immediate: true })

const submit = () => {
    form.put(route(`${props.routePrefix}.update`, props.user.id), {
        onSuccess: () => closeModal(),
        onFinish: () => form.reset('password'),
    })
}

const closeModal = () => {
    emit('close')
    form.reset()
}
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <form @submit.prevent="submit" class="p-8">
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4 mb-6">
                Edit Dealer Details
            </h2>

            <div class="space-y-5">
                <div>
                    <InputLabel for="name" value="Full Name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full bg-gray-50 border-gray-100 focus:ring-indigo-500 rounded-lg text-sm"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="email" value="Email Address" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-gray-50 border-gray-100 focus:ring-indigo-500 rounded-lg text-sm"
                        v-model="form.email"
                        required
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="password" value="New Password (Leave blank to keep current)" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full bg-gray-50 border-gray-100 focus:ring-indigo-500 rounded-lg text-sm"
                        v-model="form.password"
                        placeholder="••••••••"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t">
                <SecondaryButton @click="closeModal" class="py-2.5 px-6">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-700 border-none shadow-md"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Update Dealer
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
