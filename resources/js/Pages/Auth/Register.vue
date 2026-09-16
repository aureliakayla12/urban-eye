<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Eye, EyeOff } from 'lucide-vue-next'

defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: null,
    },
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)
const agreeTerms = ref(false)

const passwordMismatch = computed(() => {
    return (
        form.password_confirmation.length > 0 &&
        form.password !== form.password_confirmation
    )
})

const passwordMatch = computed(() => {
    return (
        form.password_confirmation.length > 0 &&
        form.password === form.password_confirmation
    )
})

function submit() {
    if (!agreeTerms.value || passwordMismatch.value) {
        return
    }

    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
    })
}
</script>

<template>
    <Head title="Daftar - UrbanEye" />

    <div class="min-h-screen bg-white text-black">
        <!-- NAVBAR -->
        <header class="h-[72px] border-b border-gray-100 bg-white shadow-sm">
            <div
                class="mx-auto flex h-full max-w-6xl items-center justify-between px-6 lg:px-8"
            >
                <!-- Logo -->
                <Link
                    href="/"
                    class="shrink-0"
                >
                    <img
                        src="/images/urbaneye-logo.png"
                        alt="UrbanEye"
                        class="h-14 w-auto"
                    />
                </Link>

                <!-- Navigation -->
                <nav class="hidden items-center gap-8 lg:flex">
                    <Link
                        href="/"
                        class="border-b-2 border-[#1B5E20] px-1 py-5 text-sm font-medium text-[#1B5E20]"
                    >
                        Beranda
                    </Link>

                    <a
                        href="/#fitur"
                        class="border-b-2 border-transparent px-1 py-5 text-sm font-medium text-black transition hover:text-[#1B5E20]"
                    >
                        Fitur
                    </a>

                    <a
                        href="/#cara-kerja"
                        class="border-b-2 border-transparent px-1 py-5 text-sm font-medium text-black transition hover:text-[#1B5E20]"
                    >
                        Cara Kerja
                    </a>

                    <a
                        href="/#tentang"
                        class="border-b-2 border-transparent px-1 py-5 text-sm font-medium text-black transition hover:text-[#1B5E20]"
                    >
                        Tentang
                    </a>

                    <a
                        href="/#kontak"
                        class="border-b-2 border-transparent px-1 py-5 text-sm font-medium text-black transition hover:text-[#1B5E20]"
                    >
                        Kontak
                    </a>
                </nav>

                <!-- Auth -->
                <div class="flex items-center gap-3">
                    <Link
                        href="/login"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-[#1B5E20] bg-white px-5 text-sm font-semibold text-[#1B5E20] transition hover:-translate-y-0.5 hover:bg-[#1B5E20] hover:text-white"
                    >
                        Login
                    </Link>

                    <Link
                        href="/register"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-[#1B5E20] px-5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#164A19] focus:outline-none focus:ring-2 focus:ring-[#4CAF50] focus:ring-offset-2"
                    >
                        Daftar
                    </Link>
                </div>
            </div>
        </header>

        <!-- MAIN -->
        <main class="h-[calc(100vh-72px)] overflow-hidden">
            <div class="grid h-full lg:grid-cols-[55%_45%]">
                <!-- LEFT -->
                <section
                    class="relative hidden h-full overflow-hidden bg-[#E8F7EC] lg:block"
                >
                    <div class="relative z-10 px-10 pt-10 xl:px-12">
                        <h1
                            class="max-w-[540px] text-[40px] font-bold leading-[1.12] tracking-[-1px] text-[#1B5E20] xl:text-[44px]"
                        >
                            Bersama
                            <br />
                            Wujudkan Kota
                            <br />
                            Lebih Bersih Dan Hijau
                        </h1>

                        <p
                            class="mt-4 max-w-[430px] text-[14px] leading-6 text-[#718096]"
                        >
                            Laporkan permasalahan lingkungan di sekitar Anda
                            dan pantau penanganannya secara transparan.
                        </p>

                        <!-- FEATURES -->
                        <div class="mt-8 space-y-5">
                            <!-- Feature 1 -->
                            <div class="flex max-w-[450px] items-start gap-4">
                                <div
                                    class="mt-0.5 shrink-0 text-[#1B5E20]"
                                >
                                    <svg
                                        class="h-7 w-7"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >
                                        <path
                                            d="M12 3 4 6v5c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V6l-8-3Z"
                                        />
                                        <path d="M12 8v4" />
                                        <circle
                                            cx="12"
                                            cy="15.5"
                                            r=".7"
                                            fill="currentColor"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="text-[15px] font-bold text-black">
                                        Laporkan dengan mudah
                                    </h3>

                                    <p
                                        class="mt-1 text-[12px] leading-5 text-[#718096]"
                                    >
                                        Sampaikan keluhan disertai foto dan lokasi
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="flex max-w-[450px] items-start gap-4">
                                <div
                                    class="mt-0.5 shrink-0 text-[#1B5E20]"
                                >
                                    <svg
                                        class="h-7 w-7"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >
                                        <path
                                            d="M12 21s7-4.4 7-10a7 7 0 1 0-14 0c0 5.6 7 10 7 10Z"
                                        />
                                        <circle
                                            cx="12"
                                            cy="11"
                                            r="2.5"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="text-[15px] font-bold text-black">
                                        Pantau perkembangan laporan
                                    </h3>

                                    <p
                                        class="mt-1 text-[12px] leading-5 text-[#718096]"
                                    >
                                        Lihat status dan tindak lanjut laporan Anda
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="flex max-w-[450px] items-start gap-4">
                                <div
                                    class="mt-0.5 shrink-0 text-[#1B5E20]"
                                >
                                    <svg
                                        class="h-7 w-7"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >
                                        <path d="M3 10.5 12 3l9 7.5" />
                                        <path d="M5 9.5V21h14V9.5" />
                                        <path d="M9 21v-6h6v6" />
                                        <path
                                            d="M16.5 4.5v-3M19 5l2-2M18.5 7.5h3"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="text-[15px] font-bold text-black">
                                        Kota lebih bersih
                                    </h3>

                                    <p
                                        class="mt-1 text-[12px] leading-5 text-[#718096]"
                                    >
                                        Bersama kita ciptakan lingkungan yang lebih baik
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CITY ILLUSTRATION -->
                    <img
                        src="/images/urbaneye-city.png"
                        alt="UrbanEye city illustration"
                        class="pointer-events-none absolute bottom-0 right-0 z-0 w-[270px] xl:w-[310px]"
                    />
                </section>

                <!-- RIGHT -->
                <section
                    class="h-full overflow-y-auto bg-white px-6 py-8 sm:px-8 lg:px-10"
                >
                    <div class="flex min-h-full items-start justify-center">
                        <div
                            class="my-4 w-full max-w-[450px] rounded-[22px] bg-white px-7 py-6 shadow-[0_8px_30px_rgba(0,0,0,0.07)] sm:px-8"
                        >
                            <!-- Heading -->
                            <div class="text-center">
                                <h2 class="text-2xl font-bold text-black">
                                    Daftar Akun Baru
                                </h2>

                                <p
                                    class="mx-auto mt-1 max-w-[320px] text-sm leading-5 text-gray-500"
                                >
                                    Bergabunglah dan berkontribusi untuk
                                    lingkungan yang lebih baik.
                                </p>
                            </div>

                            <!-- Form -->
                            <form
                                class="mt-5 space-y-2.5"
                                @submit.prevent="submit"
                            >
                                <!-- Nama -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold text-black"
                                    >
                                        Nama Lengkap
                                    </label>

                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Masukkan nama lengkap"
                                        autocomplete="name"
                                        required
                                        autofocus
                                        class="h-10 w-full rounded-xl border border-gray-300 bg-white px-4 text-xs text-black outline-none transition placeholder:text-gray-400 focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
                                    />

                                    <p
                                        v-if="form.errors.name"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold text-black"
                                    >
                                        Email
                                    </label>

                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="nama@email.com"
                                        autocomplete="username"
                                        required
                                        class="h-10 w-full rounded-xl border border-gray-300 bg-white px-4 text-xs text-black outline-none transition placeholder:text-gray-400 focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
                                    />

                                    <p
                                        v-if="form.errors.email"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <!-- No. Telepon -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold text-black"
                                    >
                                        No. Telepon
                                    </label>

                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="08xxxxxxxxxx"
                                        autocomplete="tel"
                                        class="h-10 w-full rounded-xl border border-gray-300 bg-white px-4 text-xs text-black outline-none transition placeholder:text-gray-400 focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
                                    />
                                </div>

                                <!-- Password -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold text-black"
                                    >
                                        Password
                                    </label>

                                    <div class="relative">
                                        <input
                                            v-model="form.password"
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            placeholder="Buat password"
                                            autocomplete="new-password"
                                            required
                                            class="h-10 w-full rounded-xl border border-gray-300 bg-white px-4 pr-10 text-xs text-black outline-none transition placeholder:text-gray-400 focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
                                        />

                                        <button
                                            type="button"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-[#1B5E20]"
                                            @click="
                                                showPassword = !showPassword
                                            "
                                        >
                                            <EyeOff
                                                v-if="showPassword"
                                                :size="17"
                                            />

                                            <Eye
                                                v-else
                                                :size="17"
                                            />
                                        </button>
                                    </div>

                                    <p
                                        v-if="form.errors.password"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.password }}
                                    </p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold text-black"
                                    >
                                        Konfirmasi Password
                                    </label>

                                    <div class="relative">
                                        <input
                                            v-model="form.password_confirmation"
                                            :type="
                                                showPasswordConfirmation
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            placeholder="Ulangi password"
                                            autocomplete="new-password"
                                            required
                                            class="h-10 w-full rounded-xl border bg-white px-4 pr-10 text-xs text-black outline-none transition placeholder:text-gray-400"
                                            :class="
                                                passwordMismatch
                                                    ? 'border-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500'
                                                    : passwordMatch
                                                      ? 'border-green-500 focus:border-green-600 focus:ring-1 focus:ring-green-500'
                                                      : 'border-gray-300 focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]'
                                            "
                                        />

                                        <button
                                            type="button"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-[#1B5E20]"
                                            @click="
                                                showPasswordConfirmation =
                                                    !showPasswordConfirmation
                                            "
                                        >
                                            <EyeOff
                                                v-if="
                                                    showPasswordConfirmation
                                                "
                                                :size="17"
                                            />

                                            <Eye
                                                v-else
                                                :size="17"
                                            />
                                        </button>
                                    </div>

                                    <!-- Password mismatch -->
                                    <p
                                        v-if="passwordMismatch"
                                        class="mt-1.5 text-xs text-red-500"
                                    >
                                        Password tidak cocok.
                                    </p>

                                    <!-- Password match -->
                                    <p
                                        v-else-if="passwordMatch"
                                        class="mt-1.5 text-xs text-[#1B5E20]"
                                    >
                                        Password cocok.
                                    </p>

                                    <p
                                        v-if="form.errors.password_confirmation"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.password_confirmation }}
                                    </p>
                                </div>

                                <!-- Terms -->
                                <div class="pt-1">
                                    <label
                                        class="flex cursor-pointer items-start gap-2"
                                    >
                                        <input
                                            v-model="agreeTerms"
                                            type="checkbox"
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#1B5E20] focus:ring-[#1B5E20]"
                                        />

                                        <span
                                            class="text-xs leading-5 text-gray-600"
                                        >
                                            Saya setuju dengan
                                            <a
                                                href="#"
                                                class="font-semibold text-[#1B5E20] hover:underline"
                                            >
                                                Syarat & Ketentuan
                                            </a>
                                        </span>
                                    </label>
                                </div>

                                <!-- Register -->
                                <button
                                    type="submit"
                                    :disabled="
                                        form.processing ||
                                        !agreeTerms ||
                                        passwordMismatch
                                    "
                                    class="mt-2 h-11 w-full rounded-xl bg-[#1B5E20] text-sm font-semibold text-white transition hover:bg-[#1B5E20] disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    {{
                                        form.processing
                                            ? 'Mendaftarkan...'
                                            : 'Daftar'
                                    }}
                                </button>
                            </form>

                            <!-- Login -->
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-500">
                                    Sudah punya akun?
                                    <Link
                                        href="/login"
                                        class="font-semibold text-[#1B5E20] hover:underline"
                                    >
                                        Login di sini
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>