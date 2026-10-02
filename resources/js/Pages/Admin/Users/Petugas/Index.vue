<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Plus, Pencil, Trash2, Users } from 'lucide-vue-next'

const props = defineProps({
    users: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
})

function deletePetugas(id) {
    if (!confirm('Apakah kamu yakin ingin menghapus petugas ini?')) {
        return
    }

    router.delete(route('admin.users.petugas.destroy', id))
}
</script>

<template>
    <Head title="Kelola Petugas" />

    <AuthenticatedLayout>
        <!-- Header di samping garis tiga -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Kelola Petugas
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Kelola data petugas yang bertugas menangani laporan.
                    </p>
                </div>
            </div>
        </template>

        <!-- Isi halaman -->
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Card utama -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">

                    <!-- Header tabel -->
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Daftar Petugas
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Daftar pengguna dengan role petugas.
                                </p>
                            </div>

                            <!-- Tombol tambah -->
                            <Link
                                :href="route('admin.users.petugas.create')"
                                class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition hover:opacity-90"
                                style="background-color: #15803d;"
                            >
                                <Plus class="h-4 w-4" />
                                Tambah Petugas
                            </Link>
                        </div>
                    </div>

                    <!-- Tabel petugas -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        No
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Nama
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Email
                                    </th>

                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white">

                                <!-- Jika ada data petugas -->
                                <tr
                                    v-for="(user, index) in users.data"
                                    :key="user.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{
                                            ((users.current_page ?? 1) - 1) *
                                                (users.per_page ?? 10) +
                                            index +
                                            1
                                        }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100">
                                                <Users class="h-5 w-5 text-green-700" />
                                            </div>

                                            <span class="text-sm font-medium text-gray-800">
                                                {{ user.name }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ user.email }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex justify-center gap-2">

                                            <!-- Tombol edit -->
                                            <Link
                                                :href="route('admin.users.petugas.edit', user.id)"
                                                class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-white transition hover:opacity-90"
                                                style="background-color: #15803d;"
                                            >
                                                <Pencil class="h-4 w-4" />
                                                Edit
                                            </Link>

                                            <!-- Tombol hapus -->
                                            <button
                                                type="button"
                                                @click="deletePetugas(user.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                                Hapus
                                            </button>

                                        </div>
                                    </td>
                                </tr>

                                <!-- Jika data kosong -->
                                <tr v-if="users.data.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-6 py-10 text-center"
                                    >
                                        <div class="flex flex-col items-center">
                                            <Users class="mb-3 h-10 w-10 text-gray-300" />

                                            <p class="text-sm font-medium text-gray-500">
                                                Belum ada petugas.
                                            </p>

                                            <p class="mt-1 text-xs text-gray-400">
                                                Silakan tambahkan petugas baru.
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="users.links && users.links.length > 3"
                        class="border-t border-gray-200 px-6 py-4"
                    >
                        <div class="flex flex-wrap items-center justify-center gap-1">
                            <template
                                v-for="(link, index) in users.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    class="rounded-lg px-3 py-2 text-sm transition"
                                    :class="
                                        link.active
                                            ? 'bg-green-700 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    "
                                >
                                    <span v-html="link.label"></span>
                                </Link>

                                <span
                                    v-else
                                    class="rounded-lg px-3 py-2 text-sm text-gray-400"
                                >
                                    <span v-html="link.label"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>