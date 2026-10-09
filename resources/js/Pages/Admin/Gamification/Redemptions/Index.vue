<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { Check, X, PackageCheck } from 'lucide-vue-next'

const props = defineProps({
    redemptions: {
        type: Array,
        default: () => [],
    },
})

const approveRedemption = (id) => {
    if (!confirm('Yakin ingin menyetujui penukaran reward ini?')) {
        return
    }

    router.put(
        route('admin.gamification.redemptions.approve', id)
    )
}

const rejectRedemption = (id) => {
    if (!confirm('Yakin ingin menolak penukaran reward ini?')) {
        return
    }

    router.put(
        route('admin.gamification.redemptions.reject', id)
    )
}

const markAsTaken = (id) => {
    if (!confirm('Tandai reward ini sudah diambil?')) {
        return
    }

    router.put(
        route('admin.gamification.redemptions.taken', id)
    )
}

const statusLabel = (status) => {
    const labels = {
        pending: 'Menunggu',
        approved: 'Disetujui',
        rejected: 'Ditolak',
        taken: 'Sudah Diambil',
    }

    return labels[status] ?? status
}

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
        taken: 'bg-blue-100 text-blue-700',
    }

    return classes[status] ?? 'bg-gray-100 text-gray-700'
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
    <Head title="Penukaran Reward" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Penukaran Reward
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola permintaan penukaran reward dari masyarakat.
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">

                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800">
                            Daftar Penukaran
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Permintaan reward yang dilakukan oleh masyarakat.
                        </p>
                    </div>

                    <div
                        v-if="redemptions.length === 0"
                        class="px-6 py-16 text-center"
                    >
                        <PackageCheck
                            class="mx-auto h-12 w-12 text-gray-300"
                        />

                        <h3 class="mt-4 text-sm font-semibold text-gray-700">
                            Belum ada penukaran reward
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Permintaan penukaran reward akan muncul di sini.
                        </p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500">
                                        Pengguna
                                    </th>

                                    <th class="px-6 py-3 text-left font-medium text-gray-500">
                                        Reward
                                    </th>

                                    <th class="px-6 py-3 text-left font-medium text-gray-500">
                                        Jenis
                                    </th>

                                    <th class="px-6 py-3 text-left font-medium text-gray-500">
                                        Poin
                                    </th>

                                    <th class="px-6 py-3 text-left font-medium text-gray-500">
                                        Status
                                    </th>

                                    <th class="px-6 py-3 text-right font-medium text-gray-500">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="redemption in redemptions"
                                    :key="redemption.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800">
                                            {{ redemption.user?.name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ redemption.user?.email }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800">
                                            {{ redemption.reward?.name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-gray-600">
                                        {{ rewardTypeLabel(redemption.reward?.reward_type) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700"
                                        >
                                            {{ redemption.points_used }} poin
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="statusClass(redemption.status)"
                                        >
                                            {{ statusLabel(redemption.status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">

                                            <button
                                                v-if="redemption.status === 'pending'"
                                                type="button"
                                                @click="approveRedemption(redemption.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700"
                                            >
                                                <Check class="h-4 w-4" />
                                                Setujui
                                            </button>

                                            <button
                                                v-if="redemption.status === 'pending'"
                                                type="button"
                                                @click="rejectRedemption(redemption.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700"
                                            >
                                                <X class="h-4 w-4" />
                                                Tolak
                                            </button>

                                            <button
                                                v-if="redemption.status === 'approved'"
                                                type="button"
                                                @click="markAsTaken(redemption.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700"
                                            >
                                                <PackageCheck class="h-4 w-4" />
                                                Sudah Diambil
                                            </button>

                                            <span
                                                v-if="redemption.status === 'rejected'"
                                                class="text-xs text-gray-400 self-center"
                                            >
                                                Tidak ada aksi
                                            </span>

                                            <span
                                                v-if="redemption.status === 'taken'"
                                                class="text-xs text-gray-400 self-center"
                                            >
                                                Selesai
                                            </span>

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