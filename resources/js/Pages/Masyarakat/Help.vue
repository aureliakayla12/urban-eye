<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
    Search,
    ChevronDown,
    MessageCircle,
    CircleHelp,
    Leaf,
    ArrowRight,
    Info,
} from 'lucide-vue-next'

const props = defineProps({
    whatsappUrl: {
        type: String,
        default: null,
    },
})

const search = ref('')
const activeFaq = ref(null)

const faqs = [
    {
        question: 'Bagaimana cara membuat laporan?',
        answer: 'Buka menu Buat Laporan, lengkapi informasi permasalahan lingkungan, pilih kategori dan lokasi, unggah foto, lalu kirim laporan.',
    },
    {
        question: 'Jenis laporan apa saja yang bisa dilaporkan?',
        answer: 'Kamu dapat melaporkan permasalahan lingkungan sesuai kategori yang tersedia pada formulir Buat Laporan.',
    },
    {
        question: 'Bagaimana cara melihat status laporan saya?',
        answer: 'Buka menu Riwayat Laporan, kemudian pilih laporan untuk melihat status dan riwayat penanganannya.',
    },
    {
        question: 'Apa arti status laporan?',
        answer: 'Status menunjukkan perkembangan laporan, seperti menunggu, diproses, selesai, atau ditolak. Periksa detail laporan untuk informasi lebih lanjut.',
    },
    {
        question: 'Bagaimana cara mendapatkan poin?',
        answer: 'Poin diberikan sesuai ketentuan aktivitas UrbanEye. Periksa menu Reward Saya untuk melihat informasi poin yang kamu miliki.',
    },
    {
        question: 'Bagaimana cara menukarkan poin dengan reward?',
        answer: 'Buka menu Reward Saya, pilih reward yang tersedia, lalu ikuti ketentuan penukaran.',
    },
    {
        question: 'Apakah laporan yang sudah dikirim bisa diedit atau dihapus?',
        answer: 'Perubahan laporan bergantung pada fitur yang tersedia. Jika tidak menemukan opsi yang sesuai, hubungi admin UrbanEye melalui WhatsApp.',
    },
    {
        question: 'Apa yang harus dilakukan jika laporan belum ditindaklanjuti?',
        answer: 'Periksa status laporan terlebih dahulu. Jika membutuhkan informasi lebih lanjut, hubungi admin dan sertakan informasi laporanmu.',
    },
]

const filteredFaqs = computed(() => {
    const keyword = search.value.trim().toLowerCase()

    return faqs
        .map((faq, originalIndex) => ({ ...faq, originalIndex }))
        .filter((faq) =>
            `${faq.question} ${faq.answer}`
                .toLowerCase()
                .includes(keyword),
        )
})

function toggleFaq(index) {
    activeFaq.value = activeFaq.value === index ? null : index
}
</script>

<template>
    <Head title="Bantuan" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Bantuan
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Temukan jawaban atau hubungi admin jika kamu mengalami kendala.
                </p>
            </div>
        </template>

        <div class="space-y-6">
            <section
                class="relative isolate overflow-hidden rounded-2xl
                       border border-green-100 bg-emerald-50
                       shadow-sm"
            >
                <img
                    src="/images/urbaneye-help-banner.png"
                    alt=""
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 right-0
                           -z-10 h-full w-[62%] object-cover object-center
                           opacity-90"
                />

                <div
                    class="pointer-events-none absolute inset-0 -z-10
                           bg-gradient-to-r from-emerald-50
                           via-emerald-50/95 to-emerald-50/10"
                />

                <div class="px-6 py-7 sm:px-8 sm:py-9">
                    <div
                        class="mb-3 flex items-center gap-2
                               text-sm font-semibold text-green-800"
                    >
                        <Leaf :size="18" />
                        PUSAT BANTUAN
                    </div>

                    <h1
                        class="max-w-xl text-2xl font-bold tracking-tight
                               text-slate-900 sm:text-3xl"
                    >
                        Ada yang bisa kami
                        <span class="text-green-700">bantu?</span>
                    </h1>

                    <p class="mt-2 text-sm text-slate-600 sm:text-base">
                        Cari jawaban seputar penggunaan UrbanEye.
                    </p>

                    <div class="relative mt-6 max-w-xl">
                        <Search
                            :size="21"
                            class="absolute left-4 top-1/2
                                   -translate-y-1/2 text-slate-500"
                        />
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari pertanyaan, misalnya cara membuat laporan..."
                            class="w-full rounded-xl border border-slate-200
                                   bg-white/95 py-3.5 pl-12 pr-4 text-sm
                                   text-slate-800 shadow-sm outline-none
                                   transition placeholder:text-slate-400
                                   focus:border-green-600
                                   focus:ring-2 focus:ring-green-100"
                        />
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
                <section class="min-w-0 xl:col-span-2">
                    <div class="mb-4 flex items-center gap-3">
                        <CircleHelp
                            :size="25"
                            class="shrink-0 text-green-800"
                        />
                        <h3 class="text-lg font-bold text-slate-900">
                            Pertanyaan yang Sering Diajukan
                        </h3>
                    </div>

                    <div v-if="filteredFaqs.length" class="space-y-2">
                        <div
                            v-for="faq in filteredFaqs"
                            :key="faq.question"
                            class="overflow-hidden rounded-xl border
                                   border-slate-100 bg-white shadow-sm
                                   transition hover:border-green-200"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center
                                       justify-between gap-4 px-4 py-4
                                       text-left sm:px-5"
                                :aria-expanded="
                                    activeFaq === faq.originalIndex
                                "
                                @click="toggleFaq(faq.originalIndex)"
                            >
                                <span
                                    class="text-sm font-medium leading-6
                                           text-slate-800"
                                >
                                    {{ faq.question }}
                                </span>

                                <ChevronDown
                                    :size="19"
                                    class="shrink-0 text-slate-500
                                           transition-transform"
                                    :class="{
                                        'rotate-180':
                                            activeFaq === faq.originalIndex,
                                    }"
                                />
                            </button>

                            <div
                                v-if="activeFaq === faq.originalIndex"
                                class="border-t border-slate-100 px-4
                                       py-4 sm:px-5"
                            >
                                <p
                                    class="text-sm leading-6 text-slate-600"
                                >
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-xl border border-dashed
                               border-slate-300 bg-white px-5 py-10
                               text-center"
                    >
                        <Search
                            :size="28"
                            class="mx-auto mb-3 text-slate-400"
                        />
                        <p class="font-semibold text-slate-800">
                            Pertanyaan tidak ditemukan
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            Coba gunakan kata kunci yang berbeda.
                        </p>
                        <button
                            type="button"
                            class="mt-3 text-sm font-medium text-green-800
                                   hover:underline"
                            @click="search = ''"
                        >
                            Hapus pencarian
                        </button>
                    </div>
                </section>
                
                <aside
                    class="rounded-2xl border border-green-100
                           bg-gradient-to-br from-green-50 to-emerald-50
                           p-5 sm:p-6"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center
                               rounded-full bg-green-600 text-white
                               shadow-sm"
                    >
                        <MessageCircle :size="34" />
                    </div>

                    <h3 class="mt-5 text-xl font-bold text-slate-900">
                        Masih ada pertanyaan?
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Jika kamu belum menemukan jawaban yang dicari,
                        hubungi admin UrbanEye melalui WhatsApp untuk
                        mendapatkan bantuan lebih lanjut.
                    </p>

                    <a
                        v-if="props.whatsappUrl"
                        :href="props.whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-5 flex w-full items-center gap-2
                               rounded-xl bg-green-800 px-4 py-4
                               text-sm font-semibold text-white
                               shadow-sm transition hover:bg-green-900
                               focus:outline-none focus:ring-2
                               focus:ring-green-600 focus:ring-offset-2"
                    >
                        <MessageCircle :size="22" class="shrink-0" />
                        <span class="flex-1 text-center">
                            Hubungi Admin via WhatsApp
                        </span>
                        <ArrowRight :size="20" class="shrink-0" />
                    </a>

                    <div
                        v-else
                        class="mt-5 rounded-xl border border-green-200
                               bg-white p-3 text-sm text-slate-600"
                    >
                        <Info
                            :size="20"
                            class="mb-2 text-green-800"
                        />
                        Kontak WhatsApp admin belum dikonfigurasi.
                    </div>

                    <p
                        class="mt-4 flex items-start gap-2
                               text-xs leading-5 text-slate-500"
                    >
                        <Info :size="16" class="mt-0.5 shrink-0" />
                        Kamu akan diarahkan ke WhatsApp untuk melanjutkan
                        percakapan.
                    </p>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
