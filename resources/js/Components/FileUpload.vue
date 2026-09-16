<script setup>
import { ref } from 'vue'

defineProps({
    label: {
        type: String,
        default: 'Foto',
    },
    error: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])

const fileName = ref('')

function handleFile(event) {
    const file = event.target.files[0]

    fileName.value = file ? file.name : ''
    emit('update:modelValue', file ?? null)
}
</script>

<template>
    <div>
        <label
            class="block text-sm font-medium text-gray-700"
        >
            {{ label }}
        </label>

        <input
            type="file"
            accept="image/jpeg,image/png"
            @change="handleFile"
            class="file-input file-input-bordered mt-1 w-full"
        />

        <p
            v-if="fileName"
            class="mt-2 text-sm text-gray-600"
        >
            File dipilih: {{ fileName }}
        </p>

        <p class="mt-1 text-xs text-gray-500">
            JPG, JPEG, atau PNG. Maksimal 2MB.
        </p>

        <p
            v-if="error"
            class="mt-1 text-sm text-red-600"
        >
            {{ error }}
        </p>
    </div>
</template>