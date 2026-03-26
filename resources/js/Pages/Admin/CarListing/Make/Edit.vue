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
    make: Object
})

const emit = defineEmits(['close'])

const form = useForm({
    _method: 'PUT',
    name: props.make?.name || '',
    image: null,
    status: props.make?.status ?? true,
})

watch(() => props.make, (newVal) => {
    if (newVal) {
        form.name = newVal.name
        form.status = newVal.status
    }
})

const submit = () => {
    form.post(route('admin.car-listings.make.update', props.make.id), {
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
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4 mb-8 uppercase tracking-widest text-center">Update Car Make</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="name" value="Make Name" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <TextInput id="name" type="text" v-model="form.name" class="w-full" required placeholder="e.g. HONDA" />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="image" value="Brand Logo" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <div v-if="make.image_url" class="mb-4 w-20 h-20 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center p-2 shadow-sm">
                        <img :src="make.image_url" />
                    </div>
                    <input type="file" @input="form.image = $event.target.files[0]" id="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    <InputError :message="form.errors.image" class="mt-2" />
                </div>

                <div class="block">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="form.status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <span class="ml-2 text-sm text-gray-600 font-bold uppercase tracking-widest text-[10px]">Active Status</span>
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                    <SecondaryButton @click="emit('close')" :disabled="form.processing">Cancel</SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update Make</PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
