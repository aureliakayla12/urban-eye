<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { Head, Link } from '@inertiajs/vue3'

import {
    computed,
    onBeforeUnmount,
    onMounted,
    ref,
} from 'vue'

import {
    ClipboardList,
    AlertTriangle,
    Clock3,
    CircleCheck,
    UserCircle,
    Bell,
    ChevronDown,
    MapPin,
    Plus,
    Trophy,
    Leaf,
    ArrowRight,
    FileText,
    Medal,
    Award,
    Info,
    Hourglass,
    CheckCircle2,
    AlertCircle,
    CalendarDays,
} from 'lucide-vue-next'

import L from 'leaflet'

import 'leaflet/dist/leaflet.css'


const props = defineProps({

    user: {
        type: Object,
        default: () => ({}),
    },

    stats: {
        type: Object,
        default: () => ({}),
    },

    tugas: {
        type: Array,
        default: () => [],
    },

    laporan_terbaru: {
        type: Array,
        default: () => [],
    },

    kategori: {
        type: Array,
        default: () => [],
    },

    aktivitas_petugas: {
        type: Array,
        default: () => [],
    },

    peta_laporan: {
        type: Array,
        default: () => [],
    },

    poin: {
        type: Number,
        default: 0,
    },

    badge: {
        type: Number,
        default: 0,
    },

    peringkat: {
        type: [Number, String],
        default: '-',
    },

    leaderboard: {
        type: Array,
        default: () => [],
    },

})


/*
|--------------------------------------------------------------------------
| ADMIN - PIE CHART
|--------------------------------------------------------------------------
*/

const totalKategori = computed(() => {

    return props.kategori.reduce(
        (total, item) => total + Number(item.total),
        0
    )

})


const kategoriChart = computed(() => {

    const colors = [
        '#4CAF50',
        '#3F51B5',
        '#26C6DA',
        '#FFB300',
        '#673AB7',
    ]

    let current = 0

    return props.kategori.map((item, index) => {

        const total = Number(item.total)

        const percentage =
            totalKategori.value > 0
                ? (total / totalKategori.value) * 100
                : 0

        const start = current

        current += percentage

        return {
            name: item.category?.name ?? 'Tanpa Kategori',
            total,
            percentage,
            start,
            end: current,
            color: colors[index % colors.length],
        }

    })

})


const pieStyle = computed(() => {

    if (!kategoriChart.value.length) {

        return {
            background: '#E5E7EB',
        }

    }

    const parts = kategoriChart.value.map((item) => {

        return `${item.color} ${item.start}% ${item.end}%`

    })

    return {
        background: `conic-gradient(${parts.join(', ')})`,
    }

})


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const statusClass = (status) => {

    return {
        menunggu: 'bg-yellow-100 text-yellow-700',
        diproses: 'bg-blue-100 text-blue-700',
        selesai: 'bg-green-100 text-green-700',
        ditolak: 'bg-red-100 text-red-700',
    }[status] ?? 'bg-gray-100 text-gray-600'

}


/*
|--------------------------------------------------------------------------
| MAP
|--------------------------------------------------------------------------
*/

const mapElement = ref(null)

const map = ref(null)

const selectedCategory = ref('Semua Kategori')


const categoryOptions = computed(() => {

    const categories = props.peta_laporan
        .map((report) => report.category?.name)
        .filter(Boolean)

    return [
        'Semua Kategori',
        ...new Set(categories),
    ]

})


const filteredMapReports = computed(() => {

    if (selectedCategory.value === 'Semua Kategori') {

        return props.peta_laporan

    }

    return props.peta_laporan.filter(
        (report) =>
            report.category?.name === selectedCategory.value
    )

})


const statusMarkerColor = (status) => {

    return {
        menunggu: '#F59E0B',
        diproses: '#3B82F6',
        selesai: '#16A34A',
        ditolak: '#EF4444',
    }[status] ?? '#6B7280'

}


const createMarkerIcon = (status) => {

    const color = statusMarkerColor(status)

    return L.divIcon({

        className: '',

        html: `
            <div style="
                width: 18px;
                height: 18px;
                background: ${color};
                border: 3px solid white;
                border-radius: 999px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            "></div>
        `,

        iconSize: [18, 18],

        iconAnchor: [9, 9],

    })

}


/*
|--------------------------------------------------------------------------
| MAP MASYARAKAT
|--------------------------------------------------------------------------
*/

const loadMapMarkers = () => {

    if (!map.value) {

        return

    }


    map.value.eachLayer((layer) => {

        if (layer instanceof L.Marker) {

            map.value.removeLayer(layer)

        }

    })


    filteredMapReports.value.forEach((report) => {

        if (
            !report.latitude ||
            !report.longitude
        ) {

            return

        }


        const marker = L.marker(
            [
                Number(report.latitude),
                Number(report.longitude),
            ],
            {
                icon: createMarkerIcon(report.status),
            }
        )


        marker.bindPopup(`
            <div style="min-width: 180px">

                <strong>
                    ${report.title ?? 'Laporan'}
                </strong>

                <br>

                <span>
                    ${report.category?.name ?? 'Tanpa kategori'}
                </span>

                <br>

                <small>
                    ${report.address ?? ''}
                </small>

            </div>
        `)


        marker.addTo(map.value)

    })

}


/*
|--------------------------------------------------------------------------
| MAP PETUGAS
|--------------------------------------------------------------------------
*/

const loadPetugasMapMarkers = () => {

    if (!map.value) {

        return

    }


    map.value.eachLayer((layer) => {

        if (layer instanceof L.Marker) {

            map.value.removeLayer(layer)

        }

    })


    props.tugas.forEach((task) => {

        const report = task.report


        if (
            !report?.latitude ||
            !report?.longitude
        ) {

            return

        }


        const marker = L.marker(

            [
                Number(report.latitude),
                Number(report.longitude),
            ],

            {
                icon: createMarkerIcon(task.status),
            }

        )


        marker.bindPopup(`
            <div style="min-width: 200px">

                <strong>
                    ${report.title ?? 'Laporan'}
                </strong>

                <br>

                <span>
                    ${report.category?.name ?? 'Tanpa kategori'}
                </span>

                <br>

                <small>
                    ${report.address ?? 'Alamat tidak tersedia'}
                </small>

                <br>

                <small>
                    Status: ${task.status ?? '-'}
                </small>

            </div>
        `)


        marker.addTo(map.value)

    })

}


/*
|--------------------------------------------------------------------------
| INITIALIZE MAP
|--------------------------------------------------------------------------
*/

const initializeMap = () => {

    if (
        !mapElement.value ||
        map.value
    ) {

        return

    }


    map.value = L.map(
        mapElement.value,
        {
            zoomControl: false,
        }
    ).setView(
        [-6.4025, 106.7942],
        11
    )


    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap contributors',
        }
    ).addTo(map.value)


    L.control.zoom({
        position: 'topright',
    }).addTo(map.value)


    /*
    |----------------------------------------------------------------------
    | BEDAKAN DATA MASYARAKAT DAN PETUGAS
    |----------------------------------------------------------------------
    */

    if (props.user?.role === 'petugas') {

        loadPetugasMapMarkers()

    } else {

        loadMapMarkers()

    }

}


/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

onMounted(() => {

    if (
        props.user?.role === 'masyarakat' ||
        props.user?.role === 'petugas'
    ) {

        initializeMap()

    }

})


/*
|--------------------------------------------------------------------------
| UNMOUNT
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {

    if (map.value) {

        map.value.remove()

        map.value = null

    }

})


/*
|--------------------------------------------------------------------------
| CHANGE CATEGORY
|--------------------------------------------------------------------------
*/

const changeCategory = () => {

    loadMapMarkers()

}

</script>


<template>

    <Head title="Dashboard" />


    <AuthenticatedLayout>


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <template #header>

            <div>

                <h2 class="text-xl font-semibold text-gray-900">

                    Dashboard

                </h2>


                <p class="mt-1 text-sm text-gray-500">

                    Ringkasan aktivitas UrbanEye

                </p>

            </div>

        </template>


        <!-- ========================================================= -->
        <!-- ADMIN -->
        <!-- ========================================================= -->

        <div
            v-if="user?.role === 'admin'"
            class="space-y-6"
        >


            <!-- STATISTICS -->

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">


                <!-- TOTAL LAPORAN -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100">

                            <ClipboardList
                                :size="24"
                                class="text-green-600"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Total Laporan
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ stats?.total ?? 0 }}
                            </p>


                            <p class="mt-1 text-xs text-green-600">
                                Total seluruh laporan
                            </p>

                        </div>

                    </div>

                </div>


                <!-- LAPORAN MENDESAK -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100">

                            <AlertTriangle
                                :size="24"
                                class="text-red-500"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Laporan Mendesak
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-red-500">
                                {{ stats?.mendesak ?? 0 }}
                            </p>


                            <p class="mt-1 text-xs text-gray-500">
                                Perlu segera ditindaklanjuti
                            </p>

                        </div>

                    </div>

                </div>


                <!-- DALAM PROSES -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-100">

                            <Clock3
                                :size="24"
                                class="text-yellow-500"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Dalam Proses
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ stats?.diproses ?? 0 }}
                            </p>


                            <p class="mt-1 text-xs text-gray-500">
                                Sedang ditangani
                            </p>

                        </div>

                    </div>

                </div>


                <!-- SELESAI -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-cyan-100">

                            <CircleCheck
                                :size="24"
                                class="text-cyan-500"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Selesai
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ stats?.selesai ?? 0 }}
                            </p>


                            <p class="mt-1 text-xs text-green-600">
                                Laporan terselesaikan
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- LAPORAN TERBARU + PIE -->

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.5fr_1fr]">


                <!-- LAPORAN TERBARU -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-4 flex items-center justify-between">

                        <h3 class="font-semibold text-gray-900">
                            Laporan Terbaru
                        </h3>


                        <Link
                            :href="route('admin.reports.index')"
                            class="text-sm font-medium text-green-600"
                        >
                            Lihat Semua
                        </Link>

                    </div>


                    <div
                        v-if="laporan_terbaru.length"
                        class="divide-y divide-gray-100"
                    >

                        <div
                            v-for="laporan in laporan_terbaru"
                            :key="laporan.id"
                            class="flex items-center gap-4 py-3"
                        >

                            <div class="h-20 w-28 shrink-0 overflow-hidden rounded-xl bg-gray-100">

                                <img
                                    v-if="laporan.photo"
                                    :src="`/storage/${laporan.photo}`"
                                    :alt="laporan.title"
                                    class="h-full w-full object-cover"
                                />

                            </div>


                            <div class="min-w-0 flex-1">

                                <p class="truncate font-medium text-gray-900">
                                    {{ laporan.title }}
                                </p>


                                <p class="mt-1 text-sm text-gray-500">
                                    {{ laporan.address ?? 'Alamat belum tersedia' }}
                                </p>


                                <p class="mt-1 text-xs text-gray-400">
                                    {{ laporan.created_at }}
                                </p>

                            </div>


                            <span
                                class="shrink-0 rounded-full px-4 py-2 text-xs font-medium capitalize"
                                :class="statusClass(laporan.status)"
                            >
                                {{ laporan.status }}
                            </span>

                        </div>

                    </div>


                    <div
                        v-else
                        class="flex h-40 items-center justify-center text-sm text-gray-400"
                    >
                        Belum ada laporan.
                    </div>

                </div>


                <!-- PIE -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <h3 class="font-semibold text-gray-900">
                        Laporan per Kategori
                    </h3>


                    <div
                        v-if="kategoriChart.length"
                        class="mt-5 flex flex-col items-center"
                    >

                        <div
                            class="relative h-[210px] w-[210px] rounded-full"
                            :style="pieStyle"
                        >

                            <div
                                class="absolute inset-[48px] flex items-center justify-center rounded-full bg-white"
                            >

                                <div class="text-center">

                                    <p class="text-2xl font-semibold text-gray-900">
                                        {{ totalKategori }}
                                    </p>


                                    <p class="text-xs text-gray-400">
                                        Total Laporan
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="mt-5 w-full space-y-2">

                            <div
                                v-for="item in kategoriChart"
                                :key="item.name"
                                class="flex items-center justify-between"
                            >

                                <div class="flex items-center gap-2">

                                    <span
                                        class="h-3 w-3 rounded-full"
                                        :style="{
                                            backgroundColor: item.color
                                        }"
                                    ></span>


                                    <span class="text-xs text-gray-600">
                                        {{ item.name }}
                                    </span>

                                </div>


                                <span class="text-xs font-medium text-gray-900">
                                    {{ item.total }}
                                </span>

                            </div>

                        </div>

                    </div>


                    <div
                        v-else
                        class="flex h-[290px] items-center justify-center text-sm text-gray-400"
                    >
                        Belum ada data kategori.
                    </div>

                </div>

            </div>


            <!-- AKTIVITAS PETUGAS -->

            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <div class="mb-4">

                    <h3 class="font-semibold text-gray-900">
                        Aktivitas Petugas
                    </h3>

                </div>


                <div
                    v-if="aktivitas_petugas.length"
                    class="divide-y divide-gray-100"
                >

                    <div
                        v-for="aktivitas in aktivitas_petugas"
                        :key="aktivitas.id"
                        class="flex items-center gap-4 py-4"
                    >

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gray-100">

                            <UserCircle
                                :size="27"
                                class="text-gray-500"
                            />

                        </div>


                        <div class="min-w-0 flex-1">

                            <p class="font-medium text-gray-900">
                                {{ aktivitas.user?.name ?? 'Petugas' }}
                            </p>


                            <p class="mt-1 text-sm text-gray-500">

                                {{ aktivitas.description }}
                                {{ aktivitas.report }}

                            </p>

                        </div>


                        <span class="text-xs text-gray-400">
                            {{ aktivitas.time }}
                        </span>

                    </div>

                </div>


                <div
                    v-else
                    class="flex h-28 items-center justify-center text-sm text-gray-400"
                >
                    Belum ada aktivitas petugas.
                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- PETUGAS -->
        <!-- ========================================================= -->

        <div
            v-else-if="user?.role === 'petugas'"
            class="space-y-6"
        >


            <!-- SELAMAT DATANG -->

            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h1 class="text-[18px] font-semibold text-gray-800">

                    Selamat datang, {{ user?.name }}!

                </h1>


                <p class="mt-2 text-sm text-gray-500">

                    Berikut ringkasan pekerjaan Anda hari ini.

                </p>

            </div>


            <!-- 4 STATISTIK -->

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">


                <!-- TUGAS HARI INI -->

                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-100">

                            <ClipboardList
                                class="h-6 w-6 text-green-600"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Tugas Hari Ini
                            </p>


                            <p class="mt-1 text-3xl font-bold text-green-600">
                                {{ stats?.tugas_hari_ini ?? 12 }}
                            </p>


                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas yang harus ditangani
                            </p>

                        </div>

                    </div>

                </div>


                <!-- SEDANG DIKERJAKAN -->

                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-yellow-100">

                            <Hourglass
                                class="h-6 w-6 text-yellow-500"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Sedang Dikerjakan
                            </p>


                            <p class="mt-1 text-3xl font-bold text-yellow-500">
                                {{ stats?.diproses ?? 4 }}
                            </p>


                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas dalam proses penanganan
                            </p>

                        </div>

                    </div>

                </div>


                <!-- SUDAH SELESAI -->

                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100">

                            <CheckCircle2
                                class="h-6 w-6 text-blue-600"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Sudah Selesai
                            </p>


                            <p class="mt-1 text-3xl font-bold text-blue-500">
                                {{ stats?.sudah_selesai ?? 28 }}
                            </p>


                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas yang telah diselesaikan
                            </p>

                        </div>

                    </div>

                </div>


                <!-- MENUNGGU VERIFIKASI -->

                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-red-100">

                            <AlertCircle
                                class="h-6 w-6 text-red-600"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Menunggu Verifikasi
                            </p>


                            <p class="mt-1 text-3xl font-bold text-red-500">
                                {{ stats?.menunggu_verifikasi ?? 3 }}
                            </p>


                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas menunggu verifikasi admin
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TUGAS PRIORITAS + AKTIVITAS HARI INI -->

            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">


                <!-- TUGAS PRIORITAS -->

                <div class="rounded-xl bg-white shadow-sm">

                    <div class="flex items-center justify-between border-b border-gray-200 p-5">

                        <h3 class="font-semibold text-gray-800">
                            Tugas Prioritas
                        </h3>


                        <Link
                            :href="route('petugas.tasks.index')"
                            class="text-sm font-medium text-green-500 underline"
                        >
                            Lihat semua
                        </Link>

                    </div>


                    <div class="space-y-6 p-5">


                        <!-- TUGAS 1 -->

                        <div class="flex items-start gap-3">

                            <img
                                src="/storage/reports/default.jpg"
                                alt="Tumpukan sampah"
                                class="h-10 w-10 shrink-0 rounded-lg object-cover"
                            />


                            <div class="min-w-0 flex-1">

                                <h4 class="font-medium text-gray-800">
                                    Tumpukan sampah di pinggir jalan
                                </h4>


                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-500">

                                    <MapPin
                                        class="h-3.5 w-3.5 shrink-0"
                                    />

                                    <span>
                                        Jl. Margonda Raya, Depok
                                    </span>

                                </div>


                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">

                                    <CalendarDays
                                        class="h-3.5 w-3.5 shrink-0"
                                    />

                                    <span>
                                        15 Jul 2026 | 09:30
                                    </span>

                                </div>

                            </div>


                            <div class="flex shrink-0 flex-col items-end gap-2">

                                <span class="rounded-full bg-red-100 px-3 py-1 text-[10px] font-medium text-red-600">
                                    Mendesak
                                </span>


                                <Link
                                    :href="route('petugas.tasks.show', tugas?.[0]?.id)"
                                    class="rounded-md border border-green-500 px-3 py-1 text-[10px] font-medium text-green-600 hover:bg-green-50"
                                >
                                    Lihat Detail
                                </Link>

                            </div>

                        </div>


                        <!-- TUGAS 2 -->

                        <div class="flex items-start gap-3">

                            <img
                                src="/storage/reports/jalanberlubang.jpg"
                                alt="Jalan berlubang"
                                class="h-10 w-10 shrink-0 rounded-lg object-cover"
                            />


                            <div class="min-w-0 flex-1">

                                <h4 class="font-medium text-gray-800">
                                    Jalan berlubang
                                </h4>


                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-500">

                                    <MapPin
                                        class="h-3.5 w-3.5 shrink-0"
                                    />

                                    <span>
                                        Jl. Bogor Raya, Depok
                                    </span>

                                </div>


                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">

                                    <CalendarDays
                                        class="h-3.5 w-3.5 shrink-0"
                                    />

                                    <span>
                                        15 Jul 2026 | 09:15
                                    </span>

                                </div>

                            </div>


                            <div class="flex shrink-0 flex-col items-end gap-2">

                                <span class="rounded-full bg-orange-100 px-3 py-1 text-[10px] font-medium text-orange-600">
                                    Sedang
                                </span>


                                <Link
                                    :href="route('petugas.tasks.show', tugas?.[1]?.id)"
                                    class="rounded-md border border-green-500 px-3 py-1 text-[10px] font-medium text-green-600 hover:bg-green-50"
                                >
                                    Lihat Detail
                                </Link>

                            </div>

                        </div>


                        <!-- TUGAS 3 -->

                        <div class="flex items-start gap-3">

                            <img
                                src="/storage/reports/drainase.jpg"
                                alt="Drainase"
                                class="h-10 w-10 shrink-0 rounded-lg object-cover"
                            />


                            <div class="min-w-0 flex-1">

                                <h4 class="font-medium text-gray-800">
                                    Saluran drainase tersumbat
                                </h4>


                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-500">

                                    <MapPin
                                        class="h-3.5 w-3.5 shrink-0"
                                    />

                                    <span>
                                        Jl. Nusa Indah, Depok
                                    </span>

                                </div>


                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">

                                    <CalendarDays
                                        class="h-3.5 w-3.5 shrink-0"
                                    />

                                    <span>
                                        14 Jul 2026 | 16:45
                                    </span>

                                </div>

                            </div>


                            <div class="flex shrink-0 flex-col items-end gap-2">

                                <span class="rounded-full bg-blue-100 px-3 py-1 text-[10px] font-medium text-blue-600">
                                    Rendah
                                </span>


                                <Link
                                    :href="route('petugas.tasks.show', tugas?.[2]?.id)"
                                    class="rounded-md border border-green-500 px-3 py-1 text-[10px] font-medium text-green-600 hover:bg-green-50"
                                >
                                    Lihat Detail
                                </Link>

                            </div>

                        </div>


                    </div>

                </div>


                <!-- AKTIVITAS HARI INI -->

                <div class="rounded-xl bg-white shadow-sm">

                    <div class="flex items-center justify-between border-b border-gray-200 p-5">

                        <h3 class="font-semibold text-gray-800">
                            Aktivitas Hari Ini
                        </h3>


                        <Link
                            :href="route('petugas.history.index')"
                            class="text-sm font-medium text-green-500 underline"
                        >
                            Lihat semua
                        </Link>

                    </div>


                    <div class="px-5 py-3">


                        <div class="relative flex items-center gap-3 py-3">

                            <div class="absolute bottom-[-1px] left-[10px] top-[31px] w-px bg-gray-200"></div>


                            <div class="relative z-10 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-100">

                                <CheckCircle2
                                    class="h-3.5 w-3.5 text-green-600"
                                />

                            </div>


                            <p class="flex-1 text-sm text-gray-600">
                                Sampah di Jl. Melati Raya berhasil dibersihkan
                            </p>


                            <span class="shrink-0 text-xs text-gray-400">
                                10:30
                            </span>

                        </div>


                        <div class="relative flex items-center gap-3 py-3">

                            <div class="absolute bottom-[-1px] left-[10px] top-[31px] w-px bg-gray-200"></div>


                            <div class="relative z-10 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-100">

                                <CheckCircle2
                                    class="h-3.5 w-3.5 text-green-600"
                                />

                            </div>


                            <p class="flex-1 text-sm text-gray-600">
                                Drainase di Jl. Nusa Indah selesai diperbaiki
                            </p>


                            <span class="shrink-0 text-xs text-gray-400">
                                09:45
                            </span>

                        </div>


                        <div class="relative flex items-center gap-3 py-3">

                            <div class="absolute bottom-[-1px] left-[10px] top-[31px] w-px bg-gray-200"></div>


                            <div class="relative z-10 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-orange-100">

                                <Clock3
                                    class="h-3.5 w-3.5 text-orange-500"
                                />

                            </div>


                            <p class="flex-1 text-sm text-gray-600">
                                Sedang menangani jalan berlubang di Jl. Bogor
                            </p>


                            <span class="shrink-0 text-xs text-gray-400">
                                08:50
                            </span>

                        </div>


                        <div class="relative flex items-center gap-3 py-3">

                            <div class="relative z-10 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100">

                                <Info
                                    class="h-3.5 w-3.5 text-blue-500"
                                />

                            </div>


                            <p class="flex-1 text-sm text-gray-600">
                                Laporan baru diterima: lampu jalan mati di Jl. Melati
                            </p>


                            <span class="shrink-0 text-xs text-gray-400">
                                08:20
                            </span>

                        </div>


                    </div>

                </div>

            </div>


            <!-- ========================================================= -->
            <!-- PETA LAPORAN -->
            <!-- ========================================================= -->

            <div class="rounded-xl bg-white shadow-sm">


                <!-- HEADER -->

                <div class="flex items-center justify-between border-b border-gray-200 p-5">

                    <div>

                        <h2 class="text-[18px] font-semibold text-gray-800">
                            Peta Laporan
                        </h2>


                        <p class="mt-1 text-sm text-gray-500">
                            Lokasi laporan yang perlu ditangani
                        </p>

                    </div>


                    <Link
                        :href="route('petugas.map.index')"
                        class="text-sm font-medium text-green-500 underline"
                    >
                        Lihat Peta Lengkap
                    </Link>

                </div>


                <!-- PETA + LEGEND -->

                <div class="grid grid-cols-1 lg:grid-cols-[180px_1fr]">


                    <!-- LEGEND -->

                    <div class="space-y-4 border-r border-gray-100 p-5">


                        <!-- MENDESAK -->

                        <div class="flex items-center gap-3">

                            <span class="h-3 w-3 rounded-full bg-red-500"></span>


                            <div>

                                <p class="text-xs font-medium text-gray-700">
                                    Mendesak
                                </p>


                                <p class="text-[11px] text-gray-400">
                                    3 laporan
                                </p>

                            </div>

                        </div>


                        <!-- SEDANG -->

                        <div class="flex items-center gap-3">

                            <span class="h-3 w-3 rounded-full bg-orange-500"></span>


                            <div>

                                <p class="text-xs font-medium text-gray-700">
                                    Sedang
                                </p>


                                <p class="text-[11px] text-gray-400">
                                    4 laporan
                                </p>

                            </div>

                        </div>


                        <!-- RENDAH -->

                        <div class="flex items-center gap-3">

                            <span class="h-3 w-3 rounded-full bg-blue-500"></span>


                            <div>

                                <p class="text-xs font-medium text-gray-700">
                                    Rendah
                                </p>


                                <p class="text-[11px] text-gray-400">
                                    5 laporan
                                </p>

                            </div>

                        </div>


                        <!-- SELESAI -->

                        <div class="flex items-center gap-3">

                            <span class="h-3 w-3 rounded-full bg-green-500"></span>


                            <div>

                                <p class="text-xs font-medium text-gray-700">
                                    Selesai
                                </p>


                                <p class="text-[11px] text-gray-400">
                                    12 laporan
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- MAP -->

                    <div
                        ref="mapElement"
                        class="h-[280px] w-full overflow-hidden"
                    ></div>


                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- MASYARAKAT -->
        <!-- ========================================================= -->

        <div
            v-else
            class="space-y-5"
        >


            <!-- GREETING + BANNER -->

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">


                <div>

                    <h1 class="text-3xl font-semibold text-[#102A43]">
                        Halo, {{ user?.name }}!
                    </h1>


                    <p class="mt-1 text-sm text-gray-500">
                        Bersama kita wujudkan lingkungan kota yang lebih bersih dan sehat.
                    </p>

                </div>


                <div class="flex min-h-[80px] items-center gap-4 rounded-2xl bg-[#E8F5E9] px-5 py-4 lg:w-[465px]">

                    <Leaf
                        :size="48"
                        class="shrink-0 text-green-600"
                        stroke-width="1.5"
                    />


                    <div>

                        <p class="font-semibold text-green-900">
                            Setiap laporanmu berarti
                        </p>


                        <p class="mt-1 text-xs leading-5 text-gray-600">
                            Bantu kami menjaga lingkungan Depok lebih baik untuk masa depan!
                        </p>

                    </div>

                </div>

            </div>


            <!-- 4 STATISTICS -->

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">


                <!-- TOTAL -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-100">

                            <FileText
                                :size="27"
                                class="text-green-600"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Total Laporan
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-[#102A43]">
                                {{ stats?.total_laporan ?? 0 }}
                            </p>


                            <p class="mt-1 text-xs text-green-600">
                                ↑ Laporan Anda
                            </p>

                        </div>

                    </div>

                </div>


                <!-- POIN -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100">

                            <Medal
                                :size="27"
                                class="text-blue-600"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Poin Anda
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-[#102A43]">
                                {{ poin }}
                            </p>


                            <p class="mt-1 text-xs text-blue-600">
                                Poin yang Anda kumpulkan
                            </p>

                        </div>

                    </div>

                </div>


                <!-- BADGE -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-100">

                            <Award
                                :size="27"
                                class="text-purple-600"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Badge
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-[#102A43]">
                                {{ badge }}
                            </p>


                            <p class="mt-1 text-xs text-gray-500">
                                Dapatkan badge baru!
                            </p>

                        </div>

                    </div>

                </div>


                <!-- PERINGKAT -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-yellow-100">

                            <Trophy
                                :size="27"
                                class="text-yellow-500"
                                stroke-width="1.8"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Peringkat
                            </p>


                            <p class="mt-1 text-2xl font-semibold text-[#102A43]">
                                #{{ peringkat }}
                            </p>


                            <p class="mt-1 text-xs text-gray-500">
                                Peringkat Anda
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- MAP + LAPORAN TERBARU -->

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1.55fr_1fr]">


                <!-- MAP -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                        <div>

                            <h3 class="font-semibold text-[#102A43]">
                                Peta Lokasi Laporan
                            </h3>


                            <p class="mt-1 text-xs text-gray-400">
                                Lihat sebaran laporan di sekitar wilayah Anda
                            </p>

                        </div>


                        <div class="relative">

                            <select
                                v-model="selectedCategory"
                                @change="changeCategory"
                                class="h-10 w-full appearance-none rounded-xl border border-gray-200 bg-white pl-4 pr-10 text-xs text-gray-600 outline-none focus:border-green-500 sm:w-[180px]"
                            >

                                <option
                                    v-for="category in categoryOptions"
                                    :key="category"
                                    :value="category"
                                >
                                    {{ category }}
                                </option>

                            </select>


                            <ChevronDown
                                :size="15"
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                            />

                        </div>

                    </div>


                    <!-- LEAFLET -->

                    <div
                        ref="mapElement"
                        class="h-[360px] w-full overflow-hidden rounded-xl"
                    ></div>


                    <!-- LEGEND -->

                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2">


                        <div class="flex items-center gap-2">

                            <span class="h-3 w-3 rounded-full bg-yellow-500"></span>

                            <span class="text-xs text-gray-500">
                                Menunggu
                            </span>

                        </div>


                        <div class="flex items-center gap-2">

                            <span class="h-3 w-3 rounded-full bg-blue-500"></span>

                            <span class="text-xs text-gray-500">
                                Diproses
                            </span>

                        </div>


                        <div class="flex items-center gap-2">

                            <span class="h-3 w-3 rounded-full bg-green-600"></span>

                            <span class="text-xs text-gray-500">
                                Selesai
                            </span>

                        </div>


                        <div class="flex items-center gap-2">

                            <span class="h-3 w-3 rounded-full bg-red-500"></span>

                            <span class="text-xs text-gray-500">
                                Ditolak
                            </span>

                        </div>

                    </div>

                </div>


                <!-- LAPORAN TERBARU -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-4 flex items-center justify-between">

                        <h3 class="font-semibold text-[#102A43]">
                            Laporan Terbaru
                        </h3>


                        <Link
                            :href="route('masyarakat.reports.index')"
                            class="text-xs font-medium text-blue-600"
                        >
                            Lihat Semua →
                        </Link>

                    </div>


                    <div
                        v-if="laporan_terbaru.length"
                        class="divide-y divide-gray-100"
                    >

                        <div
                            v-for="laporan in laporan_terbaru"
                            :key="laporan.id"
                            class="flex gap-3 py-3"
                        >

                            <div class="h-[70px] w-[70px] shrink-0 overflow-hidden rounded-xl bg-gray-100">

                                <img
                                    v-if="laporan.photo"
                                    :src="`/storage/${laporan.photo}`"
                                    :alt="laporan.title"
                                    class="h-full w-full object-cover"
                                />

                            </div>


                            <div class="min-w-0 flex-1">

                                <div class="flex items-start justify-between gap-2">

                                    <p class="line-clamp-2 text-xs font-semibold text-[#102A43]">
                                        {{ laporan.title }}
                                    </p>


                                    <span
                                        class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-medium capitalize"
                                        :class="statusClass(laporan.status)"
                                    >
                                        {{ laporan.status }}
                                    </span>

                                </div>


                                <p class="mt-1 text-[11px] text-gray-500">
                                    {{ laporan.category?.name ?? 'Tanpa kategori' }}
                                </p>


                                <p class="mt-1 flex items-center gap-1 text-[10px] text-gray-400">

                                    <MapPin :size="11" />

                                    {{ laporan.address ?? 'Lokasi belum tersedia' }}

                                </p>


                                <p class="mt-1 text-[10px] text-gray-400">
                                    {{ laporan.created_at }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        v-else
                        class="flex h-[350px] items-center justify-center text-sm text-gray-400"
                    >
                        Belum ada laporan.
                    </div>

                </div>

            </div>


            <!-- BOTTOM -->

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1fr_1.35fr]">


                <!-- LAPORKAN MASALAH -->

                <div class="relative overflow-hidden rounded-2xl bg-[#E8F5E9] p-6">

                    <div class="relative z-10 max-w-[65%]">

                        <h3 class="text-lg font-semibold text-[#173B2A]">
                            Laporkan Masalah Lingkungan
                        </h3>


                        <p class="mt-2 text-xs leading-5 text-gray-600">

                            Jadilah bagian dari perubahan. Laporkan masalah
                            lingkungan di sekitar Anda dengan mudah dan cepat.

                        </p>


                        <Link
                            :href="route('masyarakat.reports.create')"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#168344] px-4 py-2.5 text-xs font-medium text-white transition hover:bg-[#116D38]"
                        >

                            <Plus :size="15" />

                            Laporkan Sekarang

                            <ArrowRight :size="14" />

                        </Link>

                    </div>


                    <div class="absolute -right-2 bottom-0 text-green-700/20">

                        <Leaf :size="150" />

                    </div>

                </div>


                <!-- LEADERBOARD -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-5 flex items-center justify-between">

                        <h3 class="font-semibold text-[#102A43]">
                            Leaderboard Mingguan
                        </h3>


                        <Link
                            :href="route('masyarakat.leaderboard.index')"
                            class="text-[10px] font-medium text-blue-600"
                        >
                            Lihat Semua →
                        </Link>

                    </div>


                    <div
                        v-if="leaderboard.length"
                        class="grid grid-cols-3 gap-3"
                    >

                        <div
                            v-for="(item, index) in leaderboard.slice(0, 3)"
                            :key="item.id ?? index"
                            class="text-center"
                        >

                            <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full bg-gray-100">

                                <Trophy
                                    :size="17"
                                    :class="[
                                        index === 0
                                            ? 'text-yellow-500'
                                            : index === 1
                                                ? 'text-gray-400'
                                                : 'text-orange-500'
                                    ]"
                                />

                            </div>


                            <p class="mt-2 truncate text-[10px] font-medium text-gray-700">
                                {{ item.name }}
                            </p>


                            <p class="mt-1 text-[10px] text-blue-500">
                                {{ item.points }} poin
                            </p>

                        </div>

                    </div>


                    <div
                        v-else
                        class="flex h-[90px] items-center justify-center text-xs text-gray-400"
                    >
                        Leaderboard akan tersedia saat fitur gamifikasi selesai.
                    </div>

                </div>

            </div>

        </div>


    </AuthenticatedLayout>

</template>