<template>
    <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500">
            Tampilkan
        </span>

        <select
            :value="modelValue"
            class="h-9 rounded-lg border border-gray-300 bg-white px-2 text-sm text-gray-700 outline-none transition focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option
                v-for="limit in limits"
                :key="limit"
                :value="limit"
            >
                {{ limit }}
            </option>
        </select>

        <span class="text-sm text-gray-500">
            data
        </span>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: 5,
    },

    limits: {
        type: Array,
        default: () => [5, 10, 15, 25],
    },
})

defineEmits(['update:modelValue'])
</script>