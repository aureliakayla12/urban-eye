<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
    Trophy,
    Medal,
    Star,
    Leaf,
    FileText,
    CircleCheck,
    ArrowRight,
} from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
    leaderboard: {
        type: Array,
        default: () => [],
    },
    currentUser: {
        type: Object,
        default: () => ({
            id: null,
            name: '',
            points: 0,
            rank: null,
            hasReports: false,
            reportCount: 0,
            badges: [],
        }),
    },
})

// Backend should pass hasReports/reportCount and only ranked contributors in leaderboard.
const hasReports = computed(() =>
    props.currentUser.hasReports === true ||
    props.currentUser.has_reports === true ||
    Number(props.currentUser.reportCount ?? props.currentUser.report_count ?? 0) > 0,
)

const hasRank = computed(() => hasReports.value && props.currentUser.rank !== null && props.currentUser.rank !== undefined && Number(props.currentUser.rank) > 0)
const topThree = computed(() => props.leaderboard.slice(0, 3))

function initials(name) {
    return String(name || '?').trim().charAt(0).toUpperCase() || '?'
}

function rankClass(rank) {
    if (rank === 1) return 'bg-amber-100 text-amber-700'
    if (rank === 2) return 'bg-slate-200 text-slate-700'
    if (rank === 3) return 'bg-orange-100 text-orange-800'
    return 'bg-slate-100 text-slate-600'
}

function medalClass(rank) {
    if (rank === 1) return 'text-amber-500'
    if (rank === 2) return 'text-slate-400'
    return 'text-orange-500'
}
</script>

<template>
    <Head title="Leaderboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#101827] sm:text-2xl">Leaderboard</h1>
                <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">
                    Apresiasi untuk warga yang berkontribusi bagi lingkungan.
                </p>
            </div>
        </template>

        <div class="min-h-screen bg-[#F3F6F8] px-4 py-5 sm:px-6 sm:py-7 lg:px-8">
            <div class="mx-auto max-w-[1400px] space-y-5">
                

                <section class="grid grid-cols-1 gap-5 xl:grid-cols-[1.25fr_0.9fr]">
                    <!-- Current user summary -->
                    <article class="relative overflow-hidden rounded-2xl bg-[#102A43] p-6 text-white shadow-sm sm:p-7">
                        <Leaf class="pointer-events-none absolute -bottom-8 -right-4 h-40 w-40 text-white opacity-[0.06]" />
                        <div class="relative flex h-full min-h-[220px] flex-col justify-between gap-7">
                            <div class="flex items-center gap-4 sm:gap-5">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-2xl font-extrabold text-emerald-700 sm:h-20 sm:w-20">
                                    {{ initials(currentUser.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm text-white/65">Peringkat Anda</p>
                                    <p v-if="hasRank" class="mt-1 text-2xl font-extrabold sm:text-3xl">#{{ currentUser.rank }}</p>
                                    <p v-else class="mt-1 text-xl font-extrabold sm:text-2xl">Peringkat belum tersedia</p>
                                    <p class="mt-1 text-sm text-white/85">
                                        {{ hasReports ? 'Terima kasih sudah berkontribusi di UrbanEye.' : 'Kirim laporan pertamamu untuk mulai berkontribusi di leaderboard.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-end justify-between gap-4 border-t border-white/15 pt-5">
                                <div>
                                    <p class="text-sm text-white/65">Total Poin</p>
                                    <p class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ Number(currentUser.points || 0).toLocaleString('id-ID') }} poin</p>
                                </div>
                                <Link
                                    v-if="!hasReports"
                                    :href="route('masyarakat.reports.create')"
                                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                                >
                                    Buat Laporan <ArrowRight :size="17" />
                                </Link>
                            </div>
                        </div>
                    </article>

                    <!-- Top three -->
                    <article class="rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-extrabold text-[#102A43]">3 Kontributor Teratas</h3>
                            <p class="mt-1 text-sm text-slate-500">Pengguna dengan poin tertinggi dari laporan terverifikasi.</p>
                        </div>

                        <div v-if="topThree.length" class="divide-y divide-slate-100">
                            <div v-for="(item, index) in topThree" :key="item.id" class="flex items-center gap-3 py-3 first:pt-1 last:pb-0">
                                <div class="flex w-8 shrink-0 justify-center">
                                    <Trophy v-if="Number(item.rank ?? index + 1) === 1" :size="25" :class="medalClass(1)" />
                                    <Medal v-else :size="25" :class="medalClass(Number(item.rank ?? index + 1))" />
                                </div>
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-extrabold text-emerald-700">
                                    {{ initials(item.name) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ item.name }}</p>
                                    <p class="text-xs text-slate-400">Peringkat #{{ item.rank ?? index + 1 }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold text-emerald-700">{{ Number(item.points || 0).toLocaleString('id-ID') }} poin</p>
                            </div>
                        </div>
                        <div v-else class="flex min-h-[170px] flex-col items-center justify-center rounded-xl bg-slate-50 px-5 text-center">
                            <Trophy class="h-9 w-9 text-slate-300" />
                            <p class="mt-3 text-sm font-semibold text-slate-600">Belum ada kontributor yang masuk peringkat.</p>
                            <p class="mt-1 text-xs text-slate-400">Peringkat akan muncul setelah ada kontribusi yang tercatat.</p>
                        </div>
                    </article>
                </section>

                <section class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[1.65fr_0.9fr]">
                    <!-- Rankings table -->
                    <article class="rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-5">
                            <h3 class="text-lg font-extrabold text-[#102A43]">Peringkat Kontributor</h3>
                            <p class="mt-1 text-sm text-slate-500">Daftar pengguna yang telah membuat laporan dan mendapatkan poin.</p>
                        </div>

                        <div v-if="leaderboard.length" class="overflow-x-auto">
                            <div class="mb-2 hidden min-w-[580px] grid-cols-[56px_minmax(170px,1fr)_120px_190px] gap-3 rounded-lg bg-slate-50 px-4 py-3 text-xs font-bold text-slate-500 md:grid">
                                <span>#</span><span>Pengguna</span><span>Total Poin</span><span>Badge</span>
                            </div>
                            <div class="min-w-[580px] divide-y divide-slate-100">
                                <div
                                    v-for="(item, index) in leaderboard"
                                    :key="item.id"
                                    class="grid grid-cols-[56px_minmax(170px,1fr)_120px_190px] items-center gap-3 rounded-lg px-4 py-3 transition hover:bg-slate-50"
                                    :class="item.id === currentUser.id ? 'bg-emerald-50 hover:bg-emerald-50' : ''"
                                >
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-extrabold" :class="rankClass(Number(item.rank ?? index + 1))">
                                        {{ item.rank ?? index + 1 }}
                                    </div>
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-extrabold text-slate-600">
                                            {{ initials(item.name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-slate-800">
                                                {{ item.name }}
                                                <span v-if="item.id === currentUser.id" class="ml-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Anda</span>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-extrabold text-emerald-700">{{ Number(item.points || 0).toLocaleString('id-ID') }} poin</p>
                                    <div>
                                        <span v-if="item.badges?.length" class="inline-flex max-w-full items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800">
                                            <Leaf :size="14" class="shrink-0" />
                                            <span class="truncate">{{ item.badges[0].name }}</span>
                                        </span>
                                        <span v-else class="text-sm text-slate-400">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex min-h-[270px] flex-col items-center justify-center rounded-xl bg-slate-50 px-5 text-center">
                            <Trophy class="h-10 w-10 text-slate-300" />
                            <p class="mt-4 text-sm font-bold text-slate-700">Belum ada data leaderboard.</p>
                            <p class="mt-1 text-sm text-slate-500">Buat laporan dan tunggu verifikasi untuk mulai berkontribusi.</p>
                        </div>
                    </article>

                    <!-- How to join -->
                    <aside class="rounded-2xl border border-emerald-50 bg-gradient-to-b from-[#F0FBF4] to-white p-5 shadow-sm sm:p-6">
                        <h3 class="text-lg font-extrabold text-[#102A43]">Cara mendapatkan peringkat</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Ikuti langkah berikut untuk muncul di leaderboard dan ikut berkontribusi bagi lingkungan.</p>

                        <div class="mt-6 space-y-5">
                            <div class="flex gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-extrabold text-emerald-700">1</div>
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-emerald-100 bg-white text-emerald-700"><FileText :size="21" /></div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-800">Buat laporan</p>
                                    <p class="mt-1 text-sm leading-5 text-slate-500">Laporkan permasalahan lingkungan di sekitarmu melalui aplikasi.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-extrabold text-emerald-700">2</div>
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-emerald-100 bg-white text-emerald-700"><CircleCheck :size="22" /></div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-800">Laporan diverifikasi valid</p>
                                    <p class="mt-1 text-sm leading-5 text-slate-500">Laporan akan diperiksa Admin. Jika valid, kamu akan memperoleh poin.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-extrabold text-emerald-700">3</div>
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-emerald-100 bg-white text-emerald-700"><Star :size="21" /></div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-800">Kumpulkan poin dan naik peringkat</p>
                                    <p class="mt-1 text-sm leading-5 text-slate-500">Semakin banyak kontribusi valid, semakin tinggi posisimu di leaderboard.</p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
