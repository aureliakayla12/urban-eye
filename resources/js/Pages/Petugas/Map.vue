<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { Head } from '@inertiajs/vue3'

import { onBeforeUnmount, onMounted, ref, computed } from 'vue'

import {
    MapPin,
    FileText,
    Clock3,
    CheckCircle2,
} from 'lucide-vue-next'

import L from 'leaflet'

import 'leaflet/dist/leaflet.css'


const props = defineProps({
    reports: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| MAP
|--------------------------------------------------------------------------
*/

const mapElement = ref(null)

const map = ref(null)

const selectedStatus = ref('Semua Status')


/*
|--------------------------------------------------------------------------
| FILTER LAPORAN
|--------------------------------------------------------------------------
*/

const filteredReports = computed(() => {

    if (selectedStatus.value === 'Semua Status') {
        return props.reports
    }

    return props.reports.filter(
        report => report.status === selectedStatus.value
    )

})


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const statusLabel = (status) => {

    return {
        menunggu: 'Menunggu',
        diproses: 'Diproses',
        selesai: 'Selesai',
        ditolak: 'Ditolak',
    }[status] ?? status

}


const statusColor = (status) => {

    return {
        menunggu: '#F59E0B',
        diproses: '#3B82F6',
        selesai: '#16A34A',
        ditolak: '#EF4444',
    }[status] ?? '#6B7280'

}


/*
|--------------------------------------------------------------------------
| MARKER ICON
|--------------------------------------------------------------------------
*/

const createMarkerIcon = (status) => {

    const color = statusColor(status)

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
| LOAD MARKERS
|--------------------------------------------------------------------------
*/

const loadMarkers = () => {

    if (!map.value) {
        return
    }


    map.value.eachLayer((layer) => {

        if (layer instanceof L.Marker) {

            map.value.removeLayer(layer)

        }

    })


    filteredReports.value.forEach((report) => {

        if (!report.latitude || !report.longitude) {
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
            <div style="min-width: 220px">

                <div style="
                    font-size: 14px;
                    font-weight: 600;
                    margin-bottom: 6px;
                ">
                    ${report.title ?? 'Laporan'}
                </div>


                <div style="
                    font-size: 12px;
                    color: #6B7280;
                    margin-bottom: 4px;
                ">
                    ${report.category?.name ?? 'Tanpa kategori'}
                </div>


                <div style="
                    font-size: 12px;
                    margin-bottom: 4px;
                ">
                    ${report.address ?? 'Alamat belum tersedia'}
                </div>


                <div style="
                    display: inline-block;
                    padding: 3px 8px;
                    border-radius: 999px;
                    background: ${statusColor(report.status)}20;
                    color: ${statusColor(report.status)};
                    font-size: 11px;
                    font-weight: 600;
                ">
                    ${statusLabel(report.status)}
                </div>

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

    if (!mapElement.value || map.value) {
        return
    }


    map.value = L.map(mapElement.value, {
        zoomControl: false,
    }).setView([-6.4025, 106.7942], 12)


    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors',
        }
    ).addTo(map.value)


    L.control.zoom({
        position: 'topright',
    }).addTo(map.value)


    loadMarkers()

}


/*
|--------------------------------------------------------------------------
| FILTER DARI KARTU
|--------------------------------------------------------------------------
*/

const selectStatus = (status) => {

    selectedStatus.value = status

    loadMarkers()

}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    initializeMap()

})


onBeforeUnmount(() => {

    if (map.value) {

        map.value.remove()

        map.value = null

    }

})

</script>


<template>

    <Head title="Peta Laporan" />


    <AuthenticatedLayout>

        <template #header>

            <div>

                <h2 class="text-xl font-semibold text-gray-900">
                    Peta Laporan
                </h2>


                <p class="mt-1 text-sm text-gray-500">
                    Lokasi laporan masyarakat yang perlu ditangani
                </p>

            </div>

        </template>


        <div class="space-y-6 p-5 sm:p-8">


            <!-- HEADER -->
            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900">

                            <FileText
                                :size="21"
                                class="text-green-600"
                            />

                            Peta Laporan

                        </h3>


                        <p class="mt-1 text-sm text-gray-500">

                            Pantau lokasi laporan berdasarkan status penanganan.

                        </p>

                    </div>

                </div>

            </div>


            <!-- MAP -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm">

                <div
                    ref="mapElement"
                    class="h-[520px] w-full"
                ></div>

            </div>


            <!-- LEGEND -->
            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <h3 class="mb-4 font-semibold text-gray-900">
                    Keterangan Status
                </h3>


                <div class="flex flex-wrap gap-5">


                    <!-- MENUNGGU -->
                    <div class="flex items-center gap-2">

                        <span class="h-4 w-4 rounded-full bg-yellow-500"></span>

                        <span class="text-sm text-gray-600">
                            Menunggu
                        </span>

                    </div>


                    <!-- DIPROSES -->
                    <div class="flex items-center gap-2">

                        <span class="h-4 w-4 rounded-full bg-blue-500"></span>

                        <span class="text-sm text-gray-600">
                            Diproses
                        </span>

                    </div>


                    <!-- SELESAI -->
                    <div class="flex items-center gap-2">

                        <span class="h-4 w-4 rounded-full bg-green-600"></span>

                        <span class="text-sm text-gray-600">
                            Selesai
                        </span>

                    </div>


                    <!-- DITOLAK -->
                    <div class="flex items-center gap-2">

                        <span class="h-4 w-4 rounded-full bg-red-500"></span>

                        <span class="text-sm text-gray-600">
                            Ditolak
                        </span>

                    </div>


                </div>

            </div>


            <!-- FILTER CARD -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">


                <!-- TOTAL LAPORAN -->
                <button
                    type="button"
                    @click="selectStatus('Semua Status')"
                    class="rounded-xl bg-white p-5 text-left shadow-sm transition hover:shadow-md"
                    :class="
                        selectedStatus === 'Semua Status'
                            ? 'ring-2 ring-green-500'
                            : ''
                    "
                >

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">

                            <MapPin
                                class="text-gray-600"
                                :size="20"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Total Laporan
                            </p>


                            <p class="text-xl font-semibold text-gray-900">
                                {{ reports.length }}
                            </p>

                        </div>

                    </div>

                </button>


                <!-- DIPROSES -->
                <button
                    type="button"
                    @click="selectStatus('diproses')"
                    class="rounded-xl bg-white p-5 text-left shadow-sm transition hover:shadow-md"
                    :class="
                        selectedStatus === 'diproses'
                            ? 'ring-2 ring-blue-500'
                            : ''
                    "
                >

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">

                            <Clock3
                                class="text-blue-600"
                                :size="20"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Diproses
                            </p>


                            <p class="text-xl font-semibold text-gray-900">

                                {{
                                    reports.filter(
                                        report => report.status === 'diproses'
                                    ).length
                                }}

                            </p>

                        </div>

                    </div>

                </button>


                <!-- SELESAI -->
                <button
                    type="button"
                    @click="selectStatus('selesai')"
                    class="rounded-xl bg-white p-5 text-left shadow-sm transition hover:shadow-md"
                    :class="
                        selectedStatus === 'selesai'
                            ? 'ring-2 ring-green-500'
                            : ''
                    "
                >

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50">

                            <CheckCircle2
                                class="text-green-600"
                                :size="20"
                            />

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Selesai
                            </p>


                            <p class="text-xl font-semibold text-gray-900">

                                {{
                                    reports.filter(
                                        report => report.status === 'selesai'
                                    ).length
                                }}

                            </p>

                        </div>

                    </div>

                </button>


            </div>


        </div>

    </AuthenticatedLayout>

</template>     