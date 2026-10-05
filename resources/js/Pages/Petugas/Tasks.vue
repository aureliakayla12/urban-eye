<script setup>
import PetugasLayout from '@/Layouts/PetugasLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

import {
    MapPin,
    CalendarDays,
    ClipboardList,
} from 'lucide-vue-next'

defineProps({
    tasks: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <Head title="Tugas Saya" />

    <PetugasLayout>

        <div class="p-6 lg:p-8">

            <!-- HEADER -->
            <div class="mb-6">

                <h1 class="text-[28px] font-bold text-gray-800">
                    Tugas Saya
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Daftar tugas laporan yang ditugaskan kepada Anda
                </p>

            </div>


            <!-- JIKA BELUM ADA TUGAS -->
            <div
                v-if="tasks.length === 0"
                class="bg-white rounded-xl shadow-sm p-10 text-center"
            >

                <ClipboardList
                    class="w-12 h-12 text-gray-300 mx-auto mb-3"
                />

                <h3 class="text-[18px] font-semibold text-gray-700">
                    Belum ada tugas
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Saat ini belum ada laporan yang ditugaskan kepada Anda.
                </p>

            </div>


            <!-- DAFTAR TUGAS -->
            <div
                v-else
                class="space-y-4"
            >

                <div
                    v-for="task in tasks"
                    :key="task.id"
                    class="bg-white rounded-xl shadow-sm p-5"
                >

                    <div class="flex items-start gap-4">

                        <!-- ICON -->
                        <div
                            class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center shrink-0"
                        >
                            <ClipboardList
                                class="w-6 h-6 text-green-600"
                            />
                        </div>


                        <!-- DETAIL -->
                        <div class="flex-1 min-w-0">

                            <h2 class="text-[18px] font-semibold text-gray-800">
                                {{ task.report?.title ?? 'Tanpa judul' }}
                            </h2>


                            <!-- ALAMAT -->
                            <div
                                class="flex items-center gap-1 mt-2 text-sm text-gray-500"
                            >

                                <MapPin
                                    class="w-4 h-4 shrink-0"
                                />

                                <span>
                                    {{ task.report?.address ?? '-' }}
                                </span>

                            </div>


                            <!-- TANGGAL PENUGASAN -->
                            <div
                                class="flex items-center gap-1 mt-1 text-xs text-gray-400"
                            >

                                <CalendarDays
                                    class="w-3.5 h-3.5 shrink-0"
                                />

                                <span>
                                    {{ task.assigned_at ?? '-' }}
                                </span>

                            </div>


                            <!-- STATUS -->
                            <div class="mt-3">

                                <span
                                    class="inline-flex text-xs font-medium px-3 py-1 rounded-full"
                                    :class="{
                                        'bg-blue-100 text-blue-600':
                                            task.status === 'ditugaskan',

                                        'bg-yellow-100 text-yellow-600':
                                            task.status === 'diproses',

                                        'bg-green-100 text-green-600':
                                            task.status === 'selesai',
                                    }"
                                >
                                    {{ task.status }}
                                </span>

                            </div>

                        </div>


                        <!-- ACTION -->
                        <div class="shrink-0">

                            <Link
                                :href="`/petugas/tasks/${task.id}`"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-green-600 border border-green-500 rounded-lg hover:bg-green-50"
                            >
                                Lihat Detail
                            </Link>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </PetugasLayout>
</template>