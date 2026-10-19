<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import {
    UserRound,
    ShieldCheck,
    Save,
    LockKeyhole,
    CheckCircle2,
} from 'lucide-vue-next'

const props = defineProps({
    user: {
        type: Object,
        default: () => ({
            name: '',
            email: '',
        }),
    },
})

const profileForm = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
    })
}

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    })
}
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

            <section class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-200 p-5">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100"
                    >
                        <UserRound class="h-6 w-6 text-green-600" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-800">
                            Informasi Profil
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Perbarui informasi akun Anda.
                        </p>
                    </div>
                </div>

                <form
                    class="space-y-5 p-5"
                    @submit.prevent="updateProfile"
                >
                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Nama Lengkap
                        </label>

                        <input
                            id="name"
                            v-model="profileForm.name"
                            type="text"
                            required
                            autocomplete="name"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                        />

                        <p
                            v-if="profileForm.errors.name"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ profileForm.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Email
                        </label>

                        <input
                            id="email"
                            v-model="profileForm.email"
                            type="email"
                            required
                            autocomplete="email"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                        />

                        <p
                            v-if="profileForm.errors.email"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ profileForm.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Role
                        </label>

                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600"
                        >
                            Petugas
                        </div>
                    </div>

                    <p
                        v-if="profileForm.recentlySuccessful"
                        class="flex items-center gap-2 text-sm text-green-600"
                    >
                        <CheckCircle2 class="h-4 w-4" />
                        Profil berhasil diperbarui.
                    </p>

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Save class="h-4 w-4" />

                            {{
                                profileForm.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Profil'
                            }}
                        </button>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-200 p-5">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100"
                    >
                        <ShieldCheck class="h-6 w-6 text-blue-600" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-800">
                            Keamanan Akun
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Ubah kata sandi untuk menjaga keamanan akun.
                        </p>
                    </div>
                </div>

                <form
                    class="space-y-5 p-5"
                    @submit.prevent="updatePassword"
                >
                    <div>
                        <label
                            for="current_password"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Kata Sandi Saat Ini
                        </label>

                        <input
                            id="current_password"
                            v-model="passwordForm.current_password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                        />

                        <p
                            v-if="passwordForm.errors.current_password"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Kata Sandi Baru
                        </label>

                        <input
                            id="password"
                            v-model="passwordForm.password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                        />

                        <p
                            v-if="passwordForm.errors.password"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="password_confirmation"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Konfirmasi Kata Sandi Baru
                        </label>

                        <input
                            id="password_confirmation"
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                        />
                    </div>

                    <p
                        v-if="passwordForm.recentlySuccessful"
                        class="flex items-center gap-2 text-sm text-green-600"
                    >
                        <CheckCircle2 class="h-4 w-4" />
                        Kata sandi berhasil diperbarui.
                    </p>

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <LockKeyhole class="h-4 w-4" />

                            {{
                                passwordForm.processing
                                    ? 'Menyimpan...'
                                    : 'Ubah Kata Sandi'
                            }}
                        </button>
                    </div>
                </form>
            </section>

        </div>
    </AuthenticatedLayout>
</template>