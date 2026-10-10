<script setup>
import PetugasLayout from '@/Layouts/PetugasLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
    ArrowLeft,
    CalendarDays,
    MapPin,
    CheckCircle2,
    Clock3,
} from 'lucide-vue-next'

defineProps({
    history: {
        type: Array,
        default: () => [],
    },
})

const statusText = (status) => {
    return {
        ditugaskan: 'Ditugaskan',
        diproses: 'Diproses',
        selesai: 'Selesai',
    }[status] ?? status
}

const statusClass = (status) => {
    return {
        ditugaskan: 'bg-yellow-100 text-yellow-700',
        diproses: 'bg-blue-100 text-blue-700',
        selesai: 'bg-green-100 text-green-700',
    }[status] ?? 'bg-gray-100 text-gray-600'
}
</script>

<template>
    <Head title="Riwayat Penanganan" />

    <PetugasLayout>
        <div class="p-6 lg:p-8">

            <!-- HEADER -->
            <div class="mb-6">
                <Link
                    href="/dashboard"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-green-600"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Dashboard
                </Link>

                <h1 class="mt-4 text-2xl font-semibold text-gray-800">
                    Riwayat Penanganan
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar tugas yang pernah Anda tangani.
                </p>
            </div>


            <!-- RIWAYAT -->
            <div class="rounded-xl bg-white shadow-sm">

                <!-- HEADER TABLE -->
                <div class="border-b border-gray-200 px-6 py-5">
                    <h2 class="font-semibold text-gray-800">
                        Riwayat Tugas
                    </h2>
                </div>


                <!-- ADA DATA -->
                <div v-if="history.length" class="divide-y divide-gray-100">

                    <div
                        v-for="item in history"
                        :key="item.id"
                        class="px-6 py-5"
                    >

                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                            <!-- INFO -->
                            <div class="min-w-0 flex-1">

                                <h3 class="font-semibold text-gray-800">
                                    {{ item.report?.title ?? 'Laporan' }}
                                </h3>

                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                                    <MapPin class="h-4 w-4 shrink-0" />
                                    <span>
                                        {{ item.report?.address ?? 'Lokasi belum tersedia' }}
                                    </span>
                                </div>

                                <div class="mt-1 flex items-center gap-2 text-xs text-gray-400">
                                    <CalendarDays class="h-4 w-4 shrink-0" />
                                    <span>
                                        {{ item.updated_at ?? '-' }}
                                    </span>
                                </div>

                            </div>


                            <!-- STATUS + DETAIL -->
                            <div class="flex shrink-0 items-center gap-3">

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-medium"
                                    :class="statusClass(item.status)"
                                >
                                    {{ statusText(item.status) }}
                                </span>

                                <Link
                                    :href="route('petugas.tasks.show', item.id)"
                                    class="rounded-md border border-green-500 px-3 py-2 text-xs font-medium text-green-600 hover:bg-green-50"
                                >
                                    Lihat Detail
                                </Link>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- KOSONG -->
                <div
                    v-else
                    class="flex min-h-[250px] flex-col items-center justify-center px-6 text-center"
                >
                    <Clock3 class="h-10 w-10 text-gray-300" />

                    <p class="mt-3 text-sm font-medium text-gray-500">
                        Belum ada riwayat penanganan
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Riwayat tugas yang Anda tangani akan muncul di sini.
                    </p>
                </div>

            </div>

        </div>
    </PetugasLayout>
</template>