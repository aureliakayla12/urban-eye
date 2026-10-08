<template>
    <AuthenticatedLayout>
        <Head title="Detail Laporan" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-black">
                        Detail Laporan
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Informasi lengkap mengenai laporan yang kamu kirim.
                    </p>
                </div>
            </div>
        </template>

        <div class="min-h-screen bg-[#F5F5F5]">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

                <!-- Kembali -->
                <div class="mb-8">
                    <Link
                        :href="route('masyarakat.reports.index')"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-[#1B5E20]"
                    >
                        ← Kembali ke Laporan Saya
                    </Link>
                </div>

                <!-- Header laporan -->
                <Card>
                    <div
                        class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="rounded-full bg-[#E8F5E9] px-3 py-1 text-xs font-semibold text-[#1B5E20]"
                                >
                                    {{ report.category?.name ?? 'Tanpa kategori' }}
                                </span>

                                <span class="text-xs text-gray-400">
                                    {{ formatDate(report.created_at) }}
                                </span>
                            </div>

                            <h1
                                class="mt-3 text-2xl font-bold leading-tight text-[#111827] sm:text-3xl"
                            >
                                {{ report.title }}
                            </h1>
                        </div>

                        <StatusBadge :status="report.status" />
                    </div>
                </Card>

                <!-- Foto + AI -->
                <div class="mt-5 grid gap-5 lg:grid-cols-[1.2fr_0.8fr]">

                    <!-- Foto -->
                    <Card>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-black">
                                    Foto Laporan
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Foto yang dikirim saat membuat laporan.
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 overflow-hidden rounded-xl bg-[#F5F5F5]"
                        >
                            <div
                                class="flex min-h-[300px] items-center justify-center"
                            >
                                <img
                                    v-if="report.photo"
                                    :src="`/storage/${report.photo}`"
                                    alt="Foto laporan"
                                    class="max-h-[420px] w-full object-contain"
                                />

                                <span
                                    v-else
                                    class="text-sm text-gray-500"
                                >
                                    Tidak ada foto
                                </span>
                            </div>
                        </div>
                    </Card>

                    <!-- AI -->
                    <Card>
                        <div>
                            <h3 class="text-base font-semibold text-black">
                                Klasifikasi AI
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Analisis foto menggunakan Gemini.
                            </p>
                        </div>

                        <div
                            v-if="report.ai_category"
                            class="mt-5 space-y-4"
                        >
                            <!-- Kategori -->
                            <div
                                class="rounded-xl border border-[#DCEBDD] bg-[#F7FBF7] p-4"
                            >
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                >
                                    Kategori Terdeteksi
                                </p>

                                <p
                                    class="mt-2 text-xl font-bold text-[#1B5E20]"
                                >
                                    {{ report.ai_category }}
                                </p>
                            </div>

                            <!-- Confidence -->
                            <div class="rounded-xl bg-[#F5F5F5] p-4">
                                <div class="flex items-center justify-between">
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Tingkat Keyakinan
                                    </p>

                                    <p class="text-sm font-bold text-black">
                                        {{ report.ai_confidence }}%
                                    </p>
                                </div>

                                <div
                                    class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200"
                                >
                                    <div
                                        class="h-full rounded-full bg-[#1B5E20]"
                                        :style="{
                                            width: `${Math.min(
                                                Number(report.ai_confidence) || 0,
                                                100
                                            )}%`,
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Analisis -->
                            <div class="rounded-xl bg-[#F5F5F5] p-4">
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                >
                                    Analisis
                                </p>

                                <p
                                    class="mt-2 text-sm leading-6 text-gray-700"
                                >
                                    {{ report.ai_response }}
                                </p>
                            </div>

                            <p
                                v-if="report.classified_at"
                                class="text-xs text-gray-400"
                            >
                                Diklasifikasikan
                                {{ formatDateTime(report.classified_at) }}
                            </p>
                        </div>

                        <div
                            v-else
                            class="mt-5 rounded-xl bg-[#F5F5F5] px-4 py-6 text-center text-sm text-gray-500"
                        >
                            Belum ada hasil klasifikasi AI.
                        </div>
                    </Card>
                </div>

                <!-- Deskripsi -->
                <Card class="mt-5">
                    <div>
                        <h3 class="text-base font-semibold text-black">
                            Deskripsi Laporan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Penjelasan yang diberikan saat laporan dibuat.
                        </p>
                    </div>

                    <div class="mt-5 rounded-xl bg-[#F5F5F5] p-5">
                        <p
                            class="whitespace-pre-line text-sm leading-7 text-gray-700"
                        >
                            {{ report.description }}
                        </p>
                    </div>
                </Card>

                <!-- Lokasi -->
                <Card class="mt-5">
                    <div>
                        <h3 class="text-base font-semibold text-black">
                            Lokasi Laporan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Lokasi yang dipilih masyarakat saat mengirim laporan.
                        </p>
                    </div>

                    <!-- Map -->
                    <div
                        ref="mapContainer"
                        class="mt-5 h-[360px] w-full overflow-hidden rounded-xl border border-gray-200"
                    ></div>

                    <!-- Detail lokasi -->
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-[#F5F5F5] p-4">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Alamat
                            </p>

                            <p class="mt-2 text-sm leading-6 text-gray-700">
                                {{ report.address || '-' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-[#F5F5F5] p-4">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Wilayah
                            </p>

                            <p class="mt-2 text-sm text-gray-700">
                                {{ report.village?.name ?? '-' }},
                                {{ report.district?.name ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-[#F5F5F5] p-4">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Latitude
                            </p>

                            <p class="mt-2 text-sm text-gray-700">
                                {{ report.latitude }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-[#F5F5F5] p-4">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Longitude
                            </p>

                            <p class="mt-2 text-sm text-gray-700">
                                {{ report.longitude }}
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Riwayat status -->
                <Card class="mt-5">
                    <div>
                        <h3 class="text-base font-semibold text-black">
                            Riwayat Status
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Perkembangan status laporan dari waktu ke waktu.
                        </p>
                    </div>

                    <div
                        v-if="report.status_histories?.length"
                        class="mt-7"
                    >
                        <div
                            v-for="(history, index) in report.status_histories"
                            :key="history.id"
                            class="relative flex gap-4 pb-8 last:pb-0"
                        >
                            <!-- Garis -->
                            <div
                                v-if="
                                    index <
                                    report.status_histories.length - 1
                                "
                                class="absolute left-[9px] top-5 h-full w-px bg-gray-200"
                            ></div>

                            <!-- Titik -->
                            <div class="relative z-10 mt-1 shrink-0">
                                <div
                                    class="h-[18px] w-[18px] rounded-full border-4 border-[#E8F5E9] bg-[#1B5E20]"
                                ></div>
                            </div>

                            <!-- Konten -->
                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <p
                                        class="text-sm font-semibold text-black"
                                    >
                                        {{ statusLabel(history.new_status) }}
                                    </p>

                                    <span class="text-xs text-gray-400">
                                        {{ formatDateTime(history.changed_at) }}
                                    </span>
                                </div>

                                <div
                                    v-if="history.note"
                                    class="mt-3 rounded-xl bg-[#F5F5F5] px-4 py-3"
                                >
                                    <p
                                        class="text-sm leading-6 text-gray-700"
                                    >
                                        {{ history.note }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="mt-5 rounded-xl bg-[#F5F5F5] px-4 py-6 text-center text-sm text-gray-500"
                    >
                        Belum ada riwayat status.
                    </div>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, onBeforeUnmount, ref } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Card.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },
})

const mapContainer = ref(null)

let map = null
let marker = null

onMounted(() => {
    const latitude = Number(props.report.latitude)
    const longitude = Number(props.report.longitude)

    if (
        !mapContainer.value ||
        !Number.isFinite(latitude) ||
        !Number.isFinite(longitude)
    ) {
        return
    }

    map = L.map(mapContainer.value, {
        zoomControl: true,
        scrollWheelZoom: false,
    }).setView([latitude, longitude], 16)

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors',
        }
    ).addTo(map)

    marker = L.marker([latitude, longitude])
        .addTo(map)
        .bindPopup(
            `
                <div style="min-width: 180px;">
                    <strong>${escapeHtml(props.report.title)}</strong>
                    <br>
                    <span>${escapeHtml(props.report.address ?? '')}</span>
                </div>
            `
        )

    marker.openPopup()

    setTimeout(() => {
        map?.invalidateSize()
    }, 200)
})

onBeforeUnmount(() => {
    if (map) {
        map.remove()
        map = null
    }

    marker = null
})

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;')
}

function statusLabel(status) {
    const labels = {
        menunggu: 'Menunggu',
        diproses: 'Diproses',
        selesai: 'Selesai',
        ditolak: 'Ditolak',
    }

    return labels[status] ?? status
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

function formatDateTime(date) {
    return new Date(date).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>