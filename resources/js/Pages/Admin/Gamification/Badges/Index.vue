<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Trophy, Plus, Pencil, Trash2 } from 'lucide-vue-next'

const props = defineProps({
    badges: {
        type: Array,
        default: () => [],
    },
})

const deleteBadge = (id) => {
    if (confirm('Yakin ingin menghapus badge ini?')) {
        router.delete(route('admin.gamification.badges.destroy', id))
    }
}
</script>

<template>
    <Head title="Badge" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Badge
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola badge pencapaian untuk pengguna.
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100"
                        >
                            <Trophy class="h-6 w-6 text-[#1B5E20]" />
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Daftar Badge
                            </h3>
                            <p class="text-sm text-gray-500">
                                Badge yang dapat diperoleh pengguna.
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="route('admin.gamification.badges.create')"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#1B5E20] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-800"
                    >
                        <Plus class="h-4 w-4" />
                        Tambah Badge
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-if="props.badges.length === 0"
                    class="rounded-xl border border-gray-200 bg-white p-10 text-center shadow-sm"
                >
                    <Trophy class="mx-auto h-12 w-12 text-gray-300" />

                    <h3 class="mt-4 text-lg font-semibold text-gray-700">
                        Belum ada badge
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Tambahkan badge pertama untuk sistem gamifikasi.
                    </p>

                    <Link
                        :href="route('admin.gamification.badges.create')"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#1B5E20] px-4 py-2.5 text-sm font-medium text-white hover:bg-green-800"
                    >
                        <Plus class="h-4 w-4" />
                        Tambah Badge
                    </Link>
                </div>

                <!-- Badge Table -->
                <div
                    v-else
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Badge
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Deskripsi
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Syarat Poin
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Syarat Laporan
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="badge in props.badges"
                                    :key="badge.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <!-- Badge -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100"
                                            >
                                                <Trophy
                                                    class="h-5 w-5 text-[#1B5E20]"
                                                />
                                            </div>

                                            <div>
                                                <p
                                                    class="font-semibold text-gray-800"
                                                >
                                                    {{ badge.name }}
                                                </p>

                                                <p
                                                    v-if="badge.icon"
                                                    class="text-xs text-gray-400"
                                                >
                                                    {{ badge.icon }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Deskripsi -->
                                    <td class="max-w-xs px-6 py-4">
                                        <p class="text-sm text-gray-600">
                                            {{ badge.description || '-' }}
                                        </p>
                                    </td>

                                    <!-- Required Points -->
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <span
                                            class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800"
                                        >
                                            {{ badge.required_points }} poin
                                        </span>
                                    </td>

                                    <!-- Required Reports -->
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <span
                                            class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700"
                                        >
                                            {{ badge.required_reports }} laporan
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.gamification.badges.edit',
                                                        badge.id
                                                    )
                                                "
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                            >
                                                <Pencil class="h-4 w-4" />
                                                Edit
                                            </Link>

                                            <button
                                                type="button"
                                                @click="deleteBadge(badge.id)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-100"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>