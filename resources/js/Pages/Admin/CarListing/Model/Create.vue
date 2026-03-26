<script setup>
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
    show: Boolean,
    makes: Array
})

const emit = defineEmits(['close'])

const form = useForm({
    car_make_id: '',
    name: '',
})

const submit = () => {
    form.post(route('admin.car-listings.model.store'), {
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
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4 mb-8 uppercase tracking-widest text-center">Add New Car Model</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="car_make_id" value="Select Make" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <select v-model="form.car_make_id" id="car_make_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select a brand</option>
                        <option v-for="make in makes" :key="make.id" :value="make.id">{{ make.name }}</option>
                    </select>
                    <InputError :message="form.errors.car_make_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="name" value="Model Name" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <TextInput id="name" type="text" v-model="form.name" class="w-full" required placeholder="e.g. COROLLA" />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                    <SecondaryButton @click="emit('close')" :disabled="form.processing">Cancel</SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Create Model</PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
