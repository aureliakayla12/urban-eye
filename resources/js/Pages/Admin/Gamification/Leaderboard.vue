<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Trophy, Medal, Crown, Star } from 'lucide-vue-next'

defineProps({
    leaderboard: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <Head title="Leaderboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Leaderboard
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Peringkat masyarakat berdasarkan total poin yang diperoleh.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Header Card -->
                <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="flex items-center gap-4 p-6">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-green-100"
                        >
                            <Trophy class="h-7 w-7 text-green-600" />
                        </div>

                        <div>
                            <h1 class="text-lg font-semibold text-gray-800">
                                Top 10 Masyarakat
                            </h1>

                            <p class="text-sm text-gray-500">
                                Pengguna dengan perolehan poin tertinggi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="leaderboard.length === 0"
                    class="rounded-xl bg-white p-10 text-center shadow-sm"
                >
                    <div
                        class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100"
                    >
                        <Trophy class="h-7 w-7 text-gray-400" />
                    </div>

                    <h3 class="text-base font-semibold text-gray-800">
                        Belum ada data leaderboard
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Belum ada masyarakat yang mendapatkan poin.
                    </p>
                </div>

                <!-- Leaderboard Table -->
                <div
                    v-else
                    class="overflow-hidden rounded-xl bg-white shadow-sm"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 font-semibold text-gray-600"
                                    >
                                        Peringkat
                                    </th>

                                    <th
                                        class="px-6 py-4 font-semibold text-gray-600"
                                    >
                                        Masyarakat
                                    </th>

                                    <th
                                        class="px-6 py-4 text-right font-semibold text-gray-600"
                                    >
                                        Total Poin
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                <tr
                                    v-for="(user, index) in leaderboard"
                                    :key="user.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <!-- Rank -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full"
                                                :class="{
                                                    'bg-yellow-100':
                                                        index === 0,
                                                    'bg-gray-200':
                                                        index === 1,
                                                    'bg-orange-100':
                                                        index === 2,
                                                    'bg-gray-100':
                                                        index > 2,
                                                }"
                                            >
                                                <Crown
                                                    v-if="index === 0"
                                                    class="h-5 w-5 text-yellow-600"
                                                />

                                                <Medal
                                                    v-else-if="index === 1"
                                                    class="h-5 w-5 text-gray-500"
                                                />

                                                <Medal
                                                    v-else-if="index === 2"
                                                    class="h-5 w-5 text-orange-500"
                                                />

                                                <span
                                                    v-else
                                                    class="text-sm font-semibold text-gray-600"
                                                >
                                                    {{ index + 1 }}
                                                </span>
                                            </div>

                                            <span
                                                class="font-semibold text-gray-700"
                                            >
                                                #{{ index + 1 }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- User -->
                                    <td class="px-6 py-4">
                                        <div>
                                            <p
                                                class="font-semibold text-gray-800"
                                            >
                                                {{ user.name }}
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-gray-500"
                                            >
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </td>

                                    <!-- Points -->
                                    <td class="px-6 py-4 text-right">
                                        <div
                                            class="inline-flex items-center gap-2 rounded-full bg-green-50 px-3 py-1.5"
                                        >
                                            <Star
                                                class="h-4 w-4 text-green-600"
                                            />

                                            <span
                                                class="font-semibold text-green-700"
                                            >
                                                {{ Number(user.total_points) }}
                                                poin
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