<template>
    <AuthenticatedLayout>
        <Head title="Riwayat Laporan" />

        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Riwayat Laporan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola dan pantau laporan yang telah kamu kirim.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- FILTER -->
                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <!-- Left Side -->
                        <div
                            class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center"
                        >
                            <!-- Search -->
                            <div class="relative flex-1">
                                <svg
                                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                    />
                                </svg>

                                <input
                                    v-model="filters.search"
                                    type="text"
                                    placeholder="Cari judul, deskripsi, atau alamat..."
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-white pl-9 pr-3 text-sm text-gray-700 outline-none transition focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
                                    @keyup.enter="applyFilter"
                                />
                            </div>

                            <!-- Status -->
                            <select
                                v-model="filters.status"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20] sm:w-40"
                            >
                                <option value="">
                                    Semua Status
                                </option>

                                <option value="menunggu">
                                    Menunggu
                                </option>

                                <option value="diproses">
                                    Diproses
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                                <option value="ditolak">
                                    Ditolak
                                </option>
                            </select>

                            <!-- Kategori -->
                            <select
                                v-model="filters.categoryId"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20] sm:w-44"
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

                        <!-- Right Side -->
                        <div
                            class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center"
                        >
                            <PaginationLimit
                                v-model="filters.perPage"
                                @update:modelValue="applyFilter"
                            />

                            <button
                                type="button"
                                @click="applyFilter"
                                class="h-10 whitespace-nowrap rounded-lg bg-[#1B5E20] px-5 text-sm font-semibold text-white transition hover:bg-[#164A19]"
                            >
                                Terapkan Filter
                            </button>

                            <button
                                type="button"
                                @click="resetFilters"
                                class="h-10 whitespace-nowrap rounded-lg bg-gray-500 px-5 text-sm font-semibold text-white transition hover:bg-gray-600"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABLE -->
                <div
                    class="mt-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                >

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px]">
                            <thead>
                                <tr
                                    class="border-y border-gray-200 bg-gray-50"
                                >
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Foto
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Judul Laporan
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Kategori
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Tanggal
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- Data laporan -->
                                <tr
                                    v-for="report in reports"
                                    :key="report.id"
                                    class="border-b border-gray-100 transition hover:bg-gray-50"
                                >
                                    <!-- Foto -->
                                    <td class="px-5 py-3">
                                        <div
                                            class="h-12 w-16 overflow-hidden rounded-lg bg-gray-100"
                                        >
                                            <img
                                                v-if="report.photo"
                                                :src="`/storage/${report.photo}`"
                                                alt="Foto laporan"
                                                class="h-full w-full object-cover"
                                            />

                                            <div
                                                v-else
                                                class="flex h-full items-center justify-center text-xs text-gray-400"
                                            >
                                                -
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Judul -->
                                    <td class="max-w-[280px] px-5 py-3">
                                        <p
                                            class="truncate text-sm font-semibold text-gray-800"
                                        >
                                            {{ report.title }}
                                        </p>

                                        <p
                                            class="mt-1 truncate text-xs text-gray-400"
                                        >
                                            {{ report.description }}
                                        </p>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-5 py-3">
                                        <span
                                            class="text-sm text-gray-600"
                                        >
                                            {{
                                                report.category?.name ??
                                                'Tanpa kategori'
                                            }}
                                        </span>
                                    </td>

                                    <!-- Tanggal -->
                                    <td class="px-5 py-3">
                                        <p class="text-sm text-gray-700">
                                            {{ formatDate(report.created_at) }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            {{ formatTime(report.created_at) }}
                                        </p>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-5 py-3">
                                        <StatusBadge
                                            :status="report.status"
                                        />
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-5 py-3 text-right">
                                        <Link
                                            :href="
                                                route(
                                                    'masyarakat.reports.show',
                                                    report.id
                                                )
                                            "
                                            class="inline-flex rounded-lg bg-[#1B5E20] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#164A19]"
                                        >
                                            Lihat Detail
                                        </Link>
                                    </td>
                                </tr>

                                <!-- Tidak ada data -->
                                <tr v-if="reports.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-5 py-14 text-center"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800"
                                        >
                                            Belum Ada Laporan
                                        </h3>

                                        <p
                                            class="mt-2 text-sm text-gray-500"
                                        >
                                            Kamu belum membuat laporan apa pun.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div
                        v-if="pagination.total > 0"
                        class="flex flex-col gap-4 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <!-- Informasi jumlah data -->
                        <p class="text-sm text-gray-500">
                            Menampilkan
                            <span class="font-medium text-gray-700">
                                {{ pagination.from }}
                            </span>
                            -
                            <span class="font-medium text-gray-700">
                                {{ pagination.to }}
                            </span>
                            dari
                            <span class="font-medium text-gray-700">
                                {{ pagination.total }}
                            </span>
                            laporan
                        </p>

                        <!-- Pagination -->
                        <div class="flex items-center gap-1">
                            <!-- Previous -->
                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="
                                    pagination.current_page === 1
                                "
                                @click="
                                    goToPage(
                                        pagination.current_page - 1
                                    )
                                "
                            >
                                ←
                            </button>

                            <!-- Page Number -->
                            <button
                                v-for="page in pagination.last_page"
                                :key="page"
                                type="button"
                                class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm font-medium transition"
                                :class="
                                    page === pagination.current_page
                                        ? 'bg-[#1B5E20] text-white'
                                        : 'text-gray-600 hover:bg-gray-100'
                                "
                                @click="goToPage(page)"
                            >
                                {{ page }}
                            </button>

                            <!-- Next -->
                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="
                                    pagination.current_page ===
                                    pagination.last_page
                                "
                                @click="
                                    goToPage(
                                        pagination.current_page + 1
                                    )
                                "
                            >
                                →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import PaginationLimit from '@/Components/PaginationLimit.vue'

const props = defineProps({
    reports: {
        type: Array,
        default: () => [],
    },

    categories: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            status: '',
            category_id: '',
            per_page: 5,
        }),
    },

    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0,
        }),
    },
})

const filters = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    categoryId: props.filters.category_id ?? '',
    perPage: String(props.filters.per_page ?? 5),
})

function applyFilter() {
    router.get(
        route('masyarakat.reports.index'),
        {
            search: filters.search || undefined,
            status: filters.status || undefined,
            category_id: filters.categoryId || undefined,
            per_page: filters.perPage,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

function resetFilters() {
    filters.search = ''
    filters.status = ''
    filters.categoryId = ''
    filters.perPage = '5'

    router.get(
        route('masyarakat.reports.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

function goToPage(page) {
    if (
        page < 1 ||
        page > props.pagination.last_page ||
        page === props.pagination.current_page
    ) {
        return
    }

    router.get(
        route('masyarakat.reports.index'),
        {
            search: filters.search || undefined,
            status: filters.status || undefined,
            category_id: filters.categoryId || undefined,
            per_page: filters.perPage,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

function formatTime(date) {
    return new Date(date).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>