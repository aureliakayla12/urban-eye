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

                    <Link
                        :href="route('masyarakat.reports.index')"
                        class="inline-flex w-fit items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>

                        Kembali ke Laporan
                    </Link>

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

                <div class="mt-5 grid gap-5 lg:grid-cols-[1.2fr_0.8fr]">

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

                        <div class="mt-5">
                            <div
                                v-if="reportPhotos.length"
                                class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                            >
                                <a
                                    v-for="(photo, index) in reportPhotos"
                                    :key="photo.id"
                                    :href="photo.src"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex min-h-[240px] items-center justify-center
                                        overflow-hidden rounded-xl border border-gray-200
                                        bg-[#F5F5F5]"
                                >
                                    <img
                                        :src="photo.src"
                                        :alt="`Foto laporan ${index + 1}`"
                                        class="h-full max-h-[360px] w-full object-cover
                                            transition duration-200 group-hover:scale-[1.02]"
                                    />
                                </a>
                            </div>

                            <div
                                v-else
                                class="flex min-h-[300px] items-center justify-center
                                    rounded-xl bg-[#F5F5F5]"
                            >
                                <span class="text-sm text-gray-500">
                                    Tidak ada foto
                                </span>
                            </div>

                            <p
                                v-if="reportPhotos.length > 1"
                                class="mt-3 text-xs text-gray-500"
                            >
                                {{ reportPhotos.length }} foto laporan
                            </p>
                        </div>
                    </Card>

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

                <Card class="mt-5">
                    <div>
                        <h3 class="text-base font-semibold text-black">
                            Lokasi Laporan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Lokasi yang dipilih masyarakat saat mengirim laporan.
                        </p>
                    </div>

                    <div
                        ref="mapContainer"
                        class="relative z-0 isolate mt-5 h-[360px] w-full overflow-hidden rounded-xl border border-gray-200"
                    ></div>

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
                            <div
                                v-if="
                                    index <
                                    report.status_histories.length - 1
                                "
                                class="absolute left-[9px] top-5 h-full w-px bg-gray-200"
                            ></div>

                            <div class="relative z-10 mt-1 shrink-0">
                                <div
                                    class="h-[18px] w-[18px] rounded-full border-4 border-[#E8F5E9] bg-[#1B5E20]"
                                ></div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="text-sm font-semibold text-black">
                                            {{ historyTitle(history) }}
                                        </p>

                                        <p
                                            v-if="
                                                history.old_status &&
                                                history.old_status !== history.new_status
                                            "
                                            class="mt-1 text-xs text-gray-500"
                                        >
                                            {{ statusLabel(history.old_status) }}
                                            →
                                            {{ statusLabel(history.new_status) }}
                                        </p>
                                    </div>

                                    <span class="text-xs text-gray-400">
                                        {{ formatDateTime(history.changed_at || history.created_at) }}
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
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, onMounted, onBeforeUnmount, ref, } from 'vue'
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

const reportPhotos = computed(() => {
    const images = props.report.images ?? []

    // Gunakan seluruh foto dari tabel report_images.
    if (images.length > 0) {
        return images.map((item) => ({
            id: item.id,
            src: `/storage/${item.image}`,
        }))
    }

    return props.report.photo
        ? [{
            id: 'primary-photo',
            src: `/storage/${props.report.photo}`,
        }]
        : []
})

const mapContainer = ref(null)

let map = null
let marker = null
let refreshTimer = null

onMounted(() => {
    const hasCoordinates =
        props.report.latitude !== null &&
        props.report.latitude !== undefined &&
        props.report.latitude !== '' &&
        props.report.longitude !== null &&
        props.report.longitude !== undefined &&
        props.report.longitude !== '';

    const latitude = Number(props.report.latitude);
    const longitude = Number(props.report.longitude);

    if (
        mapContainer.value &&
        hasCoordinates &&
        Number.isFinite(latitude) &&
        Number.isFinite(longitude) &&
        latitude >= -90 &&
        latitude <= 90 &&
        longitude >= -180 &&
        longitude <= 180
    ) {
        map = L.map(mapContainer.value, {
            zoomControl: true,
            scrollWheelZoom: false,
        }).setView([latitude, longitude], 16);

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap contributors',
            },
        ).addTo(map);

        marker = L.marker([latitude, longitude])
            .addTo(map)
            .bindPopup(`
                <div style="min-width: 180px;">
                    <strong>${escapeHtml(props.report.title)}</strong>
                    <br>
                    <span>${escapeHtml(props.report.address ?? '')}</span>
                </div>
            `);

        marker.openPopup();

        setTimeout(() => {
            map?.invalidateSize();
        }, 200);
    }

    refreshTimer = setInterval(() => {
        router.reload({
            only: ['report'],
            preserveScroll: true,
            preserveState: true,
        });
    }, 10000);
});

onBeforeUnmount(() => {
    if (refreshTimer) {
        clearInterval(refreshTimer)
        refreshTimer = null
    }

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



function historyTitle(history) {
    const note = history.note?.toLowerCase() ?? '';

    if (note.includes('laporan dibuat')) {
        return 'Laporan Dibuat';
    }

    if (
        note.includes('petugas ditugaskan') ||
        note.includes('petugas ditugaskan:')
    ) {
        return 'Petugas Ditugaskan';
    }

    if (note.includes('tugas sedang dikerjakan')) {
        return 'Tugas Sedang Dikerjakan';
    }

    if (note.includes('penanganan selesai')) {
        return 'Penanganan Selesai';
    }

    if (note.includes('verifikasi diubah')) {
        return 'Verifikasi Diperbarui';
    }

    if (history.old_status !== history.new_status) {
        if (history.new_status === 'ditolak') {
            return 'Laporan Ditolak';
        }

        return 'Status Laporan Diperbarui';
    }

    return `Status: ${statusLabel(history.new_status)}`;
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
    if (!date) {
        return 'Waktu tidak tersedia';
    }

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) {
        return 'Waktu tidak tersedia';
    }

    return parsedDate.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>