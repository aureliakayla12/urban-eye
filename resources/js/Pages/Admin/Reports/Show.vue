<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },

    officers: {
        type: Array,
        default: () => [],
    },
});

const assignmentForm = useForm({
    officer_id: '',
});

let map = null;
let marker = null;

const assignOfficer = () => {
    if (!assignmentForm.officer_id) {
        return;
    }

    assignmentForm.put(
        route('admin.reports.update', props.report.id)
    );
};

onMounted(() => {
    if (
        props.report.latitude === null ||
        props.report.latitude === undefined ||
        props.report.longitude === null ||
        props.report.longitude === undefined
    ) {
        return;
    }

    map = L.map('report-map', {
        center: [
            props.report.latitude,
            props.report.longitude,
        ],
        zoom: 16,
    });

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }
    ).addTo(map);

    marker = L.marker([
        props.report.latitude,
        props.report.longitude,
    ])
        .addTo(map)
        .bindPopup(props.report.title)
        .openPopup();

    setTimeout(() => {
        map.invalidateSize();
    }, 200);
});

onBeforeUnmount(() => {
    if (map) {
        map.remove();
        map = null;
        marker = null;
    }
});

const deleteReport = () => {
    const confirmed = confirm(
        'Apakah kamu yakin ingin menghapus laporan ini?'
    );

    if (!confirmed) {
        return;
    }

    router.delete(
        route('admin.reports.destroy', props.report.id)
    );
};

const statusLabel = (value) => {
    const labels = {
        menunggu: 'Menunggu',
        diproses: 'Diproses',
        selesai: 'Selesai',
        ditolak: 'Ditolak',
    };

    return labels[value] || value;
};

const statusClass = (value) => {
    const classes = {
        menunggu: 'bg-amber-100 text-amber-700',
        diproses: 'bg-blue-100 text-blue-700',
        selesai: 'bg-green-100 text-green-700',
        ditolak: 'bg-red-100 text-red-700',
    };

    return classes[value] || 'bg-gray-100 text-gray-600';
};

const verificationLabel = (value) => {
    const labels = {
        pending: 'Pending',
        valid: 'Valid',
        hoax: 'Hoax',
    };

    return labels[value] || value;
};

const verificationClass = (value) => {
    const classes = {
        pending: 'bg-gray-100 text-gray-600',
        valid: 'bg-green-100 text-green-700',
        hoax: 'bg-red-100 text-red-700',
    };

    return classes[value] || 'bg-gray-100 text-gray-600';
};

const photoUrl = (photo) => {
    if (!photo) {
        return null;
    }

    if (photo.startsWith('http')) {
        return photo;
    }

    return `/storage/${photo}`;
};

const formatDate = (date) => {
    if (!date) {
        return '-';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

const formatTime = (date) => {
    if (!date) {
        return '-';
    }

    return new Date(date).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Detail Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Laporan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Lihat informasi lengkap laporan masyarakat
                </p>
            </div>
        </template>

        <div class="min-h-screen bg-[#f5f7f8] py-8">
            <div class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8">

                <!-- Navigasi & Aksi -->
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <!-- Kembali -->
                    <Link
                        :href="route('admin.reports.index')"
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

                    <!-- Aksi -->
                    <div class="flex items-center gap-3">

                        <!-- Edit -->
                        <Link
                            :href="route('admin.reports.edit', report.id)"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-5.5-9.5a2.121 2.121 0 013 3L12 12l-4 1 1-4 6.5-6.5z"
                                />
                            </svg>

                            Edit Laporan
                        </Link>

                        <!-- Hapus -->
                        <button
                            type="button"
                            @click="deleteReport"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium transition hover:opacity-90"
                            style="background-color: #dc2626; color: white;"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"
                                />
                            </svg>

                            Hapus Laporan
                        </button>

                    </div>
                </div>

                <!-- Detail -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    <!-- Foto -->
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm lg:col-span-1">
                        <div class="border-b border-gray-100 px-6 py-5">
                            <h3 class="font-semibold text-gray-800">
                                Foto Laporan
                            </h3>
                        </div>

                        <div class="p-5">
                            <div
                                class="overflow-hidden rounded-xl bg-gray-100"
                            >
                                <img
                                    v-if="photoUrl(report.photo)"
                                    :src="photoUrl(report.photo)"
                                    :alt="report.title"
                                    class="h-auto max-h-[450px] w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-64 items-center justify-center text-sm text-gray-400"
                                >
                                    Tidak ada foto
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi -->
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm lg:col-span-2">
                        <div class="border-b border-gray-100 px-6 py-5">
                            <h3 class="font-semibold text-gray-800">
                                Informasi Laporan
                            </h3>
                        </div>

                        <div class="space-y-6 p-6">

                            <!-- Judul -->
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Judul Laporan
                                </p>

                                <p class="mt-1 text-lg font-semibold text-gray-800">
                                    {{ report.title }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div class="flex flex-wrap gap-3">
                                <div>
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Status
                                    </p>

                                    <span
                                        :class="statusClass(report.status)"
                                        class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold"
                                    >
                                        {{ statusLabel(report.status) }}
                                    </span>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Verifikasi
                                    </p>

                                    <span
                                        :class="verificationClass(report.verification_status)"
                                        class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold"
                                    >
                                        {{
                                            verificationLabel(
                                                report.verification_status
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Kategori -->
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Kategori
                                </p>

                                <p class="mt-1 text-sm text-gray-700">
                                    {{
                                        report.category?.name ||
                                        report.ai_category ||
                                        '-'
                                    }}
                                </p>
                            </div>

                            <!-- Pelapor -->
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Pelapor
                                </p>

                                <p class="mt-1 text-sm font-medium text-gray-700">
                                    {{ report.user?.name || '-' }}
                                </p>

                                <p
                                    v-if="report.user?.email"
                                    class="mt-1 text-xs text-gray-400"
                                >
                                    {{ report.user.email }}
                                </p>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Deskripsi
                                </p>

                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">
                                    {{ report.description || '-' }}
                                </p>
                            </div>

                            <!-- Alamat -->
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Alamat
                                </p>

                                <p class="mt-1 text-sm leading-6 text-gray-600">
                                    {{ report.address || '-' }}
                                </p>
                            </div>

                            <!-- Wilayah -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Kecamatan
                                    </p>

                                    <p class="mt-1 text-sm text-gray-700">
                                        {{ report.district?.name || '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Kelurahan
                                    </p>

                                    <p class="mt-1 text-sm text-gray-700">
                                        {{ report.village?.name || '-' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Lokasi Peta -->
                            <div class="border-t border-gray-100 pt-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Lokasi Laporan
                                </p>

                                <div
                                    v-if="
                                        report.latitude !== null &&
                                        report.latitude !== undefined &&
                                        report.longitude !== null &&
                                        report.longitude !== undefined
                                    "
                                    class="mt-3 overflow-hidden rounded-xl border border-gray-200"
                                >
                                    <div
                                        id="report-map"
                                        class="relative z-0 h-[350px] w-full"
                                    ></div>
                                </div>

                                <div
                                    v-else
                                    class="mt-3 flex h-64 items-center justify-center rounded-xl bg-gray-100 text-sm text-gray-400"
                                >
                                    Lokasi belum tersedia
                                </div>

                                <div
                                    v-if="
                                        report.latitude !== null &&
                                        report.latitude !== undefined &&
                                        report.longitude !== null &&
                                        report.longitude !== undefined
                                    "
                                    class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2"
                                >
                                    <div class="rounded-lg bg-gray-50 px-4 py-3">
                                        <p class="text-xs text-gray-400">
                                            Latitude
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-gray-700">
                                            {{ report.latitude }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-gray-50 px-4 py-3">
                                        <p class="text-xs text-gray-400">
                                            Longitude
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-gray-700">
                                            {{ report.longitude }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Waktu -->
                            <div class="border-t border-gray-100 pt-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Waktu Laporan
                                </p>

                                <p class="mt-1 text-sm text-gray-700">
                                    {{ formatDate(report.created_at) }}
                                    ·
                                    {{ formatTime(report.created_at) }}
                                </p>
                            </div>

                            <!-- Petugas -->
                            <div class="border-t border-gray-100 pt-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Petugas
                                </p>

                                <div class="mt-3">
                                    <select
                                        v-model="assignmentForm.officer_id"
                                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 focus:border-green-500 focus:ring-green-500"
                                    >
                                        <option value="">
                                            Pilih petugas
                                        </option>

                                        <option
                                            v-for="officer in officers"
                                            :key="officer.id"
                                            :value="officer.id"
                                        >
                                            {{ officer.name }}
                                        </option>
                                    </select>

                                    <p
                                        v-if="assignmentForm.errors.officer_id"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ assignmentForm.errors.officer_id }}
                                    </p>

                                    <button
                                        type="button"
                                        @click="assignOfficer"
                                        :disabled="
                                            assignmentForm.processing ||
                                            !assignmentForm.officer_id
                                        "
                                        class="mt-3 inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-medium transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                                        style="background-color: #15803d; color: white;"
                                    >
                                        {{
                                            assignmentForm.processing
                                                ? 'Menugaskan...'
                                                : 'Assign Petugas'
                                        }}
                                    </button>

                                    <div
                                        v-if="report.assignments && report.assignments.length"
                                        class="mt-5 space-y-3"
                                    >
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                            Penugasan Saat Ini
                                        </p>

                                        <div
                                            v-for="assignment in report.assignments"
                                            :key="assignment.id"
                                            class="rounded-xl border border-gray-200 bg-gray-50 p-4"
                                        >
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        {{ assignment.officer?.name || '-' }}
                                                    </p>

                                                    <p
                                                        v-if="assignment.officer?.email"
                                                        class="mt-1 text-xs text-gray-500"
                                                    >
                                                        {{ assignment.officer.email }}
                                                    </p>
                                                </div>

                                                <span
                                                    class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700"
                                                >
                                                    {{ assignment.status }}
                                                </span>
                                            </div>

                                            <div class="mt-3 border-t border-gray-200 pt-3">
                                                <p class="text-xs text-gray-400">
                                                    Ditugaskan oleh
                                                </p>

                                                <p class="mt-1 text-sm text-gray-700">
                                                    {{ assignment.assigned_by?.name || '-' }}
                                                </p>
                                            </div>

                                            <div class="mt-3">
                                                <p class="text-xs text-gray-400">
                                                    Tanggal Penugasan
                                                </p>

                                                <p class="mt-1 text-sm text-gray-700">
                                                    {{ formatDate(assignment.assigned_at) }}
                                                    ·
                                                    {{ formatTime(assignment.assigned_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>