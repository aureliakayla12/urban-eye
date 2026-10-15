
<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue'

import {
    UserRound,
    ShieldCheck,
    LockKeyhole,
    TriangleAlert,
    ChevronDown,
} from 'lucide-vue-next'

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
})

const page = usePage()

const user = computed(() => page.props.auth?.user ?? {})

const initials = computed(() => {
    return (user.value.name ?? 'UrbanEye')
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word.charAt(0))
        .join('')
        .toUpperCase()
})
</script>

<template>
    <Head title="Pengaturan Akun" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Pengaturan Akun
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola informasi dan keamanan akun UrbanEye kamu.
                </p>
            </div>
        </template>

        <div class="mx-auto max-w-6xl space-y-7">

            <!-- Account Overview -->
            <section
                class="relative overflow-hidden rounded-2xl bg-[#0B1F2A] p-6 text-white shadow-sm sm:p-8"
            >
                <div
                    class="pointer-events-none absolute -right-10 -top-20 h-64 w-64 rounded-full border-[35px] border-white/[0.04]"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-24 right-40 h-48 w-48 rounded-full bg-emerald-500/[0.08]"
                ></div>

                <div
                    class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-center"
                >
                    <div class="max-w-xl">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold text-emerald-300"
                        >
                            <ShieldCheck :size="15" />
                            PENGATURAN MASYARAKAT
                        </div>

                        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                            Akunmu, kendalimu.
                        </h1>

                        <p class="mt-2 text-sm leading-6 text-slate-300 sm:text-base">
                            Pastikan informasi akun tetap benar dan lindungi
                            keamanan akunmu saat menggunakan UrbanEye.
                        </p>
                    </div>

                    <div
                        class="flex min-w-0 items-center gap-4 rounded-xl border border-white/10 bg-white/[0.06] p-4 sm:min-w-[270px]"
                    >
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-lg font-bold text-white"
                        >
                            {{ initials }}
                        </div>

                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white">
                                {{ user.name }}
                            </p>
                            <p class="mt-1 truncate text-sm text-slate-300">
                                {{ user.email }}
                            </p>
                            <span
                                class="mt-2 inline-flex rounded-full bg-white/10 px-2.5 py-1 text-xs text-emerald-200"
                            >
                                Masyarakat
                            </span>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Profile Information -->
            <section
                id="informasi-akun"
                class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-start gap-4 border-b border-slate-100 px-5 py-5 sm:px-7"
                >
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"
                    >
                        <UserRound :size="21" />
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800">
                            Informasi Profil
                        </h3>
                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            Atur informasi pribadi yang terhubung dengan akunmu.
                        </p>
                    </div>
                </div>

                <div class="p-5 sm:p-7">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-2xl"
                    />
                </div>
            </section>

            <!-- Password & Security -->
            <section
                id="keamanan-akun"
                class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-start gap-4 border-b border-slate-100 px-5 py-5 sm:px-7"
                >
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"
                    >
                        <LockKeyhole :size="21" />
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800">
                            Keamanan Akun
                        </h3>
                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            Gunakan password yang kuat dan perbarui secara berkala.
                        </p>
                    </div>
                </div>

                <div class="p-5 sm:p-7">
                    <UpdatePasswordForm class="max-w-2xl" />
                </div>
            </section>

            <!-- Delete Account -->
            <section
                id="hapus-akun"
                class="scroll-mt-28 overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-start gap-4 border-b border-red-100 bg-red-50/70 px-5 py-5 sm:px-7"
                >
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600"
                    >
                        <TriangleAlert :size="21" />
                    </div>

                    <div>
                        <h3 class="font-bold text-red-800">
                            Penghapusan Akun
                        </h3>
                        <p class="mt-1 text-sm leading-5 text-red-700/80">
                            Bagian ini berisi opsi untuk menghapus akun. Pastikan
                            kamu memahami konsekuensinya sebelum melanjutkan.
                        </p>
                    </div>
                </div>

                <div class="p-5 sm:p-7">
                    <DeleteUserForm class="max-w-2xl" />
                </div>
            </section>

            <p class="pb-2 text-center text-xs text-slate-400">
                UrbanEye · Kelola akunmu dengan aman
            </p>
        </div>
    </AuthenticatedLayout>
</template>
