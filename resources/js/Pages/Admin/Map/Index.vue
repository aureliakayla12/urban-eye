<script setup>
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import {
    Map as MapIcon,
    Search,
    RotateCcw,
    MapPin,
    Navigation,
    FileText,
    Clock,
    CheckCircle2,
    LoaderCircle,
    XCircle,
    LocateFixed,
} from 'lucide-vue-next'

import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
    reports: {
        type: Array,
        default: () => [],
    },

    categories: {
        type: Array,
        default: () => [],
    },

    districts: {
        type: Array,
        default: () => [],
    },

    villages: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const selectedCategory = ref('')
const selectedStatus = ref('')
const selectedDistrict = ref('')
const selectedVillage = ref('')

const mapContainer = ref(null)

let map = null
let markersLayer = null

const userLocation = ref(null)
const locationLoading = ref(false)
const locationError = ref('')

const radius = ref(0)

const statusOptions = [
    {
        value: 'menunggu',
        label: 'Menunggu',
    },
    {
        value: 'diproses',
        label: 'Diproses',
    },
    {
        value: 'selesai',
        label: 'Selesai',
    },
    {
        value: 'ditolak',
        label: 'Ditolak',
    },
]

const filteredVillages = computed(() => {
    if (!selectedDistrict.value) {
        return props.villages
    }

    return props.villages.filter(
        village => String(village.district_id) === String(selectedDistrict.value)
    )
})

const filteredReports = computed(() => {
    const keyword = search.value.trim().toLowerCase()

    return props.reports.filter(report => {
        const matchesSearch =
            !keyword ||
            report.title?.toLowerCase().includes(keyword) ||
            report.address?.toLowerCase().includes(keyword) ||
            report.category?.name?.toLowerCase().includes(keyword) ||
            report.district?.name?.toLowerCase().includes(keyword) ||
            report.village?.name?.toLowerCase().includes(keyword)

        const matchesCategory =
            !selectedCategory.value ||
            String(report.category?.id) === String(selectedCategory.value)

        const matchesStatus =
            !selectedStatus.value ||
            report.status === selectedStatus.value

        const matchesDistrict =
            !selectedDistrict.value ||
            String(report.district?.id) === String(selectedDistrict.value)

        const matchesVillage =
            !selectedVillage.value ||
            String(report.village?.id) === String(selectedVillage.value)

        const matchesRadius =
            !radius.value ||
            !userLocation.value ||
            calculateDistance(
                userLocation.value.latitude,
                userLocation.value.longitude,
                report.latitude,
                report.longitude
            ) <= Number(radius.value)

        return (
            matchesSearch &&
            matchesCategory &&
            matchesStatus &&
            matchesDistrict &&
            matchesVillage &&
            matchesRadius
        )
    })
})

const summary = computed(() => {
    const reports = filteredReports.value

    return {
        total: reports.length,
        waiting: reports.filter(report => report.status === 'menunggu').length,
        processing: reports.filter(report => report.status === 'diproses').length,
        completed: reports.filter(report => report.status === 'selesai').length,
        rejected: reports.filter(report => report.status === 'ditolak').length,
    }
})

function resetFilters() {
    search.value = ''
    selectedCategory.value = ''
    selectedStatus.value = ''
    selectedDistrict.value = ''
    selectedVillage.value = ''
    radius.value = 0
}

function handleDistrictChange() {
    selectedVillage.value = ''
}

function getStatusLabel(status) {
    const option = statusOptions.find(item => item.value === status)

    return option ? option.label : status
}

function getStatusClass(status) {
    switch (status) {
        case 'menunggu':
            return 'bg-yellow-100 text-yellow-700'

        case 'diproses':
            return 'bg-blue-100 text-blue-700'

        case 'selesai':
            return 'bg-green-100 text-green-700'

        case 'ditolak':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }
}

function getMarkerClass(status) {
    switch (status) {
        case 'menunggu':
            return 'marker-waiting'

        case 'diproses':
            return 'marker-processing'

        case 'selesai':
            return 'marker-completed'

        case 'ditolak':
            return 'marker-rejected'

        default:
            return 'marker-default'
    }
}

function formatDistance(distance) {
    if (distance < 1) {
        return `${Math.round(distance * 1000)} m`
    }

    return `${distance.toFixed(1)} km`
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const earthRadius = 6371

    const dLat = toRadians(lat2 - lat1)
    const dLon = toRadians(lon2 - lon1)

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRadians(lat1)) *
            Math.cos(toRadians(lat2)) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2)

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

    return earthRadius * c
}

function toRadians(value) {
    return (value * Math.PI) / 180
}

function createMap() {
    if (!mapContainer.value) {
        return
    }

    map = L.map(mapContainer.value, {
        center: [-6.2, 106.816666],
        zoom: 11,
        zoomControl: true,
    })

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }
    ).addTo(map)

    markersLayer = L.layerGroup().addTo(map)

    updateMarkers()

    setTimeout(() => {
        map?.invalidateSize()
    }, 300)
}

function updateMarkers() {
    if (!map || !markersLayer) {
        return
    }

    markersLayer.clearLayers()

    const bounds = []

    filteredReports.value.forEach(report => {
        if (
            report.latitude === null ||
            report.latitude === undefined ||
            report.longitude === null ||
            report.longitude === undefined
        ) {
            return
        }

        const latitude = Number(report.latitude)
        const longitude = Number(report.longitude)

        if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
            return
        }

        const marker = L.circleMarker(
            [latitude, longitude],
            {
                radius: 8,
                weight: 2,
                className: getMarkerClass(report.status),
            }
        )

        const statusLabel = getStatusLabel(report.status)

        const popupContent = `
            <div style="min-width: 220px;">
                <div style="font-size: 15px; font-weight: 700; margin-bottom: 8px;">
                    ${escapeHtml(report.title ?? 'Laporan')}
                </div>

                <div style="font-size: 13px; margin-bottom: 5px;">
                    <strong>Kategori:</strong>
                    ${escapeHtml(report.category?.name ?? '-')}
                </div>

                <div style="font-size: 13px; margin-bottom: 5px;">
                    <strong>Status:</strong>
                    ${escapeHtml(statusLabel)}
                </div>

                <div style="font-size: 13px; margin-bottom: 5px;">
                    <strong>Wilayah:</strong>
                    ${escapeHtml(report.district?.name ?? '-')}
                </div>

                <div style="font-size: 13px; margin-bottom: 8px;">
                    <strong>Alamat:</strong>
                    ${escapeHtml(report.address ?? '-')}
                </div>

                <a
                    href="${route('admin.reports.show', report.id)}"
                    style="
                        display: inline-block;
                        padding: 7px 10px;
                        background: #15803d;
                        color: white;
                        border-radius: 6px;
                        text-decoration: none;
                        font-size: 12px;
                        font-weight: 600;
                    "
                >
                    Lihat Detail
                </a>
            </div>
        `

        marker.bindPopup(popupContent)

        marker.addTo(markersLayer)

        bounds.push([latitude, longitude])
    })

    if (bounds.length > 0) {
        map.fitBounds(bounds, {
            padding: [30, 30],
            maxZoom: 15,
        })
    }
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
}

function getCurrentLocation() {
    if (!navigator.geolocation) {
        locationError.value = 'Browser kamu tidak mendukung GPS.'
        return
    }

    locationLoading.value = true
    locationError.value = ''

    navigator.geolocation.getCurrentPosition(
        position => {
            userLocation.value = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
            }

            locationLoading.value = false

            if (map) {
                map.setView(
                    [
                        position.coords.latitude,
                        position.coords.longitude,
                    ],
                    14
                )

                L.circleMarker(
                    [
                        position.coords.latitude,
                        position.coords.longitude,
                    ],
                    {
                        radius: 8,
                        color: '#1B5E20',
                        fillColor: '#1B5E20',
                        fillOpacity: 0.9,
                        weight: 3,
                    }
                )
                    .addTo(map)
                    .bindPopup('Lokasi kamu')
            }
        },
        error => {
            locationLoading.value = false

            if (error.code === 1) {
                locationError.value =
                    'Izin lokasi ditolak. Aktifkan lokasi di browser untuk menggunakan filter jarak.'
            } else {
                locationError.value =
                    'Lokasi tidak dapat ditemukan. Coba lagi.'
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0,
        }
    )
}

watch(
    [
        search,
        selectedCategory,
        selectedStatus,
        selectedDistrict,
        selectedVillage,
        radius,
        userLocation,
    ],
    () => {
        updateMarkers()
    }
)

onMounted(() => {
    createMap()
})

onBeforeUnmount(() => {
    if (map) {
        map.remove()
        map = null
        markersLayer = null
    }
})
</script>

<template>
    <Head title="Peta Monitoring" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Peta Monitoring
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Pantau persebaran laporan masyarakat berdasarkan lokasi.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

                <!-- Filter -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                <MapIcon class="h-5 w-5 text-green-700" />
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Filter Peta
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Gunakan filter untuk melihat laporan tertentu pada peta.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">

                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label
                                    for="search"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Cari Laporan
                                </label>

                                <div class="relative">
                                    <Search
                                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />

                                    <input
                                        id="search"
                                        v-model="search"
                                        type="text"
                                        placeholder="Cari judul, alamat, kategori, atau wilayah..."
                                        class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-700 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                    />
                                </div>
                            </div>

                            <!-- Category -->
                            <div>
                                <label
                                    for="category"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Kategori
                                </label>

                                <select
                                    id="category"
                                    v-model="selectedCategory"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                >
                                    <option value="">
                                        Semua Kategori
                                    </option>

                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label
                                    for="status"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Status
                                </label>

                                <select
                                    id="status"
                                    v-model="selectedStatus"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                >
                                    <option value="">
                                        Semua Status
                                    </option>

                                    <option
                                        v-for="status in statusOptions"
                                        :key="status.value"
                                        :value="status.value"
                                    >
                                        {{ status.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- District -->
                            <div>
                                <label
                                    for="district"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Kecamatan
                                </label>

                                <select
                                    id="district"
                                    v-model="selectedDistrict"
                                    @change="handleDistrictChange"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                >
                                    <option value="">
                                        Semua Kecamatan
                                    </option>

                                    <option
                                        v-for="district in districts"
                                        :key="district.id"
                                        :value="district.id"
                                    >
                                        {{ district.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Village -->
                            <div>
                                <label
                                    for="village"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Kelurahan
                                </label>

                                <select
                                    id="village"
                                    v-model="selectedVillage"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                >
                                    <option value="">
                                        Semua Kelurahan
                                    </option>

                                    <option
                                        v-for="village in filteredVillages"
                                        :key="village.id"
                                        :value="village.id"
                                    >
                                        {{ village.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Radius -->
                            <div>
                                <label
                                    for="radius"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Radius dari Lokasi Saya
                                </label>

                                <select
                                    id="radius"
                                    v-model="radius"
                                    :disabled="!userLocation"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-green-600 focus:ring-2 focus:ring-green-200 disabled:cursor-not-allowed disabled:bg-gray-100"
                                >
                                    <option :value="0">
                                        Semua Jarak
                                    </option>

                                    <option :value="1">
                                        1 km
                                    </option>

                                    <option :value="3">
                                        3 km
                                    </option>

                                    <option :value="5">
                                        5 km
                                    </option>

                                    <option :value="10">
                                        10 km
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    @click="getCurrentLocation"
                                    :disabled="locationLoading"
                                    class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <LocateFixed class="h-4 w-4" />

                                    {{
                                        locationLoading
                                            ? 'Mencari Lokasi...'
                                            : 'Gunakan Lokasi Saya'
                                    }}
                                </button>

                                <button
                                    type="button"
                                    @click="resetFilters"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                >
                                    <RotateCcw class="h-4 w-4" />
                                    Reset Filter
                                </button>
                            </div>

                            <p
                                v-if="locationError"
                                class="text-sm text-red-600"
                            >
                                {{ locationError }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">

                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Total
                                </p>

                                <p class="mt-1 text-2xl font-bold text-gray-800">
                                    {{ summary.total }}
                                </p>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                <FileText class="h-5 w-5 text-gray-600" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Menunggu
                                </p>

                                <p class="mt-1 text-2xl font-bold text-yellow-600">
                                    {{ summary.waiting }}
                                </p>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100">
                                <Clock class="h-5 w-5 text-yellow-600" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Diproses
                                </p>

                                <p class="mt-1 text-2xl font-bold text-blue-600">
                                    {{ summary.processing }}
                                </p>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                <LoaderCircle class="h-5 w-5 text-blue-600" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Selesai
                                </p>

                                <p class="mt-1 text-2xl font-bold text-green-600">
                                    {{ summary.completed }}
                                </p>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Ditolak
                                </p>

                                <p class="mt-1 text-2xl font-bold text-red-600">
                                    {{ summary.rejected }}
                                </p>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                                <XCircle class="h-5 w-5 text-red-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Persebaran Laporan
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Menampilkan {{ filteredReports.length }} laporan pada peta.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3 text-xs text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-yellow-500"></span>
                                    Menunggu
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                                    Diproses
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-green-600"></span>
                                    Selesai
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-red-600"></span>
                                    Ditolak
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        ref="mapContainer"
                        class="w-full"
                        style="height: 550px; width: 100%;"
                    ></div>
                </div>

                <!-- Report List -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Laporan pada Area
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Daftar laporan yang sesuai dengan filter.
                                </p>
                            </div>

                            <div class="hidden items-center gap-2 text-sm text-gray-500 sm:flex">
                                <MapPin class="h-4 w-4" />
                                {{ filteredReports.length }} laporan
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="filteredReports.length > 0"
                        class="divide-y divide-gray-200"
                    >
                        <div
                            v-for="report in filteredReports"
                            :key="report.id"
                            class="px-6 py-5 transition hover:bg-gray-50"
                        >
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-sm font-semibold text-gray-800">
                                            {{ report.title }}
                                        </h4>

                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                                            :class="getStatusClass(report.status)"
                                        >
                                            {{ getStatusLabel(report.status) }}
                                        </span>
                                    </div>

                                    <div class="mt-2 space-y-1 text-sm text-gray-500">
                                        <p>
                                            <span class="font-medium text-gray-700">
                                                Kategori:
                                            </span>
                                            {{ report.category?.name ?? '-' }}
                                        </p>

                                        <p>
                                            <span class="font-medium text-gray-700">
                                                Lokasi:
                                            </span>
                                            {{ report.address ?? '-' }}
                                        </p>

                                        <p>
                                            <span class="font-medium text-gray-700">
                                                Wilayah:
                                            </span>
                                            {{ report.district?.name ?? '-' }}
                                            <span v-if="report.village?.name">
                                                — {{ report.village.name }}
                                            </span>
                                        </p>

                                        <p>
                                            <span class="font-medium text-gray-700">
                                                Waktu:
                                            </span>
                                            {{ report.created_at ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    <div
                                        v-if="userLocation"
                                        class="hidden items-center gap-1 rounded-lg bg-gray-100 px-3 py-2 text-xs text-gray-600 sm:flex"
                                    >
                                        <Navigation class="h-3.5 w-3.5" />

                                        {{
                                            formatDistance(
                                                calculateDistance(
                                                    userLocation.latitude,
                                                    userLocation.longitude,
                                                    report.latitude,
                                                    report.longitude
                                                )
                                            )
                                        }}
                                    </div>

                                    <button
                                        type="button"
                                        @click="router.visit(route('admin.reports.show', report.id))"
                                        class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-800"
                                    >
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="px-6 py-12 text-center"
                    >
                        <MapPin class="mx-auto h-10 w-10 text-gray-300" />

                        <p class="mt-3 text-sm font-medium text-gray-600">
                            Tidak ada laporan yang sesuai.
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Coba ubah filter atau kata pencarian.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
:deep(.leaflet-container) {
    width: 100%;
    height: 100%;
    z-index: 0;
    font-family: inherit;
}

:deep(.leaflet-top),
:deep(.leaflet-bottom) {
    z-index: 10;
}

:deep(.leaflet-control) {
    z-index: 10;
}

:deep(.marker-waiting) {
    stroke: #ca8a04;
    fill: #eab308;
    fill-opacity: 0.9;
}

:deep(.marker-processing) {
    stroke: #2563eb;
    fill: #3b82f6;
    fill-opacity: 0.9;
}

:deep(.marker-completed) {
    stroke: #15803d;
    fill: #16a34a;
    fill-opacity: 0.9;
}

:deep(.marker-rejected) {
    stroke: #dc2626;
    fill: #ef4444;
    fill-opacity: 0.9;
}

:deep(.marker-default) {
    stroke: #374151;
    fill: #6b7280;
    fill-opacity: 0.9;
}
</style>