<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import {
    FileText,
    Clock,
    LoaderCircle,
    CheckCircle2,
} from 'lucide-vue-next'

import {
    Chart,
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from 'chart.js'

Chart.register(
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
)

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total: 0,
            waiting: 0,
            processing: 0,
            completed: 0,
        }),
    },

    categoryStatistics: {
        type: Array,
        default: () => [],
    },

    weeklyStatistics: {
        type: Array,
        default: () => [],
    },

    districtStatistics: {
        type: Array,
        default: () => [],
    },

    villageStatistics: {
        type: Array,
        default: () => [],
    },
})

const categoryChart = ref(null)
const weeklyChart = ref(null)
const districtChart = ref(null)
const villageChart = ref(null)

let categoryChartInstance = null
let weeklyChartInstance = null
let districtChartInstance = null
let villageChartInstance = null

onMounted(() => {
    createCategoryChart()
    createWeeklyChart()
    createDistrictChart()
    createVillageChart()
})

onBeforeUnmount(() => {
    categoryChartInstance?.destroy()
    weeklyChartInstance?.destroy()
    districtChartInstance?.destroy()
    villageChartInstance?.destroy()
})

function createCategoryChart() {
    if (!categoryChart.value) return

    categoryChartInstance = new Chart(categoryChart.value, {
        type: 'bar',

        data: {
            labels: props.categoryStatistics.map(item => item.name),

            datasets: [
                {
                    label: 'Jumlah Laporan',
                    data: props.categoryStatistics.map(item => item.total),
                    backgroundColor: '#1B5E20',
                    borderRadius: 6,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    })
}

function createWeeklyChart() {
    if (!weeklyChart.value) return

    weeklyChartInstance = new Chart(weeklyChart.value, {
        type: 'line',

        data: {
            labels: props.weeklyStatistics.map(item => item.label),

            datasets: [
                {
                    label: 'Jumlah Laporan',
                    data: props.weeklyStatistics.map(item => item.total),

                    borderColor: '#1B5E20',
                    backgroundColor: 'rgba(27, 94, 32, 0.12)',

                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,

                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    })
}

function createDistrictChart() {
    if (!districtChart.value) return

    districtChartInstance = new Chart(districtChart.value, {
        type: 'bar',

        data: {
            labels: props.districtStatistics.map(item => item.name),

            datasets: [
                {
                    label: 'Jumlah Laporan',
                    data: props.districtStatistics.map(item => item.total),

                    backgroundColor: '#2E7D32',
                    borderRadius: 6,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    })
}

function createVillageChart() {
    if (!villageChart.value) return

    villageChartInstance = new Chart(villageChart.value, {
        type: 'bar',

        data: {
            labels: props.villageStatistics.map(item => item.name),

            datasets: [
                {
                    label: 'Jumlah Laporan',
                    data: props.villageStatistics.map(item => item.total),

                    backgroundColor: '#43A047',
                    borderRadius: 6,
                },
            ],
        },

        options: {
            indexAxis: 'y',

            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                x: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    })
}
</script>

<template>
    <Head title="Statistik & Grafik" />

    <AuthenticatedLayout>

        <!-- Header di samping garis tiga -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Statistik & Grafik
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Ringkasan dan visualisasi laporan masyarakat.
                    </p>
                </div>
            </div>
        </template>

        <!-- Isi halaman -->
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Summary Cards -->
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >

                    <!-- Total Laporan -->
                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-gray-500">
                                    Total Laporan
                                </p>

                                <p class="mt-2 text-3xl font-bold text-gray-800">
                                    {{ summary.total }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-green-100 p-3">
                                <FileText
                                    class="h-6 w-6 text-green-700"
                                />
                            </div>

                        </div>
                    </div>

                    <!-- Menunggu -->
                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-gray-500">
                                    Menunggu
                                </p>

                                <p class="mt-2 text-3xl font-bold text-gray-800">
                                    {{ summary.waiting }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-yellow-100 p-3">
                                <Clock
                                    class="h-6 w-6 text-yellow-600"
                                />
                            </div>

                        </div>
                    </div>

                    <!-- Diproses -->
                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-gray-500">
                                    Diproses
                                </p>

                                <p class="mt-2 text-3xl font-bold text-gray-800">
                                    {{ summary.processing }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-blue-100 p-3">
                                <LoaderCircle
                                    class="h-6 w-6 text-blue-600"
                                />
                            </div>

                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-gray-500">
                                    Selesai
                                </p>

                                <p class="mt-2 text-3xl font-bold text-gray-800">
                                    {{ summary.completed }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-green-100 p-3">
                                <CheckCircle2
                                    class="h-6 w-6 text-green-600"
                                />
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Grafik Kategori + Grafik Mingguan -->
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <!-- Grafik Kategori -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">

                        <h2 class="text-lg font-semibold text-gray-800">
                            Laporan Berdasarkan Kategori
                        </h2>

                        <p class="mb-4 text-sm text-gray-500">
                            Jumlah laporan pada setiap kategori.
                        </p>

                        <div class="h-80">
                            <canvas ref="categoryChart"></canvas>
                        </div>

                    </div>

                    <!-- Grafik Mingguan -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">

                        <h2 class="text-lg font-semibold text-gray-800">
                            Tren Laporan per Minggu
                        </h2>

                        <p class="mb-4 text-sm text-gray-500">
                            Perkembangan jumlah laporan dalam tujuh minggu terakhir.
                        </p>

                        <div class="h-80">
                            <canvas ref="weeklyChart"></canvas>
                        </div>

                    </div>

                </div>

                <!-- Grafik Kecamatan -->
                <div class="mt-6 rounded-xl bg-white p-6 shadow-sm">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Laporan Berdasarkan Kecamatan
                    </h2>

                    <p class="mb-4 text-sm text-gray-500">
                        Jumlah laporan berdasarkan wilayah kecamatan.
                    </p>

                    <div class="h-80">
                        <canvas ref="districtChart"></canvas>
                    </div>

                </div>

                <!-- Grafik Kelurahan -->
                <div class="mt-6 rounded-xl bg-white p-6 shadow-sm">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Laporan Berdasarkan Kelurahan
                    </h2>

                    <p class="mb-4 text-sm text-gray-500">
                        Jumlah laporan berdasarkan wilayah kelurahan.
                    </p>

                    <div class="h-96">
                        <canvas ref="villageChart"></canvas>
                    </div>

                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>