<script setup>
import PetugasLayout from '@/Layouts/PetugasLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
    ArrowLeft,
    MapPin,
    CalendarDays,
    User,
    CheckCircle2,
    Clock3,
} from 'lucide-vue-next'

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
})

const status = computed(() => props.task?.status ?? 'ditugaskan')

const statusText = computed(() => {
    return {
        ditugaskan: 'Ditugaskan',
        diproses: 'Diproses',
        selesai: 'Selesai',
    }[status.value] ?? status.value
})

const statusClass = computed(() => {
    return {
        ditugaskan: 'bg-yellow-100 text-yellow-700',
        diproses: 'bg-blue-100 text-blue-700',
        selesai: 'bg-green-100 text-green-700',
    }[status.value] ?? 'bg-gray-100 text-gray-600'
})

const terimaTugas = () => {
    router.put(
        `/petugas/tasks/${props.task.id}`,
        {
            status: 'diproses',
        },
        {
            preserveScroll: true,
        }
    )
}

const selesaikanTugas = () => {
    router.put(
        `/petugas/tasks/${props.task.id}`,
        {
            status: 'selesai',
        },
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head title="Detail Laporan" />

    <PetugasLayout>
        <div class="p-6 lg:p-8">

            <!-- HEADER -->
            <div class="mb-6">
                <Link
                    href="/petugas/tasks"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-green-600"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Tugas Saya
                </Link>

                <h1 class="mt-4 text-2xl font-semibold text-gray-800">
                    Detail Laporan
                </h1>
            </div>

            <!-- CONTENT -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- KIRI -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- FOTO -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                        <div class="h-[320px] w-full bg-gray-100">
                            <img
                                v-if="task?.report?.photo"
                                :src="`/storage/${task.report.photo}`"
                                :alt="task?.report?.title"
                                class="h-full w-full object-cover"
                            />

                            <div
                                v-else
                                class="flex h-full items-center justify-center text-sm text-gray-400"
                            >
                                Tidak ada foto laporan
                            </div>
                        </div>
                    </div>

                    <!-- DETAIL LAPORAN -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">

                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">
                                    {{ task?.report?.title }}
                                </h2>

                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                                    <MapPin class="h-4 w-4" />
                                    <span>
                                        {{ task?.report?.address ?? 'Lokasi belum tersedia' }}
                                    </span>
                                </div>
                            </div>

                            <span
                                class="shrink-0 rounded-full px-3 py-1 text-xs font-medium"
                                :class="statusClass"
                            >
                                {{ statusText }}
                            </span>
                        </div>

                        <div class="my-5 border-t border-gray-100"></div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Deskripsi
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ task?.report?.description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                            <div class="rounded-lg bg-gray-50 p-4">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <CalendarDays class="h-4 w-4" />
                                    <span class="text-xs">
                                        Tanggal Laporan
                                    </span>
                                </div>

                                <p class="mt-2 text-sm font-medium text-gray-800">
                                    {{ task?.report?.created_at ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-gray-50 p-4">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <User class="h-4 w-4" />
                                    <span class="text-xs">
                                        Pelapor
                                    </span>
                                </div>

                                <p class="mt-2 text-sm font-medium text-gray-800">
                                    {{ task?.report?.user?.name ?? '-' }}
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- KANAN -->
                <div class="space-y-6">

                    <!-- STATUS -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-gray-800">
                            Status Tugas
                        </h3>

                        <div class="mt-5 space-y-4">

                            <!-- DITUGASKAN -->
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-yellow-100"
                                >
                                    <Clock3 class="h-4 w-4 text-yellow-600" />
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        Ditugaskan
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Tugas diberikan kepada petugas
                                    </p>
                                </div>
                            </div>

                            <!-- DIPROSES -->
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100"
                                >
                                    <Clock3 class="h-4 w-4 text-blue-600" />
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        Diproses
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Tugas sedang ditangani
                                    </p>
                                </div>
                            </div>

                            <!-- SELESAI -->
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100"
                                >
                                    <CheckCircle2 class="h-4 w-4 text-green-600" />
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        Selesai
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Tugas telah selesai ditangani
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- AKSI -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-gray-800">
                            Aksi Tugas
                        </h3>

                        <!-- TERIMA TUGAS -->
                        <button
                            v-if="status === 'ditugaskan'"
                            type="button"
                            @click="terimaTugas"
                            class="mt-5 w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-blue-700"
                        >
                            Terima Tugas
                        </button>

                        <!-- SELESAIKAN -->
                        <button
                            v-else-if="status === 'diproses'"
                            type="button"
                            @click="selesaikanTugas"
                            class="mt-5 w-full rounded-lg bg-green-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-green-700"
                        >
                            Selesaikan Tugas
                        </button>

                        <!-- SUDAH SELESAI -->
                        <div
                            v-else-if="status === 'selesai'"
                            class="mt-5 flex items-center justify-center gap-2 rounded-lg bg-green-100 px-4 py-3 text-sm font-medium text-green-700"
                        >
                            <CheckCircle2 class="h-4 w-4" />
                            Tugas Sudah Selesai
                        </div>
                    </div>

                    <!-- CATATAN -->
                    <div
                        v-if="task?.note"
                        class="rounded-xl bg-white p-6 shadow-sm"
                    >
                        <h3 class="font-semibold text-gray-800">
                            Catatan Penanganan
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-gray-600">
                            {{ task.note }}
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </PetugasLayout>
</template>