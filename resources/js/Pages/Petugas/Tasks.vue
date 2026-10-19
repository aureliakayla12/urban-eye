
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, ref, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

import {
    MapPin,
    CalendarDays,
    ClipboardList,
    Search,
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next'

const props = defineProps({
    tasks: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const pageSize = ref(5)
const currentPage = ref(1)

watch([search, pageSize], () => {
    currentPage.value = 1
})

const statusLabel = (status) => {
    const labels = {
        ditugaskan: 'Ditugaskan',
        diproses: 'Diproses',
        selesai: 'Selesai',
        menunggu: 'Menunggu',
        ditolak: 'Ditolak',
    }

    const normalized = String(status ?? '').trim().toLowerCase()

    return labels[normalized] ??
        (normalized
            ? normalized.charAt(0).toUpperCase() + normalized.slice(1)
            : 'Belum ada status')
}

const statusClass = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase()

    if (normalized === 'selesai') {
        return 'bg-green-100 text-green-700'
    }

    if (normalized === 'diproses') {
        return 'bg-blue-100 text-blue-700'
    }

    if (normalized === 'ditugaskan' || normalized === 'menunggu') {
        return 'bg-yellow-100 text-yellow-700'
    }

    if (normalized === 'ditolak') {
        return 'bg-red-100 text-red-700'
    }

    return 'bg-gray-100 text-gray-600'
}

const getImageUrl = (task) => {
    const report = task?.report

    const firstImage =
        report?.images?.[0] ??
        report?.report_images?.[0] ??
        null

    const image =
        firstImage?.image ??
        firstImage?.image_url ??
        firstImage?.url ??
        firstImage?.image_path ??
        firstImage?.path ??
        report?.photo ??
        report?.image_url ??
        report?.image_path ??
        (typeof report?.image === 'string' ? report.image : null)

    if (typeof image !== 'string' || !image.trim()) {
        return null
    }

    const path = image.trim()

    if (/^https?:\/\//i.test(path)) {
        return path
    }

    if (path.startsWith('/')) {
        return path
    }

    return `/storage/${path
        .replace(/^public\//, '')
        .replace(/^storage\//, '')}`
}

const formatDate = (value) => {
    if (!value) return '-'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return value
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date)
}

const formatTime = (value) => {
    if (!value) return ''

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hourCycle: 'h23',
    }).format(date)
}

const filteredTasks = computed(() => {
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return props.tasks
    }

    return props.tasks.filter((task) => {
        const report = task.report

        const searchableText = [
            report?.title,
            report?.description,
            report?.address,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()

        return searchableText.includes(keyword)
    })
})

const totalPages = computed(() => {
    return Math.max(
        1,
        Math.ceil(filteredTasks.value.length / pageSize.value)
    )
})

const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value

    return filteredTasks.value.slice(
        start,
        start + pageSize.value
    )
})

const startItem = computed(() => {
    return filteredTasks.value.length === 0
        ? 0
        : (currentPage.value - 1) * pageSize.value + 1
})

const endItem = computed(() => {
    return Math.min(
        currentPage.value * pageSize.value,
        filteredTasks.value.length
    )
})

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
    }
}
</script>

<template>
    <Head title="Tugas Saya" />

    <AuthenticatedLayout>
    <template #header>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#101827] sm:text-2xl">
                Tugas Saya
            </h1>

            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">
                Daftar tugas laporan yang ditugaskan kepada Anda.
            </p>
        </div>
    </template>

    <div class="space-y-5 p-5 sm:p-6 lg:p-8">
            <div
                class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
            >

            <div class="relative w-full sm:max-w-md">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                    />

                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari judul, deskripsi, atau alamat..."
                        class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm text-gray-700 outline-none transition focus:border-green-600 focus:ring-1 focus:ring-green-600"
                    />
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span class="whitespace-nowrap">
                        Tampilkan
                    </span> 

                    <select
                        v-model.number="pageSize"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                    </select>

                    <span>data</span>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-left">

                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Foto
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Judul Laporan
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Lokasi
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Tanggal
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="task in paginatedTasks"
                                :key="task.id"
                                class="transition hover:bg-gray-50"
                            >
                                <td class="px-5 py-3">
                                    <img
                                        v-if="getImageUrl(task)"
                                        :src="getImageUrl(task)"
                                        :alt="task.report?.title ?? 'Foto laporan'"
                                        class="h-12 w-16 rounded-lg border border-gray-100 object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-12 w-16 items-center justify-center rounded-lg bg-green-50"
                                    >
                                        <ClipboardList class="h-6 w-6 text-green-600" />
                                    </div>
                                </td>

                                <td class="max-w-[280px] px-5 py-3">
                                    <p class="truncate text-sm font-semibold text-gray-800">
                                        {{ task.report?.title ?? 'Tanpa judul' }}
                                    </p>
                                </td>

\                                <td class="max-w-[240px] px-5 py-3">
                                    <div class="flex items-start gap-2 text-sm text-gray-500">
                                        <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />

                                        <span>
                                            {{ task.report?.address ?? 'Lokasi belum tersedia' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-3">
                                    <p class="text-sm text-gray-700">
                                        {{ formatDate(task.assigned_at) }}
                                    </p>

                                    <p class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                                        <CalendarDays class="h-3.5 w-3.5" />
                                        {{ formatTime(task.assigned_at) }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-3">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                        :class="statusClass(task.status)"
                                    >
                                        {{ statusLabel(task.status) }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-3 text-center">
                                    <Link
                                        :href="`/petugas/tasks/${task.id}`"
                                        class="inline-flex items-center justify-center rounded-lg bg-green-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900"
                                    >
                                        Lihat Detail
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="paginatedTasks.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <Search class="mx-auto mb-3 h-10 w-10 text-gray-300" />

                                    <h3 class="font-semibold text-gray-700">
                                        {{ search ? 'Data tidak ditemukan' : 'Belum ada tugas' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{
                                            search
                                                ? 'Coba gunakan kata kunci lain.'
                                                : 'Saat ini belum ada laporan yang ditugaskan kepada Anda.'
                                        }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-4 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Menampilkan
                        <span class="font-medium text-gray-700">
                            {{ startItem }} - {{ endItem }}
                        </span>
                        dari
                        <span class="font-medium text-gray-700">
                            {{ filteredTasks.length }}
                        </span>
                        laporan
                    </p>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            :disabled="currentPage === 1"
                            aria-label="Halaman sebelumnya"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40"
                            @click="goToPage(currentPage - 1)"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </button>

                        <button
                            v-for="pageNumber in totalPages"
                            :key="pageNumber"
                            type="button"
                            class="flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-medium transition"
                            :class="
                                currentPage === pageNumber
                                    ? 'bg-green-800 text-white'
                                    : 'text-gray-700 hover:bg-gray-100'
                            "
                            @click="goToPage(pageNumber)"
                        >
                            {{ pageNumber }}
                        </button>

                        <button
                            type="button"
                            :disabled="currentPage >= totalPages"
                            aria-label="Halaman berikutnya"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40"
                            @click="goToPage(currentPage + 1)"
                        >
                            <ChevronRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
