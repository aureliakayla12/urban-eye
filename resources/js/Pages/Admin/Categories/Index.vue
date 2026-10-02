<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    Plus,
    Pencil,
    Trash2,
    Tag,
    Construction,
    Lightbulb,
    Droplets,
} from 'lucide-vue-next'

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const iconComponents = {
    'construction': Construction,
    'trash-2': Trash2,
    'lightbulb': Lightbulb,
    'droplets': Droplets,
}

const deleteCategory = (id) => {
    if (confirm('Apakah kamu yakin ingin menghapus kategori ini?')) {
        router.delete(route('admin.master.categories.destroy', id))
    }
}
</script>

<template>
    <Head title="Kelola Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Kelola Kategori
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola kategori laporan masyarakat
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            Daftar Kategori
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Tambahkan dan kelola kategori laporan.
                        </p>
                    </div>

                    <Link
                        :href="route('admin.master.categories.create')"
                        class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-700"
                    >
                        <Plus :size="18" />
                        Tambah Kategori
                    </Link>
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

                    <!-- Jika ada data -->
                    <div v-if="categories.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">
                                        No
                                    </th>
                                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">
                                        Kategori
                                    </th>
                                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">
                                        Deskripsi
                                    </th>
                                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">
                                        Icon
                                    </th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="(category, index) in categories"
                                    :key="category.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ index + 1 }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                                                <Tag :size="20" />
                                            </div>

                                            <span class="font-medium text-gray-800">
                                                {{ category.name }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ category.description || '-' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div
                                            v-if="category.icon && iconComponents[category.icon]"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600"
                                        >
                                            <component
                                                :is="iconComponents[category.icon]"
                                                :size="20"
                                            />
                                        </div>

                                        <span v-else class="text-sm text-gray-400">
                                            -
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">

                                            <Link
                                                :href="route('admin.master.categories.edit', category.id)"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                                            >
                                                <Pencil :size="16" />
                                                Edit
                                            </Link>

                                            <button
                                                type="button"
                                                @click="deleteCategory(category.id)"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                                            >
                                                <Trash2 :size="16" />
                                                Hapus
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Jika belum ada data -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center px-6 py-16 text-center"
                    >
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <Tag :size="30" />
                        </div>

                        <h3 class="text-lg font-semibold text-gray-800">
                            Belum ada kategori
                        </h3>

                        <p class="mt-1 max-w-md text-sm text-gray-500">
                            Belum ada kategori laporan yang tersedia.
                            Silakan tambahkan kategori baru.
                        </p>

                        <Link
                            :href="route('admin.master.categories.create')"
                            class="mt-5 inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-700"
                        >
                            <Plus :size="18" />
                            Tambah Kategori
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>