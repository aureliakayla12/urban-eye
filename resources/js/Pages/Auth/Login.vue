<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Eye, EyeOff } from 'lucide-vue-next'

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false,
    },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Login - UrbanEye" />

    <div class="h-screen overflow-hidden bg-white text-black">

       
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
                        class="inline-flex rounded-xl bg-[#1B5E20] px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#164A19] focus:outline-none focus:ring-2 focus:ring-[#4CAF50] focus:ring-offset-2"
                    >
                        Login
                    </Link>

                    <Link
                        href="/register"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-[#1B5E20] bg-white px-5 text-sm font-semibold text-[#1B5E20] transition hover:-translate-y-0.5 transition hover:bg-[#1B5E20] hover:text-white"
                    >
                        Daftar
                    </Link>
                </div>
            </div>
        </header>


       
        <!-- LOGIN AREA -->
       
        <main class="h-[calc(100vh-72px)] overflow-hidden">
            <div class="grid h-full lg:grid-cols-2">

                
                <!-- LEFT -->
           
                <section
                    class="relative hidden overflow-hidden bg-[#E8F7EC] lg:block"
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

                                    <p class="mt-1 text-[12px] leading-5 text-[#718096]">
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

                                    <p class="mt-1 text-[12px] leading-5 text-[#718096]">
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
                                        <path d="M16.5 4.5v-3M19 5l2-2M18.5 7.5h3" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="text-[15px] font-bold text-black">
                                        Kota lebih bersih
                                    </h3>

                                    <p class="mt-1 text-[12px] leading-5 text-[#718096]">
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
                    class="flex h-full items-center justify-center bg-white px-6 py-6 sm:px-8 lg:px-10"
                >
                    <div
                        class="w-full max-w-[390px] rounded-[20px] bg-white px-7 py-6 shadow-[0_8px_30px_rgba(0,0,0,0.07)]"
                    >

                        <!-- Heading -->
                        <div class="text-center">
                            <h2 class="text-[23px] font-bold text-black">
                                Masuk ke UrbanEye
                            </h2>

                            <p class="mt-1 text-[12px] text-gray-500">
                                Selamat datang kembali! Silakan masuk ke akun Anda
                            </p>
                        </div>


                        <!-- Form -->
                        <form
                            class="mt-5 space-y-3"
                            @submit.prevent="submit"
                        >

                            <!-- EMAIL -->
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold text-black"
                                >
                                    Email
                                </label>

                            <div class="relative">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="username"
                                    placeholder="nama@gmail.com"
                                    class="input input-bordered h-10 min-h-0 w-full bg-white pr-10 text-xs text-black placeholder:text-gray-400 focus:border-[#1B5E20] focus:outline-none focus:ring-0"
                                />

                                <button
                                    v-if="form.email"
                                    type="button"
                                    aria-label="Hapus email"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-[#1B5E20]"
                                    @click="form.email = ''"
                                >
                                    ×
                                </button>
                            </div>

                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>


                            <!-- PASSWORD -->
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
                                        autocomplete="current-password"
                                        placeholder="Masukkan Password"
                                        class="input input-bordered h-10 min-h-0 w-full bg-white pr-10 text-xs text-black placeholder:text-gray-400 focus:border-[#1B5E20] focus:outline-none focus:ring-0"
                                    />

                                    <button
                                        type="button"
                                        aria-label="Tampilkan password"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-[#1B5E20]"
                                        @click="
                                            showPassword = !showPassword
                                        "
                                    >
                                        <Eye
                                            v-if="!showPassword"
                                            class="h-4 w-4"
                                        />

                                        <EyeOff
                                            v-else
                                            class="h-4 w-4"
                                        />
                                    </button>
                                </div>

                                <p
                                    v-if="form.errors.password"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>


                            <!-- REMEMBER -->
                            <div
                                class="flex items-center justify-between pt-0.5"
                            >
                                <label
                                    class="flex cursor-pointer items-center gap-2"
                                >
                                    <input
                                        v-model="form.remember"
                                        type="checkbox"
                                        class="checkbox checkbox-success h-4 w-4"
                                    />

                                    <span class="text-xs text-gray-600">
                                        Ingat saya
                                    </span>
                                </label>

                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-medium text-[#1B5E20] hover:underline"
                                >
                                    Lupa password?
                                </Link>
                            </div>


                            <!-- LOGIN BUTTON -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="h-10 w-full rounded-xl bg-[#1B5E20] text-sm font-semibold text-white transition hover:bg-[#1B5E20] disabled:cursor-not-allowed disabled:opacity-70"
                                
                            >
                                {{
                                    form.processing
                                        ? 'Memproses...'
                                        : 'Login'
                                }}
                            </button>


                            <!-- DIVIDER -->
                            <div
                                class="divider my-0 text-[10px] text-gray-400"
                            >
                                atau masuk dengan
                            </div>


                            <!-- GOOGLE -->
                            <a
                                :href="route('google.redirect')"
                                class="flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                <path
                                    fill="#4285F4"
                                    d="M21.805 10.023h-9.804v3.954h5.62c-.242 1.272-.967 2.353-2.062 3.079v2.565h3.336c1.953-1.798 2.91-4.447 2.91-7.603 0-.659-.059-1.299-.17-1.995z"
                                />
                                <path
                                    fill="#34A853"
                                    d="M12.001 22c2.79 0 5.133-.925 6.844-2.507l-3.336-2.565c-.925.62-2.104.988-3.508.988-2.698 0-4.983-1.822-5.801-4.27H2.751v2.648A10.337 10.337 0 0 0 12.001 22z"
                                />
                                <path
                                    fill="#FBBC05"
                                    d="M6.2 13.646a6.22 6.22 0 0 1 0-3.292V7.706H2.751a10.003 10.003 0 0 0 0 8.588L6.2 13.646z"
                                />
                                <path
                                    fill="#EA4335"
                                    d="M12.001 6.083c1.519 0 2.879.522 3.952 1.549l2.964-2.965C17.129 2.938 14.786 2 12.001 2a10.337 10.337 0 0 0-9.25 5.706L6.2 10.354c.818-2.448 3.103-4.271 5.801-4.271z"
                                />
                                </svg>

                                    Masuk dengan Google
                            </a>

                        </form>


                        <!-- REGISTER -->
                        <p
                            class="mt-4 text-center text-xs text-gray-500"
                        >
                            Belum punya akun?

                            <Link
                                href="/register"
                                class="font-semibold text-[#1B5E20] hover:underline"
                            >
                                Daftar sekarang
                            </Link>
                        </p>

                    </div>
                </section>

            </div>
        </main>

    </div>
</template>