<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    rows: {
        type: Number,
        default: 4,
    },
    error: {
        type: String,
        default: '',
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

        <textarea
            :value="modelValue"
            :rows="rows"
            :placeholder="placeholder"
            @input="$emit('update:modelValue', $event.target.value)"
            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-[#1B5E20] focus:ring-[#1B5E20]"
        ></textarea>

        <p
            v-if="error"
            class="mt-1 text-sm text-red-600"
        >
            {{ error }}
        </p>
    </div>
</template>