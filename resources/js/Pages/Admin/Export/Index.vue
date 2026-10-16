<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { FileSpreadsheet, FileText, Download } from 'lucide-vue-next'

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const form = {
    tanggal_mulai: '',
    tanggal_selesai: '',
    kategori_id: '',
    status: '',
}

const exportLaporan = (format) => {
    const params = new URLSearchParams()

    Object.entries(form).forEach(([key, value]) => {
        if (value !== '') {
            params.append(key, value)
        }
    })

    const query = params.toString()
    const url = route('admin.export.download', { format })

    window.location.href = query ? `${url}?${query}` : url
}
</script>

<template>
    <Head title="Laporan & Export" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Laporan & Export
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Unduh laporan masyarakat sesuai periode dan filter yang dipilih.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-semibold text-gray-800">
                        Filter Laporan
                    </h2>

                    <form @submit.prevent class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Tanggal Mulai
                            </label>
                            <input
                                v-model="form.tanggal_mulai"
                                type="date"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-green-700 focus:ring-green-700"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Tanggal Selesai
                            </label>
                            <input
                                v-model="form.tanggal_selesai"
                                type="date"
                                :min="form.tanggal_mulai || undefined"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-green-700 focus:ring-green-700"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Kategori
                            </label>
                            <select
                                v-model="form.kategori_id"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-green-700 focus:ring-green-700"
                            >
                                <option value="">Semua Kategori</option>
                                <option
                                    v-for="kategori in props.categories"
                                    :key="kategori.id"
                                    :value="kategori.id"
                                >
                                    {{ kategori.name }}
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-400">
                                Pilihan kategori akan dihubungkan ke database.
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Status Laporan
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-green-700 focus:ring-green-700"
                            >
                                <option value="">Semua Status</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-green-100 p-3 text-green-700">
                                <FileSpreadsheet :size="25" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Export Excel</h3>
                                <p class="text-sm text-gray-500">
                                    Untuk mengolah data lebih lanjut.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="exportLaporan('excel')"
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-green-800"
                        >
                            <Download :size="18" />
                            Unduh Excel
                        </button>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-red-100 p-3 text-red-700">
                                <FileText :size="25" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Export PDF</h3>
                                <p class="text-sm text-gray-500">
                                    Untuk dokumentasi dan pencetakan.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="exportLaporan('pdf')"
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700"
                        >
                            <Download :size="18" />
                            Unduh PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>