import { ref } from 'vue'

const toasts = ref([])

export function useToast() {
    const show = (message, type = 'success') => {
        const id = Date.now()
        const toast = { id, message, type, show: true }
        toasts.value.push(toast)

        setTimeout(() => {
            toasts.value = toasts.value.filter(t => t.id !== id)
        }, 4000)
    }

    const success = (message) => show(message, 'success')
    const error = (message) => show(message, 'error')

    return {
        toasts,
        show,
        success,
        error
    }
}
