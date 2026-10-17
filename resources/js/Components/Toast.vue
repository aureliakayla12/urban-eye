<script setup>
import { computed, onBeforeUnmount, watch } from 'vue'
import { CheckCircle2, XCircle, Info, X } from 'lucide-vue-next'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) =>
            ['success', 'error', 'info'].includes(value),
    },
    title: {
        type: String,
        default: '',
    },
    message: {
        type: String,
        default: '',
    },
    duration: {
        type: Number,
        default: 3500,
    },
})

const emit = defineEmits(['close'])

let timeoutId = null

const config = computed(() => {
    const types = {
        success: {
            icon: CheckCircle2,
            title: props.title || 'Berhasil!',
            color: 'text-green-700',
            background: 'bg-green-50',
            border: 'border-green-200',
        },
        error: {
            icon: XCircle,
            title: props.title || 'Terjadi kesalahan',
            color: 'text-red-700',
            background: 'bg-red-50',
            border: 'border-red-200',
        },
        info: {
            icon: Info,
            title: props.title || 'Informasi',
            color: 'text-blue-700',
            background: 'bg-blue-50',
            border: 'border-blue-200',
        },
    }

    return types[props.type] || types.success
})

function closeToast() {
    clearTimeout(timeoutId)
    emit('close')
}

watch(
    () => [props.show, props.duration],
    ([show, duration]) => {
        clearTimeout(timeoutId)

        if (show && duration > 0) {
            timeoutId = setTimeout(closeToast, duration)
        }
    },
    { immediate: true },
)

onBeforeUnmount(() => {
    clearTimeout(timeoutId)
})
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-4 opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-4 opacity-0"
        >
            <div
                v-if="show"
                class="fixed right-4 top-4 z-[9999] w-[calc(100%-2rem)] max-w-sm"
                role="status"
                aria-live="polite"
            >
                <div
                    class="flex items-start gap-3 rounded-xl border p-4 shadow-lg"
                    :class="[config.background, config.border]"
                >
                    <component
                        :is="config.icon"
                        class="mt-0.5 h-6 w-6 shrink-0"
                        :class="config.color"
                    />

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-semibold"
                            :class="config.color"
                        >
                            {{ config.title }}
                        </p>

                        <p
                            v-if="message"
                            class="mt-1 break-words text-sm text-gray-600"
                        >
                            {{ message }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-md p-1 text-gray-500 transition hover:bg-black/5 hover:text-gray-800"
                        aria-label="Tutup notifikasi"
                        @click="closeToast"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>