<script setup>
import { computed, ref, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
    MapPin,
    CalendarDays,
    ClipboardList,
    Search,
    ChevronLeft,
    ChevronRight,
    Clock3,
} from 'lucide-vue-next'

const props = defineProps({
    history: {
        type: Array,
        default: () => [],
    },
})

// PENCARIAN, JUMLAH DATA, DAN HALAMAN
const search = ref('')
const pageSize = ref(5)
const currentPage = ref(1)

// Kembali ke halaman pertama saat pencarian atau jumlah data berubah
watch([search, pageSize], () => {
    currentPage.value = 1
})

// FORMAT STATUS
const statusText = (status) => {
    const labels = {
        ditugaskan: 'Ditugaskan',
        diproses: 'Diproses',
        selesai: 'Selesai',
        menunggu: 'Menunggu',
        ditolak: 'Ditolak',
        ditolak_admin: 'Ditolak Admin',
        ditolak_petugas: 'Ditolak Petugas',
    }

    const normalized = String(status ?? '').trim().toLowerCase()

    return labels[normalized] ??
        (normalized
            ? normalized.charAt(0).toUpperCase() + normalized.slice(1)
            : 'Belum ada status')
}

// WARNA STATUS
const statusClass = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase()

    if (normalized === 'ditugaskan' || normalized === 'menunggu') {
        return 'bg-yellow-100 text-yellow-700'
    }

    if (normalized === 'diproses') {
        return 'bg-blue-100 text-blue-700'
    }

    if (normalized === 'selesai') {
        return 'bg-green-100 text-green-700'
    }

    if (normalized.startsWith('ditolak')) {
        return 'bg-red-100 text-red-700'
    }

    return 'bg-gray-100 text-gray-600'
}

// MENGAMBIL FOTO LAPORAN
const getImageUrl = (item) => {
    const report = item?.report

    if (!report) {
        return null
    }

    const firstImage =
        report.images?.[0] ??
        report.report_images?.[0] ??
        null

    const image =
        firstImage?.image ??
        firstImage?.image_url ??
        firstImage?.url ??
        firstImage?.image_path ??
        firstImage?.path ??
        report.photo ??
        report.image_url ??
        report.image_path ??
        (typeof report.image === 'string' ? report.image : null)

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

    const cleanedPath = path
        .replace(/^public\//, '')
        .replace(/^storage\//, '')

    return `/storage/${cleanedPath}`
}

// FORMAT TANGGAL
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

// PENCARIAN OTOMATIS
const filteredHistory = computed(() => {
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return props.history
    }

    return props.history.filter((item) => {
        const report = item.report

        const searchableText = [
            report?.title,
            report?.description,
            report?.address,
            item.status,
            report?.status,
            item.note,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()

        return searchableText.includes(keyword)
    })
})

// PAGINATION
const totalPages = computed(() => {
    return Math.max(
        1,
        Math.ceil(filteredHistory.value.length / pageSize.value)
    )
})

const paginatedHistory = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value

    return filteredHistory.value.slice(
        start,
        start + pageSize.value
    )
})

const startItem = computed(() => {
    return filteredHistory.value.length === 0
        ? 0
        : (currentPage.value - 1) * pageSize.value + 1
})

const endItem = computed(() => {
    return Math.min(
        currentPage.value * pageSize.value,
        filteredHistory.value.length
    )
})

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
    }
}
</script>

<template>
    <Head title="Riwayat Penanganan" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#101827] sm:text-2xl">
                    Riwayat Penanganan
                </h1>

                <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">
                    Daftar tugas yang pernah Anda tangani.
                </p>
            </div>
        </template>

        <div class="space-y-5">
            <!-- JUMLAH RIWAYAT -->
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100">
                    <Clock3 class="h-6 w-6 text-green-700" />
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Total Riwayat
                    </p>

                    <p class="text-lg font-bold text-gray-800">
                        {{ history.length }} Laporan
                    </p>
                </div>
            </div>

            <!-- PENCARIAN DAN TAMPILKAN DATA -->
            <div
                class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
            >
                <!-- PENCARIAN -->
                <div class="relative w-full sm:max-w-md">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                    />

                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari judul, deskripsi, alamat, atau catatan..."
                        class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm text-gray-700 outline-none transition focus:border-green-600 focus:ring-1 focus:ring-green-600"
                    />
                </div>

                <!-- JUMLAH DATA -->
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

            <!-- TABEL RIWAYAT -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-left">
                        <!-- HEADER TABEL -->
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Foto
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Judul Laporan
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Tanggal Penanganan
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Catatan Penanganan
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <!-- ISI TABEL -->
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="item in paginatedHistory"
                                :key="item.id"
                                class="transition hover:bg-gray-50"
                            >
                                <!-- FOTO -->
                                <td class="px-5 py-3">
                                    <img
                                        v-if="getImageUrl(item)"
                                        :src="getImageUrl(item)"
                                        :alt="item.report?.title ?? 'Foto laporan'"
                                        class="h-12 w-16 rounded-lg border border-gray-100 object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-12 w-16 items-center justify-center rounded-lg bg-green-50"
                                    >
                                        <ClipboardList class="h-6 w-6 text-green-600" />
                                    </div>
                                </td>

                                <!-- JUDUL DAN LOKASI -->
                                <td class="max-w-[280px] px-5 py-3">
                                    <p class="break-words text-sm font-semibold text-gray-800">
                                        {{ item.report?.title ?? 'Laporan' }}
                                    </p>

                                    <p
                                        v-if="item.report?.description"
                                        class="mt-1 line-clamp-2 text-xs text-gray-400"
                                    >
                                        {{ item.report.description }}
                                    </p>

                                    <p class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                                        <MapPin class="h-3 w-3 shrink-0" />

                                        <span class="break-words">
                                            {{ item.report?.address ?? 'Lokasi belum tersedia' }}
                                        </span>
                                    </p>
                                </td>

                                <!-- TANGGAL PENANGANAN -->
                                <td class="whitespace-nowrap px-5 py-3">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <CalendarDays class="h-4 w-4 shrink-0 text-gray-400" />

                                        {{ formatDate(item.updated_at) }}
                                    </div>
                                </td>

                                <!-- CATATAN PENANGANAN -->
                                <td class="max-w-[320px] px-5 py-3">
                                    <p
                                        v-if="item.note && item.note.trim()"
                                        class="whitespace-pre-line break-words text-sm leading-6 text-gray-600"
                                    >
                                        {{ item.note }}
                                    </p>

                                    <span
                                        v-else
                                        class="text-sm italic text-gray-400"
                                    >
                                        Belum ada catatan
                                    </span>
                                </td>

                                <!-- STATUS -->
                                <td class="whitespace-nowrap px-5 py-3">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                        :class="statusClass(item.status)"
                                    >
                                        {{ statusText(item.status) }}
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td class="whitespace-nowrap px-5 py-3 text-center">
                                    <Link
                                        :href="route('petugas.tasks.show', item.id) + '?from=history'"
                                        class="inline-flex items-center justify-center rounded-lg bg-green-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900"
                                    >
                                        Lihat Detail
                                    </Link>
                                </td>
                            </tr>

                            <!-- JIKA KOSONG -->
                            <tr v-if="paginatedHistory.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <Search class="mx-auto mb-3 h-10 w-10 text-gray-300" />

                                    <h3 class="font-semibold text-gray-700">
                                        {{ search ? 'Data tidak ditemukan' : 'Belum ada riwayat penanganan' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{
                                            search
                                                ? 'Coba gunakan kata kunci lain.'
                                                : 'Riwayat tugas yang Anda tangani akan muncul di sini.'
                                        }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER DAN PAGINATION -->
                <div class="flex flex-col gap-4 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Menampilkan
                        <span class="font-medium text-gray-700">
                            {{ startItem }}–{{ endItem }}
                        </span>
                        dari
                        <span class="font-medium text-gray-700">
                            {{ filteredHistory.length }}
                        </span>
                        laporan
                    </p>

                    <div class="flex items-center justify-end gap-2">
                        <!-- HALAMAN SEBELUMNYA -->
                        <button
                            type="button"
                            :disabled="currentPage === 1"
                            aria-label="Halaman sebelumnya"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40"
                            @click="goToPage(currentPage - 1)"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </button>

                        <!-- NOMOR HALAMAN -->
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

                        <!-- HALAMAN BERIKUTNYA -->
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
