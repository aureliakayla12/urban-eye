<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div>
        <label
            v-if="label"
            class="mb-2 block text-xs font-semibold text-black"
        >
            {{ label }}
        </label>

        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
            class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-xs text-black outline-none placeholder:text-gray-400 focus:border-gray-300 focus:ring-0 disabled:bg-gray-100 disabled:text-gray-400"
        />

        <p
            v-if="error"
            class="mt-1 text-xs text-red-600"
        >
            {{ error }}
        </p>
    </div>
</template>