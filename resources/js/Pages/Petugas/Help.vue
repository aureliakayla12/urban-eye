
<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import {
    BookOpen,
    ClipboardList,
    Map,
    History,
    ChartNoAxesCombined,
    Settings,
    CircleHelp,
    Headset,
    ChevronDown,
    ArrowRight,
    CheckCircle2,
    ShieldCheck,
    MessageCircle,
} from 'lucide-vue-next'

const page = usePage()

const openFaq = ref(0)

const toggleFaq = (index) => {
    openFaq.value = openFaq.value === index ? null : index
}

const whatsappLink = computed(() => {
    const name = page.props.auth?.user?.name ?? 'Pengguna UrbanEye'
    const role = page.props.auth?.role ?? 'Petugas'

    const message = `Halo Admin UrbanEye,

Saya ${name} (${role}) dan ingin meminta bantuan terkait aplikasi UrbanEye.

Halaman: Bantuan
Kendala: [Tuliskan kendala Anda di sini]

Terima kasih.`

    return `https://wa.me/6289508423660?text=${encodeURIComponent(message)}`
})

const faqs = [
    {
        question: 'Bagaimana cara menerima tugas baru?',
        answer: 'Buka menu Tugas Saya, pilih tugas yang ingin dilihat, lalu tekan tombol Terima Tugas jika tersedia. Status tugas akan berubah menjadi Diproses ketika tugas mulai ditangani.',
    },
    {
        question: 'Bagaimana cara menyelesaikan laporan?',
        answer: 'Buka detail tugas yang sedang diproses. Setelah pekerjaan selesai, tekan tombol Selesaikan Tugas atau ubah status menjadi Selesai sesuai tombol yang tersedia pada halaman tersebut.',
    },
    {
        question: 'Di mana saya bisa melihat tugas yang sudah selesai?',
        answer: 'Buka menu Riwayat Penanganan untuk melihat daftar tugas dan status penanganannya.',
    },
    {
        question: 'Bagaimana cara melihat lokasi laporan?',
        answer: 'Buka menu Peta Laporan. Lokasi ditampilkan pada peta apabila laporan memiliki data koordinat yang tersedia.',
    },
    {
        question: 'Bagaimana cara melihat kinerja saya?',
        answer: 'Buka menu Statistik untuk melihat total tugas, jumlah tugas yang ditugaskan, sedang diproses, dan sudah selesai.',
    },
    {
        question: 'Apa yang harus dilakukan jika aplikasi mengalami error?',
        answer: 'Catat halaman yang mengalami masalah dan pesan error yang tampil. Ambil screenshot jika memungkinkan, lalu hubungi Admin melalui tombol WhatsApp di halaman ini.',
    },
]
</script>

<template>
    <Head title="Pengaturan Petugas" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#101827] sm:text-2xl">
                    Pengaturan Petugas
                </h1>

                <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">
                    Kelola informasi profil dan keamanan akun Anda.
                </p>
            </div>
        </template>

        <div class="space-y-6 p-6 lg:p-8">
            <div
                class="flex flex-col gap-4 rounded-2xl bg-[#E8F5E9] p-6 sm:flex-row sm:items-center"
            >
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white"
                >
                    <BookOpen class="h-7 w-7 text-green-700" />
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-green-900">
                        Selamat Datang di Pusat Bantuan!
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-green-800">
                        Temukan panduan untuk menerima tugas, menangani laporan,
                        dan menggunakan fitur Petugas UrbanEye.
                    </p>
                </div>
            </div>

            <section class="space-y-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Akses Cepat
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Langsung buka menu yang Anda perlukan.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <Link
                        :href="route('petugas.tasks.index')"
                        class="group rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100">
                                <ClipboardList class="h-5 w-5 text-green-700" />
                            </div>

                            <ArrowRight class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-green-600" />
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-800">
                            Tugas Saya
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            Lihat tugas dan mulai penanganan laporan.
                        </p>
                    </Link>

                    <Link
                        :href="route('petugas.map.index')"
                        class="group rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100">
                                <Map class="h-5 w-5 text-blue-700" />
                            </div>

                            <ArrowRight class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-green-600" />
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-800">
                            Peta Laporan
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            Lihat lokasi laporan pada peta.
                        </p>
                    </Link>

                    <Link
                        :href="route('petugas.history.index')"
                        class="group rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-purple-100">
                                <History class="h-5 w-5 text-purple-700" />
                            </div>

                            <ArrowRight class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-green-600" />
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-800">
                            Riwayat Penanganan
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            Periksa tugas dan status penanganan sebelumnya.
                        </p>
                    </Link>

                    <Link
                        :href="route('petugas.statistics.index')"
                        class="group rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-yellow-100">
                                <ChartNoAxesCombined class="h-5 w-5 text-yellow-700" />
                            </div>

                            <ArrowRight class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-green-600" />
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-800">
                            Statistik
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            Lihat ringkasan kinerja dan status tugas.
                        </p>
                    </Link>

                    <Link
                        :href="route('petugas.settings.index')"
                        class="group rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-gray-100">
                                <Settings class="h-5 w-5 text-gray-700" />
                            </div>

                            <ArrowRight class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-green-600" />
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-800">
                            Pengaturan Akun
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            Kelola informasi profil dan keamanan akun.
                        </p>
                    </Link>

                </div>
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                        <CheckCircle2 class="h-5 w-5 text-green-700" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-800">
                            Langkah Penanganan Laporan
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Ikuti alur berikut saat menangani laporan.
                        </p>
                    </div>
                </div>

                <div class="mt-6 space-y-5">

                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">
                            1
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Periksa tugas
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Buka Tugas Saya, pilih tugas, lalu periksa
                                deskripsi, lokasi, dan informasi laporan.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">
                            2
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Mulai penanganan
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Terima tugas dan ubah status menjadi Diproses
                                saat mulai mengerjakan laporan.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">
                            3
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Selesaikan tugas
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Setelah pekerjaan selesai, ubah status menjadi
                                Selesai dan isi catatan penanganan jika tersedia.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">
                            4
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Periksa riwayat
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-gray-500">
                                Gunakan Riwayat Penanganan dan Statistik untuk
                                melihat status serta ringkasan tugas Anda.
                            </p>
                        </div>
                    </div>

                </div>
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                        <CircleHelp class="h-5 w-5 text-blue-700" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-800">
                            Pertanyaan Umum
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Klik pertanyaan untuk melihat jawabannya.
                        </p>
                    </div>
                </div>

                <div class="mt-5 divide-y divide-gray-100">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="faq.question"
                        class="py-1"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 py-4 text-left"
                            :aria-expanded="openFaq === index"
                            @click="toggleFaq(index)"
                        >
                            <span class="text-sm font-medium text-gray-800">
                                {{ faq.question }}
                            </span>

                            <ChevronDown
                                class="h-5 w-5 shrink-0 text-gray-400 transition-transform"
                                :class="{
                                    'rotate-180 text-green-600': openFaq === index
                                }"
                            />
                        </button>

                        <div
                            v-if="openFaq === index"
                            class="pb-4 pr-6 text-sm leading-6 text-gray-500"
                        >
                            {{ faq.answer }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-green-100 bg-green-50 p-5 sm:p-6">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white">
                        <Headset class="h-6 w-6 text-green-700" />
                    </div>

                    <div class="flex-1">
                        <h2 class="font-semibold text-green-900">
                            Masih Mengalami Kendala?
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-green-800">
                            Hubungi Admin UrbanEye melalui WhatsApp untuk meminta
                            bantuan terkait akun atau kendala aplikasi.
                        </p>

                        <a
                            :href="whatsappLink"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-700"
                        >
                            <MessageCircle class="h-5 w-5" />
                            Hubungi Admin via WhatsApp
                            <ArrowRight class="h-4 w-4" />
                        </a>

                        <div class="mt-4 flex items-start gap-2 text-xs text-green-800">
                            <ShieldCheck class="mt-0.5 h-4 w-4 shrink-0" />

                            <p>
                                Jangan bagikan kata sandi atau informasi
                                rahasia akun kepada orang lain.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </AuthenticatedLayout>
</template>
