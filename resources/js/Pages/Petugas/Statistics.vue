<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

import {
    ClipboardList,
    Clock3,
    CheckCircle2,
    Hourglass,
    TrendingUp,
} from 'lucide-vue-next'

const props = defineProps({
    statistics: {
        type: Object,
        default: () => ({
            total: 0,
            diproses: 0,
            selesai: 0,
            ditugaskan: 0,
        }),
    },
})

// DATA UNTUK DIAGRAM DONAT
const statusChart = computed(() => {
    const items = [
        {
            label: 'Ditugaskan',
            value: Number(props.statistics?.ditugaskan ?? 0),
            color: '#EAB308',
        },
        {
            label: 'Diproses',
            value: Number(props.statistics?.diproses ?? 0),
            color: '#3B82F6',
        },
        {
            label: 'Selesai',
            value: Number(props.statistics?.selesai ?? 0),
            color: '#16A34A',
        },
    ]

    const totalStatus = items.reduce(
        (total, item) => total + item.value,
        0
    )

    let currentPosition = 0

    const segments = items.map((item) => {
        const percentage = totalStatus > 0
            ? (item.value / totalStatus) * 100
            : 0

        const start = currentPosition
        const end = start + percentage

        currentPosition = end

        return {
            ...item,
            percentage: Math.round(percentage),
            start,
            end,
        }
    })

    const gradient = totalStatus > 0
        ? `conic-gradient(${segments
            .map(
                (item) =>
                    `${item.color} ${item.start}% ${item.end}%`
            )
            .join(', ')})`
        : '#E5E7EB'

    return {
        totalStatus,
        segments,
        style: {
            background: gradient,
        },
    }
})
</script>

<template>
    <Head title="Statistik" />

    <AuthenticatedLayout>
        <!-- HEADER -->
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Statistik
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan kinerja dan penanganan tugas Anda
                </p>
            </div>
        </template>

        <div class="space-y-6 p-5 sm:p-8">

            <!-- 4 KARTU STATISTIK -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                <!-- TOTAL TUGAS -->
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100"
                        >
                            <ClipboardList
                                class="h-6 w-6 text-green-600"
                            />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Total Tugas
                            </p>

                            <p class="mt-1 text-3xl font-bold text-gray-800">
                                {{ statistics.total }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Seluruh tugas Anda
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DITUGASKAN -->
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100"
                        >
                            <Hourglass
                                class="h-6 w-6 text-yellow-500"
                            />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Ditugaskan
                            </p>

                            <p class="mt-1 text-3xl font-bold text-yellow-500">
                                {{ statistics.ditugaskan }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Belum diproses
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DIPROSES -->
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100"
                        >
                            <Clock3
                                class="h-6 w-6 text-blue-600"
                            />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Diproses
                            </p>

                            <p class="mt-1 text-3xl font-bold text-blue-600">
                                {{ statistics.diproses }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Sedang ditangani
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SELESAI -->
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100"
                        >
                            <CheckCircle2
                                class="h-6 w-6 text-green-600"
                            />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Selesai
                            </p>

                            <p class="mt-1 text-3xl font-bold text-green-600">
                                {{ statistics.selesai }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Tugas terselesaikan
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- DIAGRAM STATUS TUGAS -->
            <div class="rounded-xl bg-white p-6 shadow-sm">

                <!-- JUDUL DIAGRAM -->
                <div class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100"
                    >
                        <TrendingUp
                            class="h-5 w-5 text-green-600"
                        />
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Progress Penanganan
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Diagram perbandingan status tugas Anda
                        </p>
                    </div>
                </div>

                <!-- DIAGRAM DAN LEGENDA -->
                <div
                    class="grid grid-cols-1 items-center gap-8 md:grid-cols-2"
                >

                    <!-- DIAGRAM DONAT -->
                    <div class="flex justify-center py-3">
                        <div
                            class="relative h-56 w-56 shrink-0 rounded-full"
                            :style="statusChart.style"
                        >
                            <!-- BAGIAN TENGAH DIAGRAM -->
                            <div
                                class="absolute inset-7 flex flex-col items-center justify-center rounded-full bg-white shadow-sm"
                            >
                                <p class="text-sm text-gray-500">
                                    Total Tugas
                                </p>

                                <p class="mt-1 text-4xl font-bold text-gray-800">
                                    {{ statistics.total }}
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Seluruh penugasan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- LEGENDA DIAGRAM -->
                    <div class="space-y-5">

                        <div
                            v-for="item in statusChart.segments"
                            :key="item.label"
                            class="flex items-center justify-between gap-4"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="h-4 w-4 shrink-0 rounded-full"
                                    :style="{
                                        backgroundColor: item.color
                                    }"
                                ></span>

                                <div>
                                    <p class="text-sm font-medium text-gray-700">
                                        {{ item.label }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ item.percentage }}% dari total status
                                    </p>
                                </div>
                            </div>

                            <p class="text-xl font-semibold text-gray-800">
                                {{ item.value }}
                            </p>
                        </div>

                        <!-- KETERANGAN JIKA BELUM ADA TUGAS -->
                        <p
                            v-if="statusChart.totalStatus === 0"
                            class="border-t border-gray-100 pt-4 text-xs text-gray-400"
                        >
                            Belum ada data tugas untuk ditampilkan.
                        </p>

                    </div>
                </div>
            </div>

            <!-- PERFORMA PENANGANAN -->
            <div class="rounded-xl border border-green-100 bg-green-50 p-5">
                <div class="flex items-start gap-3">
                    <CheckCircle2
                        class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                    />

                    <div>
                        <p class="text-sm font-medium text-green-800">
                            Performa Penanganan
                        </p>

                        <p class="mt-1 text-xs leading-5 text-green-700">
                            Anda telah menyelesaikan
                            <strong>
                                {{ statistics.selesai }}
                            </strong>
                            dari
                            <strong>
                                {{ statistics.total }}
                            </strong>
                            total tugas yang diberikan.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>