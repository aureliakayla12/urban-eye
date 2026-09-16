<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    status: {
        type: String,
        default: null,
    },
})

const form = useForm({
    email: '',
})

function submit() {
    form.post(route('password.email'))
}
</script>

<template>
    <Head title="Lupa Password - UrbanEye" />

    <div class="min-h-screen bg-white text-black">
        <!-- Navbar -->
        <header class="h-[72px] border-b border-gray-100 bg-white shadow-sm">
            <div class="mx-auto flex h-full max-w-7xl items-center justify-between px-8">
                <Link href="/">
                    <img
                        src="/images/urbaneye-logo.png"
                        alt="UrbanEye"
                        class="h-14 w-auto"
                    />
                </Link>

                <div class="flex items-center gap-3">
                    <span class="hidden text-sm text-gray-500 sm:inline">
                        Sudah punya akun?
                    </span>

                    <Link
                        href="/login"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-[#1B5E20] px-5 text-sm font-semibold text-white transition hover:bg-[#1B5E20]"
                    >
                        Login
                    </Link>

                    <Link
                        href="/register"
                        class="hidden h-10 items-center justify-center rounded-xl border border-[#1B5E20] bg-white px-5 text-sm font-semibold text-[#1B5E20] transition hover:bg-[#1B5E20] hover:text-white sm:inline-flex"
                    >
                        Daftar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="h-[calc(100vh-72px)] overflow-hidden">
            <div class="grid h-full lg:grid-cols-2">
                <!-- Left -->
                <section
                    class="relative hidden overflow-hidden bg-[#E8F7EC] lg:block"
                >
                <div class="relative z-10 flex h-full items-center px-10 xl:px-16">
                    <div class="max-w-[520px] pb-24">
                        <p
                            class="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-[#1B5E20]"
                        >
                            UrbanEye
                        </p>

                        <h1
                            class="max-w-[500px] text-4xl font-bold leading-tight text-[#17351B] xl:text-5xl"
                        >
                            Tetap Terhubung,
                        <br />
                            Jaga Kota Bersama
                        </h1>

                        <p
                            class="mt-5 max-w-[460px] text-sm leading-6 text-gray-600 xl:text-base"
                        >
                            Atur kembali akses akun UrbanEye dengan mudah,
                            lalu lanjutkan kontribusimu untuk menciptakan
                            lingkungan kota yang lebih bersih dan hijau.
                        </p>

                    <div class="mt-7 space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-[#1B5E20] shadow-sm"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 15v2" />
                                <path d="M18 8V6a6 6 0 0 0-12 0v2" />
                                <rect
                                    width="16"
                                    height="12"
                                    x="4"
                                    y="8"
                                    rx="2"
                                />
                            </svg>
                        </div>

                            <span class="text-sm text-gray-600">
                                Proses reset password yang sederhana
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-[#1B5E20] shadow-sm"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M20 7h-9" />
                                <path d="M20 17h-9" />
                                <path d="M7 7v10" />
                                <path d="m4 4 3 3-3 3" />
                                <path d="m4 14 3 3-3 3" />
                            </svg>
                        </div>

                            <span class="text-sm text-gray-600">
                                Akses kembali akun UrbanEye dengan mudah
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-[#1B5E20] shadow-sm"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M12 2 4 5v6c0 5 3.4 9.6 8 11 4.6-1.4 8-6 8-11V5l-8-3Z"
                                />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>

                            <span class="text-sm text-gray-600">
                                Data akun tetap terlindungi
                            </span>
                        </div>
                    </div>
                </div>
            </div>

                <!-- City Illustration -->
                <div
                    class="pointer-events-none absolute bottom-0 right-0 z-0 w-[280px] xl:w-[320px]"
                >
                    <img
                        src="/images/urbaneye-city.png"
                        alt="UrbanEye city illustration"
                        class="block h-auto w-full"
                    />
                </div>
                </section>

                <!-- Right -->
                <section
                    class="flex items-center justify-center bg-white px-6 py-8 sm:px-8 lg:px-10"
                >
                    <div
                        class="w-full max-w-[430px] rounded-[22px] bg-white px-7 py-7 shadow-[0_8px_30px_rgba(0,0,0,0.07)] sm:px-8"
                    >
                        <div class="text-center">
                            <div
                                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#E8F7EC] text-[#1B5E20]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M15 7a6 6 0 1 0-8.5 8.5L8 17l3-3"
                                    />
                                    <path
                                        d="m14 10 7-7"
                                    />
                                    <path
                                        d="m18 3 3 3"
                                    />
                                </svg>
                            </div>

                            <h2 class="text-2xl font-bold text-black">
                                Lupa Password?
                            </h2>

                            <p class="mt-2 text-sm leading-5 text-gray-500">
                                Masukkan email yang terdaftar dan kami akan
                                mengirimkan link untuk mengatur ulang password
                                Anda.
                            </p>
                        </div>

                        <!-- Success -->
                        <div
                            v-if="status"
                            class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-[#1B5E20]"
                        >
                            {{ status }}
                        </div>

                        <form
                            class="mt-6 space-y-4"
                            @submit.prevent="submit"
                        >
                            <!-- Email -->
                            <div>
                                <label class="mb-2 block text-xs font-semibold text-black">
                                    Email
                                </label>

                                <div class="relative">
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="nama@gmail.com"
                                        autocomplete="email"
                                        required
                                        autofocus
                                        class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 pr-10 text-sm text-black outline-none transition placeholder:text-gray-400 focus:border-[#1B5E20] focus:ring-1 focus:ring-[#1B5E20]"
                                    />

                                    <button
                                        v-if="form.email"
                                        type="button"
                                        aria-label="Hapus email"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-lg leading-none text-gray-400 transition hover:text-[#1B5E20]"
                                        @click="form.email = ''"
                                    >
                                        ×
                                    </button>
                                </div>

                                <p
                                    v-if="form.errors.email"
                                    class="mt-1.5 text-xs text-red-500"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="h-11 w-full rounded-xl bg-[#1B5E20] text-sm font-semibold text-white transition hover:bg-[#1B5E20] disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                {{
                                    form.processing
                                        ? 'Mengirim...'
                                        : 'Kirim Link Reset Password'
                                }}
                            </button>
                        </form>

                        <!-- Back -->
                        <div class="mt-5 text-center">
                            <Link
                                href="/login"
                                class="text-sm font-medium text-[#1B5E20] transition hover:underline"
                            >
                                ← Kembali ke Login
                            </Link>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>