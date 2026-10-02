<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Save } from 'lucide-vue-next'

const form = useForm({
    name: '',
    description: '',
    icon: '',
})

const submit = () => {
    form.post(route('admin.master.categories.store'))
}
</script>

<template>
    <Head title="Tambah Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Tambah Kategori
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan kategori laporan masyarakat
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

                <!-- Kembali -->
                <Link
                    :href="route('admin.master.categories.index')"
                    class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800"
                >
                    <ArrowLeft :size="18" />
                    Kembali ke Kategori
                </Link>

                <!-- Form -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                    <div class="mb-6">
                        <h1 class="text-xl font-bold text-gray-800">
                            Form Tambah Kategori
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Isi informasi kategori yang ingin ditambahkan.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">

                        <!-- Nama -->
                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Nama Kategori
                            </label>

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Jalan Rusak"
                                class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            >

                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
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
                                rows="4"
                                placeholder="Jelaskan kategori laporan ini..."
                                class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            ></textarea>

                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Icon -->
                        <div>
                            <label
                                for="icon"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Icon
                            </label>

                            <input
                                id="icon"
                                v-model="form.icon"
                                type="text"
                                placeholder="Contoh: trash-2"
                                class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                Nama icon bersifat opsional.
                            </p>

                            <p
                                v-if="form.errors.icon"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.icon }}
                            </p>
                        </div>

                        <!-- Tombol -->
                        <div class="flex justify-end gap-3 border-t pt-5">

                            <Link
                                :href="route('admin.master.categories.index')"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Save :size="18" />
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Kategori' }}
                            </button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>