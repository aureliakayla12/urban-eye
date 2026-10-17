
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

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

// Mendeteksi halaman asal
const page = usePage()

const fromHistory = computed(() => {
    return page.url.includes('from=history')
})

// Status tugas
const status = computed(() => {
    return props.task?.status ?? 'ditugaskan'
})

const statusText = computed(() => {
    return {
        ditugaskan: 'Ditugaskan',
        diproses: 'Diproses',
        selesai: 'Selesai',
    }[status.value] ?? status.value
})

// Prioritas laporan
const priority = computed(() => {
    const reportPriority =
        props.task?.report?.priority ?? props.task?.priority

    if (reportPriority && String(reportPriority).trim()) {
        const normalized = String(reportPriority).trim().toLowerCase()

        if (
            ['mendesak', 'urgent', 'high', 'tinggi'].includes(normalized)
        ) {
            return 'mendesak'
        }

        if (
            ['sedang', 'medium', 'moderate'].includes(normalized)
        ) {
            return 'sedang'
        }

        if (['rendah', 'low'].includes(normalized)) {
            return 'rendah'
        }
    }

    // Pemetaan sementara sampai prioritas tersedia di database.
    const title = (props.task?.report?.title ?? '').toLowerCase()

    if (title.includes('sampah')) return 'mendesak'
    if (title.includes('jalan berlubang')) return 'sedang'
    if (title.includes('drainase')) return 'rendah'

    return 'belum ditentukan'
})

const priorityText = computed(() => {
    return {
        mendesak: 'Mendesak',
        sedang: 'Sedang',
        rendah: 'Rendah',
    }[priority.value] ?? 'Prioritas belum ditentukan'
})

const priorityClass = computed(() => {
    return {
        mendesak: 'bg-red-100 text-red-600',
        sedang: 'bg-orange-100 text-orange-600',
        rendah: 'bg-blue-100 text-blue-600',
    }[priority.value] ?? 'bg-gray-100 text-gray-600'
})

// Foto laporan
const reportImageUrl = computed(() => {
    const photo =
        props.task?.report?.photo ??
        props.task?.report?.images?.[0]?.image_path ??
        props.task?.report?.images?.[0]?.path

    if (typeof photo !== 'string' || !photo.trim()) {
        return null
    }

    const image = photo.trim()

    if (/^https?:\/\//i.test(image)) {
        return image
    }

    if (image.startsWith('/')) {
        return image
    }

    const cleanedPath = image
        .replace(/^public\//, '')
        .replace(/^storage\//, '')

    return `/storage/${cleanedPath}`
})

// CATATAN PENANGANAN
const catatanPenanganan = ref(props.task?.note ?? '')
const isSaving = ref(false)

const noteError = computed(() => {
    return page.props.errors?.note ?? null
})

// Sinkronkan catatan saat data tugas diperbarui oleh server.
watch(
    () => props.task?.note,
    (note) => {
        catatanPenanganan.value = note ?? ''
    },
)

// Terima tugas
const terimaTugas = () => {
    router.put(
        `/petugas/tasks/${props.task.id}`,
        {
            status: 'diproses',
        },
        {
            preserveScroll: true,
        },
    )
}

// Simpan catatan tanpa menyelesaikan tugas.
const simpanCatatan = () => {
    router.put(
        `/petugas/tasks/${props.task.id}`,
        {
            status: status.value,
            note: catatanPenanganan.value,
        },
        {
            preserveScroll: true,
            onStart: () => {
                isSaving.value = true
            },
            onFinish: () => {
                isSaving.value = false
            },
        },
    )
}

// Selesaikan tugas sekaligus simpan catatan.
const selesaikanTugas = () => {
    router.put(
        `/petugas/tasks/${props.task.id}`,
        {
            status: 'selesai',
            note: catatanPenanganan.value,
        },
        {
            preserveScroll: true,
        },
    )
}
</script>

<template>
    <Head title="Detail Laporan" />

    <AuthenticatedLayout>
        <div class="p-5 lg:p-8">
            <!-- HEADER -->
            <div class="mb-6">
                <Link
                    :href="
                        fromHistory
                            ? route('petugas.history.index')
                            : route('petugas.tasks.index')
                    "
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-green-600"
                >
                    <ArrowLeft class="h-4 w-4" />

                    {{
                        fromHistory
                            ? 'Kembali ke Riwayat Penanganan'
                            : 'Kembali ke Tugas Saya'
                    }}
                </Link>

                <h1 class="mt-4 text-2xl font-semibold text-gray-800">
                    Detail Laporan
                </h1>
            </div>

            <!-- KONTEN -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- BAGIAN KIRI -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- FOTO LAPORAN -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                        <div
                            class="flex h-[320px] w-full items-center justify-center bg-gray-100"
                        >
                            <img
                                v-if="reportImageUrl"
                                :src="reportImageUrl"
                                :alt="task?.report?.title ?? 'Foto laporan'"
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
                        <div
                            class="flex items-start justify-between gap-4"
                        >
                            <div class="min-w-0">
                                <h2
                                    class="text-xl font-semibold text-gray-800"
                                >
                                    {{ task?.report?.title ?? 'Tanpa judul' }}
                                </h2>

                                <div
                                    class="mt-2 flex items-start gap-2 text-sm text-gray-500"
                                >
                                    <MapPin
                                        class="mt-0.5 h-4 w-4 shrink-0"
                                    />

                                    <span>
                                        {{
                                            task?.report?.address ??
                                            'Lokasi belum tersedia'
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- BADGE PRIORITAS -->
                            <span
                                class="shrink-0 rounded-full px-3 py-1 text-xs font-medium"
                                :class="priorityClass"
                            >
                                {{ priorityText }}
                            </span>
                        </div>

                        <div class="my-5 border-t border-gray-100"></div>

                        <!-- DESKRIPSI -->
                        <div>
                            <h3
                                class="text-sm font-semibold text-gray-800"
                            >
                                Deskripsi
                            </h3>

                            <p
                                class="mt-2 text-sm leading-6 text-gray-600"
                            >
                                {{
                                    task?.report?.description ??
                                    'Tidak ada deskripsi.'
                                }}
                            </p>
                        </div>

                        <!-- INFORMASI LAPORAN -->
                        <div
                            class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2"
                        >
                            <!-- TANGGAL -->
                            <div class="rounded-lg bg-gray-50 p-4">
                                <div
                                    class="flex items-center gap-2 text-gray-500"
                                >
                                    <CalendarDays class="h-4 w-4" />

                                    <span class="text-xs">
                                        Tanggal Laporan
                                    </span>
                                </div>

                                <p
                                    class="mt-2 break-words text-sm font-medium text-gray-800"
                                >
                                    {{ task?.report?.created_at ?? '-' }}
                                </p>
                            </div>

                            <!-- PELAPOR -->
                            <div class="rounded-lg bg-gray-50 p-4">
                                <div
                                    class="flex items-center gap-2 text-gray-500"
                                >
                                    <User class="h-4 w-4" />

                                    <span class="text-xs">
                                        Pelapor
                                    </span>
                                </div>

                                <p
                                    class="mt-2 text-sm font-medium text-gray-800"
                                >
                                    {{ task?.report?.user?.name ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN KANAN -->
                <div class="space-y-6">
                    <!-- STATUS TUGAS -->
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
                                    <Clock3
                                        class="h-4 w-4 text-yellow-600"
                                    />
                                </div>

                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
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
                                    <Clock3
                                        class="h-4 w-4 text-blue-600"
                                    />
                                </div>

                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
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
                                    <CheckCircle2
                                        class="h-4 w-4 text-green-600"
                                    />
                                </div>

                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
                                        Selesai
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Tugas telah selesai ditangani
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 border-t border-gray-100 pt-4">
                            <p class="text-sm text-gray-500">
                                Status saat ini
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ statusText }}
                            </p>
                        </div>
                    </div>

                    <!-- AKSI TUGAS -->
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

                        <!-- SELESAIKAN TUGAS -->
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

                        <div
                            v-else
                            class="mt-5 rounded-lg bg-gray-100 px-4 py-3 text-center text-sm font-medium text-gray-600"
                        >
                            Status: {{ statusText }}
                        </div>
                    </div>

                    <!-- FORM CATATAN PENANGANAN -->
                    <div
                        v-if="status === 'diproses' && !fromHistory"
                        class="rounded-xl bg-white p-6 shadow-sm"
                    >
                        <h3 class="font-semibold text-gray-800">
                            Catatan Penanganan
                        </h3>

                        <p class="mt-2 text-sm leading-5 text-gray-500">
                            Tuliskan hasil penanganan laporan sebelum
                            menyelesaikan tugas.
                        </p>

                        <textarea
                            v-model="catatanPenanganan"
                            rows="4"
                            :disabled="isSaving"
                            placeholder="Contoh: Sampah sudah dibersihkan..."
                            class="mt-4 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-green-500 disabled:bg-gray-100"
                        ></textarea>

                        <p
                            v-if="noteError"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ noteError }}
                        </p>

                        <button
                            type="button"
                            @click="simpanCatatan"
                            :disabled="isSaving"
                            class="mt-3 w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{
                                isSaving
                                    ? 'Menyimpan...'
                                    : 'Simpan Catatan'
                            }}
                        </button>
                    </div>

                    <!-- CATATAN TERSIMPAN -->
                    <div
                        v-else-if="
                            task?.note ||
                            status === 'selesai' ||
                            fromHistory
                        "
                        class="rounded-xl bg-white p-6 shadow-sm"
                    >
                        <h3 class="font-semibold text-gray-800">
                            Catatan Penanganan
                        </h3>

                        <p
                            v-if="task?.note"
                            class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-600"
                        >
                            {{ task.note }}
                        </p>

                        <p
                            v-else
                            class="mt-3 text-sm text-gray-400"
                        >
                            Belum ada catatan penanganan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
