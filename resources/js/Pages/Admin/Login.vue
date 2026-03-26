<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <Head title="Admin Login" />
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Admin Login</h1>
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" required>
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input v-model="form.password" type="password" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <button type="submit" :disabled="form.processing" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 focus:outline-none disabled:opacity-50">
                    Login
                </button>
            </form>
        </div>
    </div>
</template>
