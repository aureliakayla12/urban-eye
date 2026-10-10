<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { Head } from '@inertiajs/vue3'

import {
    ClipboardList,
    Clock3,
    CheckCircle2,
    Hourglass,
    TrendingUp,
} from 'lucide-vue-next'


const props = defineProps({

    statistics: {
        type: Object,
        default: () => ({
            total: 0,
            diproses: 0,
            selesai: 0,
            ditugaskan: 0,
        }),
    },

})


const percentage = (value) => {

    if (!props.statistics.total) {
        return 0
    }

    return Math.round(
        (value / props.statistics.total) * 100
    )

}

</script>


<template>

    <Head title="Statistik" />


    <AuthenticatedLayout>

        <template #header>

            <div>

                <h2 class="text-xl font-semibold text-gray-900">
                    Statistik
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan kinerja dan penanganan tugas Anda
                </p>

            </div>

        </template>


        <div class="space-y-6 p-5 sm:p-8">


            <!-- ===================================================== -->
            <!-- 4 STATISTIK -->
            <!-- ===================================================== -->

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">


                <!-- TOTAL -->
                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">

                            <ClipboardList
                                class="h-6 w-6 text-green-600"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Total Tugas
                            </p>

                            <p class="mt-1 text-3xl font-bold text-gray-800">
                                {{ statistics.total }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Seluruh tugas Anda
                            </p>

                        </div>

                    </div>

                </div>


                <!-- DITUGASKAN -->
                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">

                            <Hourglass
                                class="h-6 w-6 text-yellow-500"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Ditugaskan
                            </p>

                            <p class="mt-1 text-3xl font-bold text-yellow-500">
                                {{ statistics.ditugaskan }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Belum diproses
                            </p>

                        </div>

                    </div>

                </div>


                <!-- DIPROSES -->
                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">

                            <Clock3
                                class="h-6 w-6 text-blue-600"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Diproses
                            </p>

                            <p class="mt-1 text-3xl font-bold text-blue-600">
                                {{ statistics.diproses }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Sedang ditangani
                            </p>

                        </div>

                    </div>

                </div>


                <!-- SELESAI -->
                <div class="rounded-xl bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">

                            <CheckCircle2
                                class="h-6 w-6 text-green-600"
                            />

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Selesai
                            </p>

                            <p class="mt-1 text-3xl font-bold text-green-600">
                                {{ statistics.selesai }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Tugas terselesaikan
                            </p>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- PROGRESS PENANGANAN -->
            <!-- ===================================================== -->

            <div class="rounded-xl bg-white p-6 shadow-sm">

                <div class="mb-5 flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">

                        <TrendingUp
                            class="h-5 w-5 text-green-600"
                        />

                    </div>


                    <div>

                        <h3 class="font-semibold text-gray-800">
                            Progress Penanganan
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Persentase status tugas Anda
                        </p>

                    </div>

                </div>


                <!-- DITUGASKAN -->

                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between">

                        <span class="text-sm text-gray-600">
                            Ditugaskan
                        </span>

                        <span class="text-sm font-medium text-gray-800">
                            {{ percentage(statistics.ditugaskan) }}%
                        </span>

                    </div>


                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">

                        <div
                            class="h-full rounded-full bg-yellow-500 transition-all"
                            :style="{
                                width: `${percentage(statistics.ditugaskan)}%`
                            }"
                        ></div>

                    </div>

                </div>


                <!-- DIPROSES -->

                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between">

                        <span class="text-sm text-gray-600">
                            Diproses
                        </span>

                        <span class="text-sm font-medium text-gray-800">
                            {{ percentage(statistics.diproses) }}%
                        </span>

                    </div>


                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">

                        <div
                            class="h-full rounded-full bg-blue-500 transition-all"
                            :style="{
                                width: `${percentage(statistics.diproses)}%`
                            }"
                        ></div>

                    </div>

                </div>


                <!-- SELESAI -->

                <div>

                    <div class="mb-2 flex items-center justify-between">

                        <span class="text-sm text-gray-600">
                            Selesai
                        </span>

                        <span class="text-sm font-medium text-gray-800">
                            {{ percentage(statistics.selesai) }}%
                        </span>

                    </div>


                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">

                        <div
                            class="h-full rounded-full bg-green-500 transition-all"
                            :style="{
                                width: `${percentage(statistics.selesai)}%`
                            }"
                        ></div>

                    </div>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- INFORMASI -->
            <!-- ===================================================== -->

            <div class="rounded-xl border border-green-100 bg-green-50 p-5">

                <div class="flex items-start gap-3">

                    <CheckCircle2
                        class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                    />

                    <div>

                        <p class="text-sm font-medium text-green-800">
                            Performa Penanganan
                        </p>

                        <p class="mt-1 text-xs leading-5 text-green-700">

                            Anda telah menyelesaikan
                            <strong>
                                {{ statistics.selesai }}
                            </strong>
                            dari
                            <strong>
                                {{ statistics.total }}
                            </strong>
                            total tugas yang diberikan.

                        </p>

                    </div>

                </div>

            </div>


        </div>

    </AuthenticatedLayout>

</template>