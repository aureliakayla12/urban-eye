<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Save } from 'lucide-vue-next'

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
    icon: props.category.icon || '',
})

const submit = () => {
    form.put(route('admin.master.categories.update', props.category.id))
}
</script>

<template>
    <Head title="Edit Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Kategori
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi kategori laporan
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

                <!-- Tombol Kembali -->
                <Link
                    :href="route('admin.master.categories.index')"
                    class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft :size="18" />
                    Kembali ke Kategori
                </Link>

                <!-- Form -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                    <div class="mb-6">
                        <h1 class="text-xl font-bold text-gray-800">
                            Edit Kategori
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Ubah informasi kategori sesuai kebutuhan.
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
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Contoh: Jalan Rusak"
                            />

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
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Masukkan deskripsi kategori"
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

                            <select
                                id="icon"
                                v-model="form.icon"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            >
                                <option value="construction">
                                    Construction - Jalan Rusak
                                </option>

                                <option value="trash-2">
                                    Trash 2 - Sampah
                                </option>

                                <option value="lightbulb">
                                    Lightbulb - Lampu Mati
                                </option>

                                <option value="droplets">
                                    Droplets - Drainase
                                </option>
                            </select>

                            <p
                                v-if="form.errors.icon"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.icon }}
                            </p>
                        </div>

                        <!-- Tombol -->
                        <div class="flex justify-end gap-3 pt-3">

                            <Link
                                :href="route('admin.master.categories.index')"
                                class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Save :size="18" />

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