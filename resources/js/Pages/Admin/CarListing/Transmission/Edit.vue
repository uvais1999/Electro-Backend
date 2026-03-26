<script setup>
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { watch } from 'vue'

const props = defineProps({
    show: Boolean,
    transmission: Object
})

const emit = defineEmits(['close'])

const form = useForm({
    _method: 'PUT',
    name: props.transmission?.name || '',
    status: props.transmission?.status ?? true,
})

watch(() => props.transmission, (newVal) => {
    if (newVal) {
        form.name = newVal.name
        form.status = newVal.status
    }
})

const submit = () => {
    form.post(route('admin.car-listings.transmission.update', props.transmission.id), {
        onSuccess: () => {
            form.reset()
            emit('close')
        }
    })
}
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="xl">
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4 mb-8 uppercase tracking-widest text-center">Update Transmission</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="name" value="Transmission Name" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <TextInput id="name" type="text" v-model="form.name" class="w-full" required />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="block">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="form.status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <span class="ml-2 text-sm text-gray-600 font-bold uppercase tracking-widest text-[10px]">Active Status</span>
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                    <SecondaryButton @click="emit('close')" :disabled="form.processing">Cancel</SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update Transmission</PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
