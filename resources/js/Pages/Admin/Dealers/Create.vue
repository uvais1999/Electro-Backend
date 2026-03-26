<script setup>
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: String,
    role: String,
    routePrefix: String,
})

const emit = defineEmits(['close'])

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: props.role,
})

const closeModal = () => {
    emit('close')
    form.reset()
}

const createUser = () => {
    form.post(route(`${props.routePrefix}.store`), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    })
}
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Add New {{ title }}
            </h2>

            <form @submit.prevent="createUser" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full"
                        required />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                        class="mt-1 block w-full" required />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>

                <div class="flex items-center justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create {{ title.slice(0, -1) }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
