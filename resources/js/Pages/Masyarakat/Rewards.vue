<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import {
    Star,
    Trophy,
    CheckCircle2,
    XCircle,
    Gift,
    ChevronLeft,
    ChevronRight,
    Leaf,
    Medal,
    ArrowUpRight,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'

const props = defineProps({
    totalPoints: { type: Number, default: 0 },
    rank: { type: [Number, String], default: '-' },
    totalParticipants: { type: Number, default: 0 },
    badges: { type: Array, default: () => [] },
    rewards: { type: Array, default: () => [] },
    nextReward: { type: Object, default: null },
    pointHistory: { type: Array, default: () => [] },
    redemptionHistory: { type: Array, default: () => [] },
})

const page = usePage()
const processingRewardId = ref(null)
const failedRewardImages = ref([])
const rewardPage = ref(0)
const rewardsPerPage = 4

const featuredBadge = computed(() => props.badges[0] ?? null)

const badgeCount = computed(() => props.badges.length)

function redeemReward(reward) {
    if (!reward.can_redeem || processingRewardId.value !== null) return

    if (!window.confirm(`Tukar ${reward.name} dengan ${reward.point_cost} poin?`)) return

    processingRewardId.value = reward.id

    router.post(route('masyarakat.rewards.redeem', reward.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            processingRewardId.value = null
        },
    })
}

const rewardPages = computed(() => {
    const pages = []
    for (let i = 0; i < props.rewards.length; i += rewardsPerPage) {
        pages.push(props.rewards.slice(i, i + rewardsPerPage))
    }
    return pages
})

const visibleRewards = computed(() => rewardPages.value[rewardPage.value] ?? [])
const totalRewardPages = computed(() => rewardPages.value.length)
const hasInStockRewards = computed(() =>
    props.rewards.some(reward => Number(reward.stock) > 0)
)

const progressPercentage = computed(() => {
    if (!props.nextReward || !props.nextReward.target_points) return 100
    return Math.min(
        (Number(props.nextReward.current_points) / Number(props.nextReward.target_points)) * 100,
        100,
    )
})

function previousRewardPage() {
    if (rewardPage.value > 0) rewardPage.value--
}

function nextRewardPage() {
    if (rewardPage.value < totalRewardPages.value - 1) rewardPage.value++
}

function rewardImage(image) {
    if (!image) return null
    if (/^(https?:|data:)/i.test(image)) return image

    const normalized = String(image)
        .replace(/^\/+/, '')
        .replace(/^storage\//i, '')

    return `/storage/${normalized}`
}

function handleRewardImageError(id) {
    if (!failedRewardImages.value.includes(id)) {
        failedRewardImages.value.push(id)
    }
}

function redemptionStatus(status) {
    return {
        pending: 'Menunggu',
        approved: 'Disetujui',
        rejected: 'Ditolak',
        taken: 'Diambil',
    }[status] ?? status
}

function redemptionStatusClass(status) {
    return {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200',
        approved: 'bg-green-50 text-green-700 ring-green-200',
        rejected: 'bg-red-50 text-red-700 ring-red-200',
        taken: 'bg-blue-50 text-blue-700 ring-blue-200',
    }[status] ?? 'bg-gray-100 text-gray-700 ring-gray-200'
}
</script>

<template>
    <Head title="Reward Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#101827] sm:text-2xl">Reward Saya</h1>
                <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">
                    Kumpulkan poin dari laporan valid dan tukarkan dengan reward menarik
                </p>
            </div>
        </template>

        <div class="min-h-screen bg-[#F3F6F8] px-4 py-5 sm:px-6 sm:py-7 lg:px-8">
            <div class="mx-auto max-w-[1400px] space-y-6">
                <div
                    v-if="page.props.errors?.reward"
                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                    role="alert"
                >
                    {{ page.props.errors.reward }}
                </div>
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
                    role="status"
                >
                    {{ page.props.flash.success }}
                </div>

                <!-- Ringkasan poin, badge, dan peringkat -->
                <section class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:gap-6">
                    <article class="flex min-h-[122px] items-center gap-5 rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.06)] sm:p-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#E8F7ED]">
                            <Star class="h-8 w-8 text-[#16803A]" stroke-width="1.9" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-base font-bold text-[#151B26]">Poin Saya</p>
                            <div class="mt-0.5 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold leading-none text-[#16803A]">{{ totalPoints }}</span>
                                <span class="text-xs text-gray-500">poin</span>
                            </div>
                            <p class="mt-1.5 text-[11px] text-gray-500">Poin tersedia</p>
                        </div>
                    </article>

                    <article class="flex min-h-[122px] items-center gap-5 rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.06)] sm:p-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#E3F8EE]">
                            <Medal class="h-8 w-8 text-[#328BFF]" stroke-width="1.9" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-base font-bold text-[#151B26]">Badge Saya</p>
                            <div class="mt-1">
                                <span
                                    v-if="featuredBadge"
                                    class="text-xl font-extrabold leading-tight text-[#3986F6] sm:text-2xl"
                                >
                                    {{ featuredBadge.name }}
                                </span>
                                <span
                                    v-else
                                    class="text-base font-bold text-gray-500"
                                >
                                    Belum ada badge
                                </span>
                            </div>
                            <p class="mt-1.5 text-[11px] text-gray-500">
                                {{ badgeCount }} badge diperoleh
                            </p>
                        </div>
                    </article>

                   <article class="flex min-h-[122px] items-center gap-5 rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.06)] sm:p-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#FFF4DF]">
                            <Trophy class="h-8 w-8 text-[#F59E0B]" stroke-width="1.9" />
                        </div>

                    <div class="min-w-0">
                        <p class="text-base font-bold text-[#151B26]">Peringkat Saya</p>
                            <p
                                v-if="rank !== null && rank !== undefined && rank !== '-' && rank !== ''"
                                class="mt-0.5 text-3xl font-extrabold leading-none text-[#F59E0B]"
                            >
                                #{{ rank }}
                            </p>

                            <p v-else class="mt-1 text-base font-bold text-gray-500">
                                Belum tersedia
                            </p>

                            <p class="mt-1.5 text-[11px] text-gray-500">
                                <template v-if="rank !== null && rank !== undefined && rank !== '-' && rank !== '' && totalParticipants">
                                    dari {{ totalParticipants }} warga aktif
                                </template>
                                <template v-else>
                                    Buat laporan pertama untuk mendapatkan peringkat.
                                </template>
                            </p>
                        </div>
                    </article>
                </section>

                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1.85fr)_minmax(300px,1fr)]">
                    <div class="min-w-0 space-y-6">
                        <section class="rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.05)] sm:p-6">
                            <h2 class="text-lg font-bold text-[#151B26]">Progress Menuju Reward Berikutnya</h2>

                            <div v-if="nextReward" class="mt-6">
                                <div class="relative h-9 overflow-hidden rounded-md bg-[#E3E7EB]">
                                    <div
                                        class="absolute inset-y-0 left-0 rounded-md bg-[#16803A] transition-all duration-500"
                                        :style="{ width: `${progressPercentage}%` }"
                                    />
                                    <span
                                        class="absolute inset-0 flex items-center justify-center px-2 text-sm font-bold"
                                        :class="progressPercentage >= 35 ? 'text-white' : 'text-gray-600'"
                                    >
                                        {{ nextReward.current_points }} / {{ nextReward.target_points }} poin
                                    </span>
                                </div>
                                <p class="mt-6 text-sm font-medium text-gray-500">
                                    <template v-if="nextReward.remaining_points > 0">
                                        {{ nextReward.remaining_points }} poin lagi untuk mendapatkan
                                        <span class="font-semibold text-[#202938]">{{ nextReward.name }}</span>.
                                    </template>
                                    <template v-else>
                                        Kamu sudah mencapai target reward berikutnya.
                                    </template>
                                </p>
                            </div>
                            
                            <div
                                v-else-if="!rewards.length"
                                class="mt-5 rounded-xl bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600"
                            >
                                Belum ada reward aktif saat ini.
                            </div>

                            <div
                                v-else-if="hasInStockRewards"
                                class="mt-5 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
                            >
                                Kamu sudah memiliki cukup poin untuk semua reward yang stoknya tersedia.
                            </div>

                            <div
                                v-else
                                class="mt-5 rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700"
                            >
                                Stok reward sedang habis. Silakan cek kembali nanti.
                            </div>

                        </section>

                        <section class="rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.05)] sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h2 class="text-lg font-bold text-[#151B26]">Reward yang Bisa Ditukar</h2>
                                <span class="text-xs text-gray-500">{{ rewards.length }} reward tersedia</span>
                            </div>

                            <div v-if="visibleRewards.length" class="mt-5 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                                <article
                                    v-for="reward in visibleRewards"
                                    :key="reward.id"
                                    class="flex min-w-0 flex-col rounded-xl border border-[#EDF0F3] bg-white p-2.5 shadow-[0_3px_10px_rgba(15,23,42,0.04)] transition hover:-translate-y-0.5 hover:shadow-md sm:p-3"
                                >
                                    <div class="flex h-24 items-center justify-center overflow-hidden rounded-lg bg-[#F7F9FA] sm:h-28">
                                        <img
                                            v-if="reward.image && !failedRewardImages.includes(reward.id)"
                                            :src="rewardImage(reward.image)"
                                            :alt="reward.name"
                                            class="h-full w-full object-contain p-2"
                                            loading="lazy"
                                            @error="handleRewardImageError(reward.id)"
                                        />
                                        <Gift v-else class="h-11 w-11 text-[#CBD1D8]" stroke-width="1.7" />
                                    </div>
                                    <h3 class="mt-3 line-clamp-2 min-h-9 text-xs font-bold leading-4 text-[#151B26] sm:text-sm">
                                        {{ reward.name }}
                                    </h3>
                                    <p class="mt-1 text-xs font-medium text-gray-600">{{ reward.point_cost }} poin</p>
                                    <p class="mt-1 text-[11px] text-gray-500">Stok: {{ reward.stock }}</p>
                                    <button
                                        type="button"
                                        :disabled="!reward.can_redeem || processingRewardId !== null || reward.stock < 1"
                                        @click="redeemReward(reward)"
                                        class="mt-3 min-h-8 w-full rounded-md border px-2 py-1.5 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-[#16803A]/30"
                                        :class="reward.can_redeem && reward.stock > 0
                                            ? 'border-[#16803A] text-[#16803A] hover:bg-[#16803A] hover:text-white disabled:cursor-wait disabled:opacity-50'
                                            : 'cursor-not-allowed border-gray-300 text-gray-400'"
                                    >
                                        <span v-if="processingRewardId === reward.id">Memproses...</span>
                                        <span v-else-if="reward.stock < 1">Stok habis</span>
                                        <span v-else-if="!reward.can_redeem">Poin tidak cukup</span>
                                        <span v-else>Tukar</span>
                                    </button>
                                </article>
                            </div>
                            <div v-else class="mt-5 rounded-xl bg-[#F7F9FA] px-5 py-10 text-center">
                                <Gift class="mx-auto h-9 w-9 text-gray-300" />
                                <p class="mt-3 text-sm font-semibold text-gray-600">Belum ada reward yang tersedia.</p>
                                <p class="mt-1 text-xs text-gray-500">Reward aktif dari pengelola akan muncul di sini.</p>
                            </div>

                            <div v-if="totalRewardPages > 1" class="mt-6 flex items-center justify-center gap-5">
                                <button
                                    type="button"
                                    aria-label="Halaman reward sebelumnya"
                                    class="rounded-lg p-1 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30"
                                    :disabled="rewardPage === 0"
                                    @click="previousRewardPage"
                                >
                                    <ChevronLeft class="h-6 w-6" />
                                </button>
                                <div class="flex items-center gap-2" aria-label="Halaman reward">
                                    <button
                                        v-for="(_, index) in rewardPages"
                                        :key="index"
                                        type="button"
                                        :aria-label="`Halaman ${index + 1}`"
                                        :aria-current="index === rewardPage ? 'page' : undefined"
                                        class="h-2.5 w-2.5 rounded-full transition"
                                        :class="index === rewardPage ? 'bg-[#16803A]' : 'bg-[#DDE2E6] hover:bg-gray-400'"
                                        @click="rewardPage = index"
                                    />
                                </div>
                                <button
                                    type="button"
                                    aria-label="Halaman reward berikutnya"
                                    class="rounded-lg p-1 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30"
                                    :disabled="rewardPage === totalRewardPages - 1"
                                    @click="nextRewardPage"
                                >
                                    <ChevronRight class="h-6 w-6" />
                                </button>
                            </div>
                        </section>

                        <section class="relative isolate min-h-[150px] overflow-hidden rounded-2xl bg-[#EAF7EE] shadow-[0_6px_22px_rgba(15,23,42,0.05)] sm:min-h-[170px]">
                            <img
                                src="/images/urbaneye-reward-banner.png"
                                alt="Ilustrasi warga bersama menjaga lingkungan"
                                class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center"
                                loading="lazy"
                            />
                            <div class="relative z-10 flex min-h-[150px] items-center px-5 py-5 sm:min-h-[170px] sm:px-7">
                                <div class="max-w-[62%] rounded-xl bg-[#EAF7EE]/80 p-2 backdrop-blur-[1px] sm:max-w-[58%] sm:p-3">
                                    <div class="flex items-center gap-2 text-[#16803A]">
                                        <h2 class="text-sm font-extrabold leading-5 sm:text-base">Mari Wujudkan Lingkungan yang Lebih Bersih!</h2>
                                    </div>
                                    <p class="mt-2 text-[11px] leading-5 text-gray-600 sm:text-xs">
                                        Setiap laporan valid yang kamu kirim membantu menciptakan kota yang lebih bersih dan nyaman bagi semua.
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="min-w-0 space-y-6">
                        <section class="rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.05)] sm:p-6">
                            <h2 class="border-b border-gray-200 pb-3 text-lg font-bold text-[#151B26]">Cara Mendapatkan Poin</h2>
                            <div class="space-y-4 pt-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-50">
                                        <CheckCircle2 class="h-5 w-5 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-[#202938]">Laporan terverifikasi valid</p>
                                        <p class="mt-0.5 text-xs leading-5 text-gray-500">Setiap laporan yang terbukti valid akan mendapatkan 10 poin.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50">
                                        <XCircle class="h-5 w-5 text-red-500" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-[#202938]">Laporan tidak valid / hoaks</p>
                                        <p class="mt-0.5 text-xs leading-5 text-gray-500">Poin dapat dikurangi jika laporan terbukti tidak valid.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                                        <Star class="h-5 w-5 text-amber-500" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-[#202938]">Leaderboard</p>
                                        <p class="mt-0.5 text-xs leading-5 text-gray-500">Peringkat diperbarui berdasarkan total poin yang terkumpul.</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.05)] sm:p-6">
                            <div class="flex items-center justify-between gap-3 border-b border-gray-200 pb-3">
                                <h2 class="text-lg font-bold text-[#151B26]">Riwayat Poin</h2>
                                <span class="shrink-0 text-xs font-semibold text-[#16803A]">10 terbaru</span>
                            </div>

                            <div v-if="pointHistory.length" class="divide-y divide-gray-100">
                                <div v-for="item in pointHistory" :key="item.id" class="flex items-center gap-3 py-4 last:pb-0">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                                        :class="item.type === 'tambah' ? 'bg-green-50' : 'bg-red-50'"
                                    >
                                        <CheckCircle2 v-if="item.type === 'tambah'" class="h-5 w-5 text-green-600" />
                                        <XCircle v-else class="h-5 w-5 text-red-500" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-[#202938]">{{ item.description || 'Transaksi poin' }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ item.date }}</p>
                                    </div>
                                    <span class="shrink-0 text-base font-extrabold" :class="item.type === 'tambah' ? 'text-green-600' : 'text-red-500'">
                                        {{ item.type === 'tambah' ? '+' : '-' }}{{ item.points }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="mt-4 rounded-xl bg-[#F7F9FA] px-4 py-7 text-center">
                                <Trophy class="mx-auto h-7 w-7 text-gray-300" />
                                <p class="mt-2 text-xs text-gray-500">Belum ada riwayat poin.</p>
                            </div>
                        </section>

                        <section class="rounded-2xl bg-white p-5 shadow-[0_6px_22px_rgba(15,23,42,0.05)] sm:p-6">
                            <div class="flex items-center justify-between gap-3 border-b border-gray-200 pb-3">
                                <h2 class="text-lg font-bold text-[#151B26]">Riwayat Penukaran</h2>
                                <ArrowUpRight class="h-4 w-4 text-gray-400" />
                            </div>
                            <div v-if="redemptionHistory.length" class="divide-y divide-gray-100">
                                <div v-for="item in redemptionHistory" :key="item.id" class="flex items-start justify-between gap-3 py-4 last:pb-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#202938]">{{ item.reward_name }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ item.date }}</p>
                                        <p class="mt-1 text-xs text-gray-600">{{ item.points_used }} poin</p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold ring-1" :class="redemptionStatusClass(item.status)">
                                        {{ redemptionStatus(item.status) }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="mt-4 rounded-xl bg-[#F7F9FA] px-4 py-7 text-center">
                                <Gift class="mx-auto h-7 w-7 text-gray-300" />
                                <p class="mt-2 text-xs text-gray-500">Belum ada riwayat penukaran reward.</p>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>