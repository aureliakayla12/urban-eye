<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    reports: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },

    categories: {
        type: Array,
        default: () => [],
    },

    statuses: {
        type: Array,
        default: () => [],
    },

    verificationStatuses: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            status: '',
            verification_status: '',
            category_id: '',
        }),
    },
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const verificationStatus = ref(
    props.filters.verification_status || ''
);
const categoryId = ref(
    props.filters.category_id || ''
);

const applyFilter = () => {
    router.get(
        route('admin.reports.index'),
        {
            search: search.value,
            status: status.value,
            verification_status: verificationStatus.value,
            category_id: categoryId.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const resetFilter = () => {
    search.value = '';
    status.value = '';
    verificationStatus.value = '';
    categoryId.value = '';
    router.get(
        route('admin.reports.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const formatDate = (date) => {
    if (!date) {
        return '-';
    }
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatTime = (date) => {
    if (!date) {
        return '';
    }
    return new Date(date).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
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
        menunggu:
            'bg-amber-100 text-amber-700',
        diproses:
            'bg-blue-100 text-blue-700',
        selesai:
            'bg-green-100 text-green-700',
        ditolak:
            'bg-red-100 text-red-700',
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
        pending:
            'bg-gray-100 text-gray-600',
        valid:
            'bg-green-100 text-green-700',
        hoax:
            'bg-red-100 text-red-700',
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
</script>

<template>
    <Head title="Kelola Laporan" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Kelola Laporan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola dan pantau seluruh laporan masyarakat
                </p>
            </div>
        </template>

        <div class="min-h-screen bg-[#f5f7f8] py-8">
            <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Daftar Laporan
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Lihat, filter, dan kelola laporan
                                    yang masuk dari masyarakat.
                                </p>
                            </div>

                            <div class="rounded-lg bg-green-50 px-4 py-2 text-sm font-medium text-green-700">
                                Total:
                                {{ reports.total || reports.data.length }}
                                laporan
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-5">
                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                            <div class="lg:col-span-5">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Cari laporan
                                </label>

                                <div class="relative">
                                    <svg
                                        class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                        />
                                    </svg>

                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Cari judul, deskripsi atau alamat..."
                                        class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-4 text-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-100"
                                        @keyup.enter="applyFilter"
                                    />
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Status
                                </label>

                                <select v-model="status" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    <option value="">
                                        Semua Status
                                    </option>
                                    <option
                                        v-for="item in statuses"
                                        :key="item"
                                        :value="item"
                                    >
                                        {{ statusLabel(item) }}
                                    </option>
                                </select>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Kategori
                                </label>

                                <select v-model="categoryId" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
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

                            <div class="lg:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Verifikasi
                                </label>

                                <select v-model="verificationStatus" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    <option value="">
                                        Semua Verifikasi
                                    </option>
                                    <option
                                        v-for="item in verificationStatuses"
                                        :key="item"
                                        :value="item"
                                    >
                                        {{ verificationLabel(item) }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="resetFilter"
                                class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                            >
                                Reset
                            </button>

                            <button
                                type="button"
                                @click="applyFilter"
                                class="rounded-xl bg-[#1B5E20] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-green-800"
                            >
                                Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1150px]">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50 text-left">
                                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Foto
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Judul Laporan
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Kategori
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Pelapor
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Lokasi
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Tanggal
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Status
                                    </th>

                                    <th class="px-4 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Verifikasi
                                    </th>

                                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="report in reports.data"
                                    :key="report.id"
                                    class="border-b border-gray-100 transition hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="h-14 w-16 overflow-hidden rounded-lg bg-gray-100">
                                            <img
                                                v-if="photoUrl(report.photo)"
                                                :src="photoUrl(report.photo)"
                                                :alt="report.title"
                                                class="h-full w-full object-cover"
                                            />

                                            <div
                                                v-else
                                                class="flex h-full w-full items-center justify-center text-xs text-gray-400"
                                            >
                                                No Image
                                            </div>
                                        </div>
                                    </td>

                                    <td class="max-w-[230px] px-4 py-4">
                                        <p class="font-medium text-gray-800">
                                            {{ report.title }}
                                        </p>

                                        <p class="mt-1 line-clamp-2 text-xs text-gray-400">
                                            {{ report.description }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                            {{
                                                report.category?.name ||
                                                report.ai_category ||
                                                '-'
                                            }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-sm font-medium text-gray-700">
                                            {{
                                                report.user?.name ||
                                                '-'
                                            }}
                                        </p>
                                    </td>

                                    <td class="max-w-[180px] px-4 py-4">
                                        <p class="line-clamp-2 text-sm text-gray-600">
                                            {{ report.address || '-' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4">
                                        <p class="text-sm text-gray-600">
                                            {{ formatDate(report.created_at) }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            {{ formatTime(report.created_at) }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            :class="statusClass(report.status)"
                                            class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold"
                                        >
                                            {{ statusLabel(report.status) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            :class="verificationClass(report.verification_status)"
                                            class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold"
                                        >
                                            {{
                                                verificationLabel(
                                                    report.verification_status
                                                )
                                            }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <Link
                                            :href="
                                                route(
                                                    'admin.reports.show',
                                                    report.id
                                                )
                                            "
                                            class="inline-flex items-center gap-2 rounded-lg bg-[#1B5E20] px-4 py-2 text-xs font-semibold text-white transition hover:bg-green-800"
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
                                                    d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6Z"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                                />
                                            </svg>
                                            Lihat Detail
                                        </Link>
                                    </td>
                                </tr>

                                <tr v-if="reports.data.length === 0">
                                    <td colspan="9" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                            <svg
                                                class="h-8 w-8 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"
                                                />
                                            </svg>
                                        </div>

                                        <h4 class="mt-4 font-semibold text-gray-700">
                                            Belum ada laporan
                                        </h4>

                                        <p class="mt-1 text-sm text-gray-400">
                                            Tidak ada laporan yang sesuai
                                            dengan pencarian atau filter.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="reports.data.length > 0"
                        class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 px-6 py-4 sm:flex-row"
                    >
                        <p class="text-sm text-gray-500">
                            Menampilkan
                            <span class="font-medium text-gray-700">
                                {{ reports.from }}
                            </span>
                            sampai
                            <span class="font-medium text-gray-700">
                                {{ reports.to }}
                            </span>
                            dari
                            <span class="font-medium text-gray-700">
                                {{ reports.total }}
                            </span>
                            laporan
                        </p>

                        <div class="flex items-center gap-1">
                            <template
                                v-for="(link, index) in reports.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    preserve-state
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm transition"
                                    :class="
                                        link.active
                                            ? 'border-[#1B5E20] bg-[#1B5E20] text-white'
                                            : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'
                                    "
                                >
                                    <span
                                        v-html="link.label"
                                    ></span>

                                </Link>

                                <span
                                    v-else
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-gray-100 px-3 text-sm text-gray-300"
                                >
                                    <span
                                        v-html="link.label"
                                    ></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>