<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
})

const statusLabel = computed(() => {
    const labels = {
        menunggu: 'Menunggu',
        diproses: 'Diproses',
        selesai: 'Selesai',
        ditolak: 'Ditolak',
    }

    return labels[props.status] ?? props.status
})
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
        :class="{
            'bg-yellow-100 text-yellow-700': status === 'menunggu',
            'bg-blue-100 text-blue-700': status === 'diproses',
            'bg-green-100 text-green-700': status === 'selesai',
            'bg-red-100 text-red-700': status === 'ditolak',
            'bg-gray-100 text-gray-600': ![
                'menunggu',
                'diproses',
                'selesai',
                'ditolak',
            ].includes(status),
        }"
    >
        {{ statusLabel }}
    </span>
</template>