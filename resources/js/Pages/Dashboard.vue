<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, onMounted, ref, } from 'vue'
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
        type: String,
        default: 'Belum Ada',
    },

    badge_count: {
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


const formatNumber = (value) => Number(value ?? 0).toLocaleString('id-ID')

const formatDate = (value) => {
    if (!value) return 'Tanggal tidak tersedia'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return String(value)
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(date)
}


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
 
const statusClass = (status) => {

    return {
        menunggu: 'bg-yellow-100 text-yellow-700',
        diproses: 'bg-blue-100 text-blue-700',
        selesai: 'bg-green-100 text-green-700',
        ditolak: 'bg-red-100 text-red-700',
    }[status] ?? 'bg-gray-100 text-gray-600'

}

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

    const normalizedStatus = String(status ?? '').trim().toLowerCase()

    return {
        menunggu: '#F59E0B',
        diproses: '#3B82F6',
        selesai: '#16A34A',  
        ditolak: '#EF4444',
    }[normalizedStatus] ?? '#6B7280'

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

    if (props.user?.role === 'petugas') {

        loadPetugasMapMarkers()

    } else {

        loadMapMarkers()

    }

}

onMounted(() => {

    if (
        props.user?.role === 'masyarakat' ||
        props.user?.role === 'petugas'
    ) {

        initializeMap()

    }

})

onBeforeUnmount(() => {

    if (map.value) {

        map.value.remove()

        map.value = null

    }

})

const changeCategory = () => {

    loadMapMarkers()

}

</script>


<template>

    <Head title="Dashboard" />


    <AuthenticatedLayout>
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

        <div v-if="user?.role === 'admin'" class="space-y-6">

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100">
                            <ClipboardList :size="24" class="text-green-600" stroke-width="1.8" />
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

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100">
                            <AlertTriangle :size="24" class="text-red-500" stroke-width="1.8"/>
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

                <di class="rounded-2xl bg-white p-5 shadow-sm">

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

                </di>

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

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.5fr_1fr]">

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">
                            Laporan Terbaru
                        </h3>

                        <Link :href="route('admin.reports.index')" class="text-sm font-medium text-green-600">
                            Lihat Semua
                        </Link>
                    </div>

                    <div v-if="laporan_terbaru.length" class="divide-y divide-gray-100" >
                        <div v-for="laporan in laporan_terbaru" :key="laporan.id"class="flex items-center gap-4 py-3">
                            <div class="h-20 w-28 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                <img v-if="laporan.photo" :src="`/storage/${laporan.photo}`" :alt="laporan.title" class="h-full w-full object-cover"/>
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

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900">
                        Laporan per Kategori
                    </h3>

                    <div v-if="kategoriChart.length" class="mt-5 flex flex-col items-center">
                        <div class="relative h-[210px] w-[210px] rounded-full" :style="pieStyle">
                            <div class="absolute inset-[48px] flex items-center justify-center rounded-full bg-white">
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
                            <div v-for="item in kategoriChart" :key="item.name" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="h-3 w-3 rounded-full"
                                        :style="{
                                            backgroundColor: item.color
                                        }">
                                    </span>

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


        <div
            v-else-if="user?.role === 'petugas'"
            class="space-y-6"
        >
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h1 class="text-[18px] font-semibold text-gray-800">
                    Selamat datang, {{ user?.name }}!
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Berikut ringkasan pekerjaan Anda hari ini.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-100">
                            <ClipboardList class="h-6 w-6 text-green-600" />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Tugas Hari Ini</p>
                            <p class="mt-1 text-3xl font-bold text-green-600">
                                {{ stats?.tugas_hari_ini ?? 0 }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas yang harus ditangani
                            </p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-yellow-100">
                            <Hourglass class="h-6 w-6 text-yellow-500" />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
                            <p class="mt-1 text-3xl font-bold text-yellow-500">
                                {{ stats?.diproses ?? 0 }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas dalam proses penanganan
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100">
                            <CheckCircle2 class="h-6 w-6 text-blue-600" />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Sudah Selesai</p>
                            <p class="mt-1 text-3xl font-bold text-blue-500">
                                {{ stats?.sudah_selesai ?? 0 }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas yang telah diselesaikan
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-red-100">
                            <AlertCircle class="h-6 w-6 text-red-600" />
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
                            <p class="mt-1 text-3xl font-bold text-red-500">
                                {{ stats?.menunggu_verifikasi ?? 0 }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                Total tugas menunggu verifikasi admin
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
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

                    <div v-if="tugas?.length" class="space-y-6 p-5">
                        <div
                            v-for="task in tugas.slice(0, 3)"
                            :key="task.id"
                            class="flex items-start gap-3"
                        >
                            <img
                                v-if="task.report?.photo"
                                :src="`/storage/${task.report.photo}`"
                                :alt="task.report?.title ?? 'Foto laporan'"
                                class="h-10 w-10 shrink-0 rounded-lg object-cover"
                            />

                            <div
                                v-else
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                            >
                                <FileText class="h-5 w-5 text-gray-400" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <h4 class="font-medium text-gray-800">
                                    {{ task.report?.title ?? 'Laporan tanpa judul' }}
                                </h4>

                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-500">
                                    <MapPin class="h-3.5 w-3.5 shrink-0" />
                                    <span>
                                        {{ task.report?.address ?? 'Alamat belum tersedia' }}
                                    </span>
                                </div>

                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                                    <CalendarDays class="h-3.5 w-3.5 shrink-0" />
                                    <span>{{ formatDate(task.created_at) }}</span>
                                </div>

                                <span
                                    class="mt-2 inline-block rounded-full px-3 py-1 text-[10px] font-medium capitalize"
                                    :class="statusClass(task.status)"
                                >
                                    {{ task.status ?? 'Status belum tersedia' }}
                                </span>
                            </div>

                            <Link
                                :href="route('petugas.tasks.show', task.id)"
                                class="shrink-0 rounded-md border border-green-500 px-3 py-1 text-[10px] font-medium text-green-600 hover:bg-green-50"
                            >
                                Lihat Detail
                            </Link>
                        </div>
                    </div>

                    <div v-else class="p-8 text-center">
                        <ClipboardList class="mx-auto h-9 w-9 text-gray-300" />
                        <p class="mt-3 text-sm font-medium text-gray-600">
                            Belum ada tugas
                        </p>
                        <p class="mt-1 text-sm text-gray-400">
                            Tugas akan muncul setelah ditugaskan kepada Anda.
                        </p>
                    </div>
                </div>

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

                    <div v-if="aktivitas_petugas?.length" class="px-5 py-3">
                        <div
                            v-for="(aktivitas, index) in aktivitas_petugas"
                            :key="aktivitas.id ?? index"
                            class="flex items-start gap-3 py-3"
                        >
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100">
                                <CheckCircle2 class="h-4 w-4 text-green-600" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600">
                                    {{ aktivitas.description ?? aktivitas.deskripsi ?? 'Aktivitas' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    {{ formatDate(aktivitas.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-8 text-center">
                        <Clock3 class="mx-auto h-9 w-9 text-gray-300" />
                        <p class="mt-3 text-sm font-medium text-gray-600">
                            Belum ada aktivitas
                        </p>
                        <p class="mt-1 text-sm text-gray-400">
                            Aktivitas akan muncul setelah tercatat di sistem.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm">
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

                <div class="grid grid-cols-1 lg:grid-cols-[180px_1fr]">
                    <div class="space-y-4 border-r border-gray-100 p-5">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                            <p class="text-xs font-medium text-gray-700">Menunggu</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                            <p class="text-xs font-medium text-gray-700">Diproses</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-green-600"></span>
                            <p class="text-xs font-medium text-gray-700">Selesai</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-red-500"></span>
                            <p class="text-xs font-medium text-gray-700">Ditolak</p>
                        </div>
                    </div>

                    <div
                        ref="mapElement"
                        class="h-[280px] w-full overflow-hidden"
                    ></div>
                </div>
            </div>
        </div>
           
        <div v-else class="space-y-6">
            <section class="relative isolate min-h-[156px] overflow-hidden rounded-[24px] border border-emerald-100 bg-gradient-to-r from-[#EAF8F0] via-[#E9F7FA] to-[#DCEFFC] shadow-sm sm:min-h-[164px]">
                <img
                    src="/images/urbaneye-city-hero.png"
                    alt="Ilustrasi perkotaan hijau"
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 right-0 h-full w-[58%] object-contain object-right-bottom opacity-70 sm:w-[52%] sm:opacity-100"
                />
                <div class="pointer-events-none absolute inset-y-0 right-[28%] z-0 w-[28%] bg-gradient-to-r from-[#EAF8F0] via-[#E9F7FA]/85 to-transparent"></div>
                <div class="relative z-10 flex min-h-[156px] items-center px-5 py-6 sm:min-h-[164px] sm:px-7">
                    <div class="max-w-[690px] sm:max-w-[62%]">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-bold tracking-tight text-[#102A43] sm:text-3xl lg:text-4xl">
                                Halo, <span class="text-[#1B5E20]">{{ user?.name }}</span>!
                            </h1>
                        </div>
                        <p class="mt-2 max-w-xl text-sm font-medium leading-5 text-[#344B5C] sm:text-base sm:leading-6">
                            Bersama kita wujudkan lingkungan kota yang lebih bersih dan sehat.
                        </p>
                        <p class="mt-1.5 max-w-xl text-xs leading-5 text-[#526777] sm:text-sm">
                            Setiap laporan kecilmu membawa perubahan besar untuk masa depan yang lebih baik.
                        </p>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <div class="relative flex min-h-[112px] items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                            <FileText :size="25" :stroke-width="1.8" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-[#102A43]">{{ stats?.total_laporan ?? 0 }}</p>
                            <p class="mt-1 text-xs text-gray-500">Laporan yang kamu kirim</p>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <div class="relative flex min-h-[112px] items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                            <Medal :size="25" :stroke-width="1.8" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500">Poin Anda</p>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-[#102A43]">{{ formatNumber(poin) }}</p>
                            <p class="mt-1 text-xs text-gray-500">Total poin bersih terkumpul</p>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <div class="relative flex min-h-[112px] items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-purple-100 text-purple-700">
                            <Award :size="25" :stroke-width="1.8" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500">Badge Saya</p>
                            <p class="mt-2 line-clamp-2 break-words text-lg font-bold leading-tight tracking-tight text-[#102A43] sm:text-xl" :title="badge">{{ badge || 'Belum Ada' }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ badge_count }} badge diperoleh</p>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                    <div class="relative flex min-h-[112px] items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                            <Trophy :size="25" :stroke-width="1.8" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500">Peringkat</p>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-[#102A43]">{{ peringkat === '-' ? '-' : `#${peringkat}` }}</p>
                            <p class="mt-1 text-xs text-gray-500">Peringkat poin sepanjang waktu</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-5 xl:grid-cols-[1.35fr_1fr]">
                <div class="relative z-0 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-green-50 text-green-700">
                                    <MapPin :size="19" />
                                </div>
                                <h2 class="font-semibold text-[#102A43]">Peta Lokasi Laporan</h2>
                            </div>
                            <p class="mt-2 text-xs leading-5 text-gray-500">Jelajahi sebaran laporan lingkungan di sekitar kota.</p>
                        </div>
                        <div class="relative w-full sm:w-[190px]">
                            <select
                                v-model="selectedCategory"
                                @change="changeCategory"
                                class="h-10 w-full appearance-none rounded-xl border border-gray-200 bg-white pl-3 pr-9 text-xs text-gray-600 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                            >
                                <option v-for="category in categoryOptions" :key="category" :value="category">
                                    {{ category }}
                                </option>
                            </select>
                            <ChevronDown :size="15" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        </div>
                    </div>

                    <div
                        ref="mapElement"
                        class="relative z-0 isolate h-[300px] w-full overflow-hidden rounded-xl sm:h-[360px]"
                    ></div>

                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-gray-100 pt-4">
                        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background-color: #F59E0B"></span><span class="text-xs text-gray-500">Menunggu</span></div>
                        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background-color: #3B82F6"></span><span class="text-xs text-gray-500">Diproses</span></div>
                        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background-color: #16A34A"></span><span class="text-xs text-gray-500">Selesai</span></div>
                        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background-color: #EF4444"></span><span class="text-xs text-gray-500">Ditolak</span></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-[#102A43]">Laporan Terbaru</h2>
                            <p class="mt-1 text-xs text-gray-500">Tiga laporan terakhir yang kamu kirim.</p>
                        </div>
                        <Link :href="route('masyarakat.reports.index')" class="shrink-0 text-xs font-semibold text-green-700 transition hover:text-green-900">
                            Lihat Semua <ArrowRight :size="13" class="ml-1 inline" />
                        </Link>
                    </div>

                    <div v-if="laporan_terbaru.length" class="divide-y divide-gray-100">
                        <div v-for="laporan in laporan_terbaru.slice(0, 3)" :key="laporan.id" class="flex gap-3 py-4 first:pt-1 last:pb-1">
                            <div class="h-[76px] w-[76px] shrink-0 overflow-hidden rounded-xl bg-green-50 ring-1 ring-black/5">
                                <img v-if="laporan.photo" :src="`/storage/${laporan.photo}`" :alt="laporan.title" class="h-full w-full object-cover" loading="lazy" />
                                <div v-else class="flex h-full w-full items-center justify-center text-green-700"><Leaf :size="25" /></div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="line-clamp-2 text-sm font-semibold leading-5 text-[#102A43]">{{ laporan.title }}</p>
                                    <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-semibold capitalize" :class="statusClass(laporan.status)">{{ laporan.status }}</span>
                                </div>
                                <p class="mt-1.5 text-xs text-gray-500">{{ laporan.category?.name ?? 'Tanpa kategori' }}</p>
                                <p class="mt-1.5 flex items-start gap-1 text-[11px] leading-4 text-gray-400">
                                    <MapPin :size="12" class="mt-0.5 shrink-0" />
                                    <span class="line-clamp-1">{{ laporan.address ?? 'Lokasi belum tersedia' }}</span>
                                </p>
                                <p class="mt-1.5 text-[10px] text-gray-400">{{ formatDate(laporan.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex min-h-[300px] flex-col items-center justify-center px-4 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-green-700"><FileText :size="25" /></div>
                        <p class="mt-3 text-sm font-semibold text-gray-700">Belum ada laporan</p>
                        <p class="mt-1 max-w-[220px] text-xs leading-5 text-gray-500">Laporan pertamamu bisa membantu menjaga lingkungan sekitar.</p>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-5 xl:grid-cols-[0.95fr_1.25fr]">
                <div class="relative isolate overflow-hidden rounded-2xl border border-green-100 bg-gradient-to-br from-[#E8F5E9] via-[#F2FAF0] to-[#D9F0D6] p-6 sm:p-7">
                    <div class="pointer-events-none absolute -bottom-7 -right-5 text-green-800/10"><Leaf :size="170" :stroke-width="1.1" /></div>
                    <img
                        src="/images/urbaneye-report-cta.png"
                        alt=""
                        aria-hidden="true"
                        class="pointer-events-none absolute inset-y-0 right-0 z-0 h-full w-[38%] object-cover object-center opacity-90 sm:w-[40%]"
                    />
                    <div class="pointer-events-none absolute inset-y-0 right-[18%] z-0 w-[50%] bg-gradient-to-r from-[#F2FAF0] via-[#F2FAF0]/55 to-transparent"></div>
                    <div class="relative z-10 max-w-[72%] sm:max-w-[68%]">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/80 px-3 py-1 text-[10px] font-bold tracking-wide text-green-800"><Leaf :size="12" /> MULAI DARI SEKITARMU</span>
                        <h2 class="mt-4 text-xl font-bold leading-7 text-[#173B2A]">Ada masalah lingkungan yang kamu temukan?</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Jangan biarkan begitu saja. Laporkan dengan foto dan lokasi agar petugas bisa menindaklanjutinya.</p>
                        <Link :href="route('masyarakat.reports.create')" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#168344] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#116D38]">
                            <Plus :size="16" /> Laporkan Sekarang <ArrowRight :size="15" />
                        </Link>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><Trophy :size="19" /></div>
                                <h2 class="font-semibold text-[#102A43]">Leaderboard Mingguan</h2>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Top 3 kontributor berdasarkan poin minggu ini.</p>
                        </div>
                        <Link :href="route('masyarakat.leaderboard.index')" class="shrink-0 text-xs font-semibold text-green-700 transition hover:text-green-900">
                            Lihat Semua <ArrowRight :size="13" class="ml-1 inline" />
                        </Link>
                    </div>

                    <div v-if="leaderboard.length" class="space-y-3">
                        <div v-for="(item, index) in leaderboard.slice(0, 3)" :key="item.id ?? index" class="flex items-center gap-3 rounded-xl border px-3 py-3 sm:px-4" :class="index === 0 ? 'border-amber-100 bg-amber-50/70' : 'border-gray-100 bg-gray-50/70'">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold" :class="index === 0 ? 'bg-amber-400 text-white' : index === 1 ? 'bg-slate-300 text-slate-700' : 'bg-orange-200 text-orange-800'">
                                <Trophy v-if="index === 0" :size="18" />
                                <span v-else>{{ index + 1 }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-[#102A43]">{{ item.name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">Peringkat {{ index + 1 }} minggu ini</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-green-800">{{ formatNumber(item.points) }}</p>
                                <p class="text-[10px] text-gray-500">poin</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex min-h-[170px] flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50/70 px-4 text-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-50 text-amber-600"><Trophy :size="21" /></div>
                        <p class="mt-3 text-sm font-semibold text-gray-700">Belum ada perolehan poin minggu ini</p>
                        <p class="mt-1 max-w-sm text-xs leading-5 text-gray-500">Jadilah salah satu kontributor teratas dengan mengirim laporan yang valid.</p>
                    </div>
                </div>
            </section>
        </div>

    </AuthenticatedLayout>
</template>
