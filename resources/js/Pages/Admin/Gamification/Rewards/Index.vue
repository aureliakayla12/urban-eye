<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    Gift,
    Plus,
    Pencil,
    Trash2,
    Star,
} from 'lucide-vue-next'

defineProps({
    rewards: {
        type: Array,
        default: () => [],
    },
})

const deleteReward = (id) => {
    if (!confirm('Apakah kamu yakin ingin menghapus reward ini?')) {
        return
    }

    router.delete(route('admin.gamification.rewards.destroy', id))
}

const rewardTypeLabel = (type) => {
    const types = {
        voucher: 'Voucher',
        pulsa: 'Pulsa',
        bibit: 'Bibit',
        lainnya: 'Lainnya',
    }

    return types[type] ?? type
}
</script>

<template>
    <Head title="Kelola Reward" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Kelola Reward
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola reward yang dapat ditukarkan menggunakan poin.
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-5 sm:px-6 lg:px-8">

                <!-- Header Reward -->
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100 text-green-600"
                            >
                                <Gift class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    Daftar Reward
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Kelola hadiah yang tersedia untuk pengguna UrbanEye.
                                </p>
                            </div>
                        </div>

                        <Link
                            :href="route('admin.gamification.rewards.create')"
                            class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
                        >
                            <Plus class="h-4 w-4" />
                            Tambah Reward
                        </Link>
                    </div>
                </div>

                <!-- Tabel Reward -->
                <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b px-5 py-4">
                        <div>
                            <h3 class="font-semibold text-gray-800">
                                Daftar Reward
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Informasi hadiah, kebutuhan poin, stok, dan status.
                            </p>
                        </div>

                        <Gift class="h-5 w-5 text-green-600" />
                    </div>

                    <div
                        v-if="rewards.length === 0"
                        class="px-5 py-12 text-center"
                    >
                        <Gift class="mx-auto h-10 w-10 text-gray-300" />

                        <p class="mt-3 text-sm font-medium text-gray-500">
                            Belum ada reward
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Silakan tambahkan reward terlebih dahulu.
                        </p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-5 py-3">No</th>
                                    <th class="px-5 py-3">Reward</th>
                                    <th class="px-5 py-3">Jenis</th>
                                    <th class="px-5 py-3">Poin</th>
                                    <th class="px-5 py-3">Stok</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="(reward, index) in rewards"
                                    :key="reward.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ index + 1 }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600"
                                            >
                                                <Gift class="h-4 w-4" />
                                            </div>

                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    {{ reward.name }}
                                                </p>

                                                <p class="max-w-xs truncate text-xs text-gray-500">
                                                    {{ reward.description || 'Tidak ada deskripsi.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-gray-600">
                                        {{ rewardTypeLabel(reward.reward_type) }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700"
                                        >
                                            <Star class="h-3.5 w-3.5" />
                                            {{ reward.point_cost }} poin
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-gray-600">
                                        {{ reward.stock }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            v-if="reward.status"
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700"
                                        >
                                            Aktif
                                        </span>

                                        <span
                                            v-else
                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600"
                                        >
                                            Nonaktif
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex justify-center gap-2">
                                            <Link
                                                :href="route('admin.gamification.rewards.edit', reward.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white hover:bg-green-700"
                                            >
                                                <Pencil class="h-3.5 w-3.5" />
                                                Edit
                                            </Link>

                                            <button
                                                type="button"
                                                @click="deleteReward(reward.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700"
                                            >
                                                <Trash2 class="h-3.5 w-3.5" />
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