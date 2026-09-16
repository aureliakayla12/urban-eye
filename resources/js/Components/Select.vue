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
            class="block text-sm font-medium text-gray-700"
        >
            {{ label }}
        </label>

        <select
            :value="modelValue"
            :disabled="disabled"
            @change="$emit('update:modelValue', $event.target.value)"
            class="mt-1 block w-full rounded-lg border-gray-300 bg-white focus:border-[#1B5E20] focus:ring-[#1B5E20] disabled:bg-gray-100 disabled:text-gray-400"
        >
            <slot />
        </select>

        <p
            v-if="error"
            class="mt-1 text-sm text-red-600"
        >
            {{ error }}
        </p>
    </div>
</template>