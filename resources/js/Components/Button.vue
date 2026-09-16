<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold transition duration-200',
            'disabled:cursor-not-allowed disabled:opacity-50',
            variant === 'primary'
                ? 'bg-[#1B5E20] text-white hover:bg-[#164A19]'
                : 'border border-[#1B5E20] bg-white text-[#1B5E20] hover:bg-[#F5F5F5]',
        ]"
    >
        <slot />
    </button>
</template>