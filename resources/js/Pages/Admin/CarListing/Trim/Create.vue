<script setup>
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    show: Boolean,
    makes: Array
})

const emit = defineEmits(['close'])

const selectedMakeId = ref('')
const models = ref([])
const loadingModels = ref(false)

const form = useForm({
    car_model_id: '',
    name: '',
})

watch(selectedMakeId, async (newVal) => {
    form.car_model_id = ''
    models.value = []
    if (newVal) {
        loadingModels.value = true
        try {
            const response = await axios.get(route('admin.car-listings.trim.get-models', newVal))
            models.value = response.data
        } catch (error) {
            console.error('Error fetching models:', error)
        } finally {
            loadingModels.value = false
        }
    }
})

const submit = () => {
    form.post(route('admin.car-listings.trim.store'), {
        onSuccess: () => {
            form.reset()
            selectedMakeId.value = ''
            emit('close')
        }
    })
}
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="xl">
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-900 border-b pb-4 mb-8 uppercase tracking-widest text-center">Add New Car Trim</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="make" value="Select Make" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <select v-model="selectedMakeId" id="make" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">Select a brand</option>
                        <option v-for="make in makes" :key="make.id" :value="make.id">{{ make.name }}</option>
                    </select>
                </div>

                <div v-if="selectedMakeId">
                    <InputLabel for="car_model_id" value="Select Model" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <select v-model="form.car_model_id" id="car_model_id" :disabled="loadingModels" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">{{ loadingModels ? 'Loading...' : 'Select a model' }}</option>
                        <option v-for="model in models" :key="model.id" :value="model.id">{{ model.name }}</option>
                    </select>
                    <InputError :message="form.errors.car_model_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="name" value="Trim Name" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                    <TextInput id="name" type="text" v-model="form.name" class="w-full" required placeholder="e.g. EX-L" />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                    <SecondaryButton @click="emit('close')" :disabled="form.processing">Cancel</SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Create Trim</PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
