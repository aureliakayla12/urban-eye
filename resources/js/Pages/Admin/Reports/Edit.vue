<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },

    categories: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    title: props.report.title ?? '',
    description: props.report.description ?? '',
    category_id: props.report.category_id ?? '',
    address: props.report.address ?? '',
    status: props.report.status ?? 'menunggu',
    verification_status: props.report.verification_status ?? 'pending',
})

const submit = () => {
    form.put(route('admin.reports.update', props.report.id))
}
</script>

<template>
    <Head title="Edit Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Laporan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi laporan masyarakat
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-6">
                    <Link
                        :href="route('admin.reports.show', report.id)"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-green-700"
                    >
                        ← Kembali ke Detail
                    </Link>
                </div>

                <!-- Form -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Informasi Laporan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Silakan perbarui data laporan sesuai kebutuhan.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">

                        <!-- Judul -->
                        <div>
                            <label
                                for="title"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Judul Laporan
                            </label>

                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Masukkan judul laporan"
                            />

                            <p
                                v-if="form.errors.title"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label
                                for="category_id"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Kategori
                            </label>

                            <select
                                id="category_id"
                                v-model="form.category_id"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500"
                            >
                                <option value="">
                                    Pilih kategori
                                </option>

                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.category_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.category_id }}
                            </p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label
                                for="description"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Deskripsi
                            </label>

                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="5"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Masukkan deskripsi laporan"
                            ></textarea>

                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label
                                for="address"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Alamat
                            </label>

                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="3"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Masukkan alamat laporan"
                            ></textarea>

                            <p
                                v-if="form.errors.address"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <!-- Status + Verifikasi -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <!-- Status -->
                            <div>
                                <label
                                    for="status"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Status Laporan
                                </label>

                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500"
                                >
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

                                <p
                                    v-if="form.errors.status"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <!-- Verifikasi -->
                            <div>
                                <label
                                    for="verification_status"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Verifikasi
                                </label>

                                <select
                                    id="verification_status"
                                    v-model="form.verification_status"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500"
                                >
                                    <option value="pending">
                                        Pending
                                    </option>

                                    <option value="valid">
                                        Valid
                                    </option>

                                    <option value="hoax">
                                        Hoax
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.verification_status"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.verification_status }}
                                </p>
                            </div>
                        </div>

                        <!-- Tombol -->
                        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">

                            <Link
                                :href="route('admin.reports.show', report.id)"
                                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg px-5 py-2.5 text-sm font-medium"
                                style="background-color: #15803d; color: white;"
                            >
                                {{
                                    form.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Perubahan'
                                }}
                            </button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>