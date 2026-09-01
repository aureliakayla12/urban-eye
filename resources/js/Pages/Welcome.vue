<template>
    <Head title="UrbanEye" />
    <div class="min-h-screen bg-white text-black">

        <!-- NAVBAR -->
      
        <header
            class="sticky top-0 z-50 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur"
        >
            <div
                class="mx-auto flex h-20 max-w-6xl items-center justify-between px-6 lg:px-8"
            >
                <!-- Logo -->
                <Link
                    href="/"
                    class="shrink-0"
                >
                    <img
                        src="/images/urbaneye-logo.png"
                        alt="UrbanEye"
                        class="h-11 w-auto object-contain"
                    />
                </Link>

                <!-- Menu -->
                <nav class="hidden items-center gap-8 lg:flex">

                    <!-- Beranda -->
                    <button
                        type="button"
                        @click="scrollToSection('beranda')"
                        :class="[
                            'border-b-2 px-1 py-6 text-sm font-medium transition',
                            activeSection === 'beranda'
                                ? 'border-[#1B5E20] text-[#1B5E20]'
                                : 'border-transparent text-black hover:text-[#1B5E20]',
                        ]"
                    >
                        Beranda
                    </button>

                    <!-- Fitur -->
                    <button
                        type="button"
                        @click="scrollToSection('fitur')"
                        :class="[
                            'border-b-2 px-1 py-6 text-sm font-medium transition',
                            activeSection === 'fitur'
                                ? 'border-[#1B5E20] text-[#1B5E20]'
                                : 'border-transparent text-black hover:text-[#1B5E20]',
                        ]"
                    >
                        Fitur
                    </button>

                    <!-- Cara Kerja -->
                    <button
                        type="button"
                        @click="scrollToSection('cara-kerja')"
                        :class="[
                            'border-b-2 px-1 py-6 text-sm font-medium transition',
                            activeSection === 'cara-kerja'
                                ? 'border-[#1B5E20] text-[#1B5E20]'
                                : 'border-transparent text-black hover:text-[#1B5E20]',
                        ]"
                    >
                        Cara Kerja
                    </button>

                    <!-- Tentang -->
                    <button
                        type="button"
                        @click="scrollToSection('tentang')"
                        :class="[
                            'border-b-2 px-1 py-6 text-sm font-medium transition',
                            activeSection === 'tentang'
                                ? 'border-[#1B5E20] text-[#1B5E20]'
                                : 'border-transparent text-black hover:text-[#1B5E20]',
                        ]"
                    >
                        Tentang
                    </button>

                    <!-- Kontak -->
                    <button
                        type="button"
                        @click="scrollToSection('kontak')"
                        :class="[
                            'border-b-2 px-1 py-6 text-sm font-medium transition',
                            activeSection === 'kontak'
                                ? 'border-[#1B5E20] text-[#1B5E20]'
                                : 'border-transparent text-black hover:text-[#1B5E20]',
                        ]"
                    >
                        Kontak
                    </button>
                </nav>

                <!-- Auth -->
                <div
                    v-if="canLogin"
                    class="flex items-center gap-3"
                >
                    <Link
                        :href="route('login')"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-[#1B5E20] bg-white px-5 text-sm font-semibold text-[#1B5E20] transition hover:bg-[#1B5E20] transition hover:-translate-y-0.5 hover:text-white"
                    >
                        Login
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="inline-flex rounded-xl bg-[#1B5E20] px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#164A19] focus:outline-none focus:ring-2 focus:ring-[#4CAF50] focus:ring-offset-2"
                    >
                        Daftar
                    </Link>
                </div>

            </div>
        </header>

        
        <!-- HERO -->
       
        <section
            id="beranda"
            class="scroll-mt-20 bg-white"
        >
            <div
                class="mx-auto grid max-w-7xl items-center gap-10 px-6 py-12 lg:grid-cols-[0.9fr_1.1fr] lg:px-20 lg:py-14"
            >

                <!-- Hero text -->
                <div>
                    <div
                        class="mb-5 inline-flex rounded-full bg-[#E8F5E9] px-4 py-2 text-xs font-medium text-[#1B5E20]"
                    >
                        Smart Environment Platform
                    </div>

                    <h1
                        class="max-w-xl text-4xl font-bold leading-[1.1] tracking-tight text-black sm:text-5xl lg:text-[52px]"
                    >
                        Bangun Kota
                        <span class="block text-[#1B5E20]">
                            Lebih Bersih
                        </span>
                        Bersama
                        <span class="text-[#1B5E20]">
                            UrbanEye
                        </span>
                    </h1>

                    <p
                        class="mt-5 max-w-lg text-sm leading-6 text-[#757575] lg:text-base"
                    >
                        UrbanEye membantu masyarakat melaporkan
                        permasalahan lingkungan secara cepat,
                        transparan, dan berbasis lokasi.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <Link
                            :href="canLogin ? route('login') : '#fitur'"
                            class="btn bg-[#1B5E20] px-6 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#164A19]"
                        >
                            Laporkan Sekarang
                        </Link>

                        <button
                            type="button"
                            class="btn btn-outline border-[#1B5E20] bg-white px-6 text-[#1B5E20] transition hover:-translate-y-0.5 hover:border-[#1B5E20] hover:bg-[#1B5E20] hover:text-white"
                            
                            @click="scrollToSection('fitur')"
                        >
                            Pelajari Lebih Lanjut
                        </button>
                    </div>
                </div>

          
                <!-- URBANEYE WEB PREVIEW -->

                <div class="flex justify-center lg:justify-end">
                    <div class="w-full max-w-[570px]">

                        <!-- Browser Window -->
                        <div
                            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-[0_18px_40px_rgba(0,0,0,0.12)]"
                        >

                            <!-- Browser bar -->
                            <div
                                class="flex h-9 items-center justify-between border-b border-gray-200 bg-[#F7F7F7] px-4"
                            >
                                <div class="flex gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-[#FF5F57]"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-[#FEBC2E]"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-[#28C840]"></span>
                                </div>

                                <div
                                    class="flex h-5 w-[200px] items-center justify-center rounded-md bg-white text-[7px] text-gray-400 shadow-sm"
                                >
                                    urbaneye
                                </div>

                                <span class="text-[9px] text-gray-400">
                                    ⋯
                                </span>
                            </div>

                            <!-- Web App -->
                            <div class="flex min-h-[315px]">

                                <!-- Sidebar -->
                                <aside
                                    class="hidden w-[105px] shrink-0 bg-[#0B1F2A] px-2.5 py-3 text-white sm:block"
                                >
                                    <!-- Logo -->
                                    <div
                                        class="flex items-center gap-2 border-b border-white/10 pb-3"
                                    >
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-md bg-[#1B5E20] text-[7px] font-bold"
                                        >
                                            UE
                                        </div>

                                        <div>
                                            <p class="text-[7px] font-bold">
                                                UrbanEye
                                            </p>

                                            <p class="text-[4px] text-white/50">
                                                Smart Environment
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Menu -->
                                    <div class="mt-3 space-y-1">

                                        <div
                                            class="flex items-center gap-2 rounded-md bg-[#1B5E20] px-2 py-1.5 text-[6px]"
                                        >
                                            <span>⌂</span>
                                            Dashboard
                                        </div>

                                        <div
                                            class="flex items-center gap-2 px-2 py-1.5 text-[6px] text-white/70"
                                        >
                                            <span>▤</span>
                                            Buat Laporan
                                        </div>

                                        <div
                                            class="flex items-center gap-2 px-2 py-1.5 text-[6px] text-white/70"
                                        >
                                            <span>◫</span>
                                            Riwayat Laporan
                                        </div>

                                        <div
                                            class="flex items-center gap-2 px-2 py-1.5 text-[6px] text-white/70"
                                        >
                                            <span>♜</span>
                                            Leaderboard
                                        </div>

                                        <div
                                            class="flex items-center gap-2 px-2 py-1.5 text-[6px] text-white/70"
                                        >
                                            <span>🎁</span>
                                            Reward Saya
                                        </div>

                                        <div
                                            class="flex items-center gap-2 px-2 py-1.5 text-[6px] text-white/70"
                                        >
                                            <span>⚙</span>
                                            Pengaturan
                                        </div>

                                    </div>

                                    <!-- User -->
                                    <div
                                        class="mt-8 border-t border-white/10 pt-3"
                                    >
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-5 w-5 rounded-full bg-[#4CAF50]"
                                            ></div>

                                            <div>
                                                <p class="text-[6px] font-semibold">
                                                    Masyarakat
                                                </p>

                                                <p class="text-[4px] text-white/50">
                                                    Pengguna
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </aside>

                                <!-- Dashboard -->
                                <div class="min-w-0 flex-1 bg-[#F7F9F7]">

                                    <!-- Topbar -->
                                    <div
                                        class="flex h-9 items-center justify-between border-b border-gray-200 bg-white px-3"
                                    >
                                        <div>
                                            <p class="text-[5px] text-gray-400">
                                                Dashboard
                                            </p>

                                            <h3 class="text-[8px] font-bold text-black">
                                                Selamat datang di UrbanEye
                                            </h3>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-[8px] text-gray-400">
                                                ♧
                                            </span>

                                            <div
                                                class="h-5 w-5 rounded-full bg-[#D8E8D9]"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Dashboard content -->
                                    <div class="p-3">

                                        <!-- Statistics -->
                                        <div
                                            class="grid grid-cols-4 gap-2"
                                        >
                                            <div
                                                class="rounded-lg border border-gray-100 bg-white p-2 shadow-sm"
                                            >
                                                <p class="text-[5px] text-gray-400">
                                                    Total Laporan
                                                </p>

                                                <p class="mt-1 text-xs font-bold text-black">
                                                    128
                                                </p>

                                                <p class="text-[5px] text-green-600">
                                                    +12%
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-lg border border-gray-100 bg-white p-2 shadow-sm"
                                            >
                                                <p class="text-[5px] text-gray-400">
                                                    Diproses
                                                </p>

                                                <p class="mt-1 text-xs font-bold text-black">
                                                    45
                                                </p>

                                                <span class="badge badge-warning badge-xs mt-1 text-[8px]">
                                                    Aktif
                                                </span>
                                            </div>

                                            <div
                                                class="rounded-lg border border-gray-100 bg-white p-2 shadow-sm"
                                            >
                                                <p class="text-[5px] text-gray-400">
                                                    Selesai
                                                </p>

                                                <p class="mt-1 text-xs font-bold text-black">
                                                    83
                                                </p>

                                                <p class="text-[5px] text-green-600">
                                                    65%
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-lg border border-gray-100 bg-white p-2 shadow-sm"
                                            >
                                                <p class="text-[5px] text-gray-400">
                                                    Poin Saya
                                                </p>

                                                <p class="mt-1 text-xs font-bold text-black">
                                                    120
                                                </p>

                                                <p class="text-[5px] text-[#1B5E20]">
                                                    Pelapor Aktif
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Map + Activity -->
                                        <div
                                            class="mt-2 grid grid-cols-[1.35fr_1fr] gap-2"
                                        >

                                            <!-- Map -->
                                            <div
                                                class="overflow-hidden rounded-lg bg-white shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center justify-between px-2.5 py-1.5"
                                                >
                                                    <h4 class="text-[6px] font-bold text-black">
                                                        Peta Laporan
                                                    </h4>

                                                    <span class="text-[5px] text-[#1B5E20]">
                                                        Lihat Semua
                                                    </span>
                                                </div>

                                                <div
                                                    class="relative h-[100px] overflow-hidden bg-[#E5EAE4]"
                                                >
                                                    <div
                                                        class="absolute left-[20%] top-[-30%] h-[170%] w-2 rotate-[30deg] bg-white/80"
                                                    ></div>

                                                    <div
                                                        class="absolute left-[50%] top-[-25%] h-[170%] w-3 -rotate-[35deg] bg-white/80"
                                                    ></div>

                                                    <div
                                                        class="absolute right-[20%] top-[-30%] h-[170%] w-2 rotate-[45deg] bg-white/80"
                                                    ></div>

                                                    <div
                                                        class="absolute bottom-[25%] left-[-10%] h-2 w-[120%] rotate-[8deg] bg-white/80"
                                                    ></div>

                                                    <div
                                                        class="absolute top-[35%] left-[-10%] h-2 w-[120%] -rotate-[10deg] bg-white/80"
                                                    ></div>

                                                    <span
                                                        class="absolute left-[23%] top-[35%] h-4 w-4 rounded-full border-2 border-white bg-red-500 shadow"
                                                    ></span>

                                                    <span
                                                        class="absolute left-[58%] top-[23%] h-4 w-4 rounded-full border-2 border-white bg-red-500 shadow"
                                                    ></span>

                                                    <span
                                                        class="absolute right-[20%] top-[55%] h-4 w-4 rounded-full border-2 border-white bg-red-500 shadow"
                                                    ></span>

                                                    <span
                                                        class="absolute left-[46%] bottom-[14%] h-4 w-4 rounded-full border-2 border-white bg-[#1B5E20] shadow"
                                                    ></span>
                                                </div>
                                            </div>

                                            <!-- Activity -->
                                            <div
                                                class="rounded-lg bg-white p-2.5 shadow-sm"
                                            >
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-[6px] font-bold text-black">
                                                        Aktivitas
                                                    </h4>

                                                    <span class="text-[5px] text-gray-400">
                                                        Terbaru
                                                    </span>
                                                </div>

                                                <div class="mt-2.5 space-y-2">

                                                    <div class="flex gap-1.5">
                                                        <div
                                                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-[#E8F5E9] text-[6px] text-[#1B5E20]"
                                                        >
                                                            ✓
                                                        </div>

                                                        <div>
                                                            <p class="text-[6px] font-semibold">
                                                                Laporan selesai
                                                            </p>

                                                            <p class="text-[5px] text-gray-400">
                                                                Sampah di Beji
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="flex gap-1.5">
                                                        <div
                                                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-[#FFF4CC] text-[6px] text-yellow-700"
                                                        >
                                                            !
                                                        </div>

                                                        <div>
                                                            <p class="text-[6px] font-semibold">
                                                                Laporan diproses
                                                            </p>

                                                            <p class="text-[5px] text-gray-400">
                                                                Lampu jalan
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="flex gap-1.5">
                                                        <div
                                                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-[#E3F2FD] text-[6px] text-blue-600"
                                                        >
                                                            +
                                                        </div>

                                                        <div>
                                                            <p class="text-[6px] font-semibold">
                                                                Poin bertambah
                                                            </p>

                                                            <p class="text-[5px] text-gray-400">
                                                                +10 poin
                                                            </p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recent Reports -->
                                        <div
                                            class="mt-2 rounded-lg bg-white p-2.5 shadow-sm"
                                        >
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-[6px] font-bold text-black">
                                                    Laporan Terbaru
                                                </h4>

                                                <span class="text-[5px] text-[#1B5E20]">
                                                    Lihat Semua
                                                </span>
                                            </div>

                                            <div
                                                class="mt-2 grid grid-cols-3 gap-2"
                                            >

                                                <div
                                                    class="rounded-md bg-[#F8FAF8] p-1.5"
                                                >
                                                    <div
                                                        class="h-7 rounded bg-gray-200"
                                                    ></div>

                                                    <p
                                                        class="mt-1 text-[5px] font-semibold text-black"
                                                    >
                                                        Sampah menumpuk
                                                    </p>

                                                    <span
                                                        class="badge badge-success badge-xs mt-1 text-[5px] text-white"
                                                    >
                                                        Selesai
                                                    </span>
                                                </div>

                                                <div
                                                    class="rounded-md bg-[#F8FAF8] p-1.5"
                                                >
                                                    <div
                                                        class="h-7 rounded bg-gray-200"
                                                    ></div>

                                                    <p
                                                        class="mt-1 text-[5px] font-semibold text-black"
                                                    >
                                                        Lampu jalan
                                                    </p>

                                                    <span
                                                        class="badge badge-warning badge-xs mt-1 text-[5px]"
                                                    >
                                                        Diproses
                                                    </span>
                                                </div>

                                                <div
                                                    class="rounded-md bg-[#F8FAF8] p-1.5"
                                                >
                                                    <div
                                                        class="h-7 rounded bg-gray-200"
                                                    ></div>

                                                    <p
                                                        class="mt-1 text-[5px] font-semibold text-black"
                                                    >
                                                        Drainase
                                                    </p>

                                                    <span
                                                        class="badge badge-info badge-xs mt-1 text-[5px]"
                                                    >
                                                        Menunggu
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

       
        <!-- FEATURES -->
            <section
                id="fitur"
                class="scroll-mt-20 bg-white py-10"
            >
                <div class="mx-auto max-w-7x1 px-6 lg:px-20">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-black lg:text-4xl">
                            Mengapa Memilih UrbanEye?
                        </h2>

                        <p
                            class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-gray-600 lg:text-base"
                        >
                            Platform pelaporan lingkungan yang cepat, transparan, dan mudah digunakan oleh masyarakat.
                        </p>
                    </div>

                    <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                           class="group rounded-lg bg-white px-9 py-6 text-center shadow-[0_4px_18px_rgba(0,0,0,0.08)] transition duration-300 hover:-translate-y-1 hover:bg-[#E8F5E9] hover:shadow-[0_8px_24px_rgba(0,0,0,0.12)]"
                        >
                            <div
                                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#E8F5E9] text-[#1B5E20] transition duration-300 group-hover:bg-[#1B5E20] group-hover:text-white"
                            >
                                <!-- Location -->
                                <svg
                                    v-if="feature.icon === 'location'"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M12 21s7-4.4 7-10a7 7 0 1 0-14 0c0 5.6 7 10 7 10Z"
                                    />
                                    <circle cx="12" cy="11" r="2.5" />
                                </svg>

                                <!-- Camera -->
                                <svg
                                    v-else-if="feature.icon === 'camera'"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M4 7h3l1.5-2h7L17 7h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1Z"
                                    />
                                    <circle cx="12" cy="13" r="3.5" />
                                </svg>

                                <!-- Tracking -->
                                <svg
                                    v-else-if="feature.icon === 'tracking'"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <circle cx="12" cy="12" r="8.5" />
                                    <path d="M12 7v5l3 2" />
                                    <path d="m16 17 2 2 3-3" />
                                </svg>

                                <!-- Notification -->
                                <svg
                                    v-else
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                                    />
                                    <path d="M10 21h4" />
                                </svg>
                            </div>

                            <h3
                                class="mt-5 text-base font-bold text-black"
                            >
                                {{ feature.title }}
                            </h3>

                            <p
                                class="mt-3 text-sm leading-6 text-gray-600 transition group-hover:text-black"
                            >
                                {{ feature.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- HOW IT WORKS -->
            <section
                id="cara-kerja"
                class="scroll-mt-20 bg-[#F5F5F5] py-10"
            >
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-black lg:text-4xl">
                            Cara Kerja UrbanEye
                        </h2>

                        <p
                            class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-gray-600"
                        >
                            Hanya dalam 4 langkah mudah untuk melaporkan permasalahan lingkungan.
                        </p>
                    </div>

                    <div class="relative mt-14 grid gap-8 md:grid-cols-4">
                        <div
                            class="absolute left-[12%] right-[12%] top-8 hidden h-px bg-gray-300 md:block"
                        ></div>

                        <div
                            v-for="step in steps"
                            :key="step.number"
                            class="group relative text-center"
                        >
                            <div
                                class="relative z-10 mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-[#1B5E20] shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:bg-[#1B5E20] group-hover:text-white group-hover:shadow-md"
                            >
                                <svg
                                    v-if="step.icon === 'report'"
                                    class="h-7 w-7"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M6 3h12v18H6z" />
                                    <path d="M9 7h6M9 11h6M9 15h4" />
                                </svg>

                                <svg
                                    v-else-if="step.icon === 'location'"
                                    class="h-7 w-7"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M12 21s7-4.4 7-10a7 7 0 1 0-14 0c0 5.6 7 10 7 10Z"
                                    />
                                    <circle cx="12" cy="11" r="2.5" />
                                </svg>

                                <svg
                                    v-else-if="step.icon === 'camera'"
                                    class="h-7 w-7"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M4 7h3l1.5-2h7L17 7h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1Z"
                                    />
                                    <circle cx="12" cy="13" r="3.5" />
                                </svg>

                                <svg
                                    v-else
                                    class="h-7 w-7"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="m4 4 16 8-16 8 4-8-4-8Z" />
                                    <path d="M8 12h12" />
                                </svg>
                            </div>

                            <div class="mx-auto mt-4 flex h-9 w-9 items-center justify-center rounded-full bg-[#1B5E20] text-xs font-bold text-white">
                                {{ step.number }}
                            </div>

                            <h3 class="mt-4 text-base font-bold text-black">
                                {{ step.title }}
                            </h3>

                            <p
                                class="mx-auto mt-2 max-w-[220px] text-sm leading-6 text-gray-600"
                            >
                                {{ step.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>


     
            <!-- TENTANG -->

            <section
                id="tentang"
                class="scroll-mt-20 bg-white py-16"
            >
                <div class="mx-auto max-w-6xl px-6 lg:px-8">

                <div
                    class="grid items-center gap-10 lg:grid-cols-[1.2fr_0.9fr]"
                >

            <!-- Text -->
            <div>
                <span
                    class="inline-flex rounded-full bg-[#E8F5E9] px-4 py-2 text-xs font-semibold text-[#1B5E20]"
                >
                    Tentang UrbanEye
                </span>

                <h2
                    class="mt-4 max-w-xl text-3xl font-bold leading-tight text-black lg:text-4xl"
                >
                    Dari Kepedulian Menjadi
                    <span class="text-[#1B5E20]">
                        Aksi Nyata
                    </span>
                </h2>

                <p
                    class="mt-5 max-w-xl text-sm leading-7 text-[#757575]"
                >
                    UrbanEye hadir sebagai ruang kolaborasi antara masyarakat
                    dan pihak terkait dalam membangun lingkungan perkotaan
                    yang lebih tertata. Setiap partisipasi menjadi bagian
                    dari proses untuk menciptakan perubahan yang berarti.
                </p>

                <p
                    class="mt-3 max-w-xl text-sm leading-7 text-[#757575]"
                >
                    Melalui semangat kepedulian bersama, UrbanEye mendorong
                    masyarakat untuk tidak hanya menjadi pengamat, tetapi
                    ikut mengambil peran dalam menjaga kualitas lingkungan
                    di sekitarnya.
                </p>
            </div>


            <!-- Highlight -->
            <div
                class="rounded-2xl bg-[#F7FAF7] p-7 shadow-[0_8px_24px_rgba(0,0,0,0.08)]"
            >
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#1B5E20] text-xl text-white"
                    >
                        ♻
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-black">
                            Visi UrbanEye
                        </h3>

                        <p class="mt-2 text-xs leading-6 text-[#757575]">
                            Mendorong terciptanya lingkungan perkotaan yang
                            lebih peduli, responsif, dan berkelanjutan melalui
                            keterlibatan masyarakat.
                        </p>
                    </div>
                </div>

                <div class="mt-6 h-px bg-gray-200"></div>

                <div class="mt-6 flex items-start gap-4">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#E8F5E9] text-xl text-[#1B5E20]"
                    >
                        🌱
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-black">
                            Semangat UrbanEye
                        </h3>

                        <p class="mt-2 text-xs leading-6 text-[#757575]">
                            Karena perubahan sebuah kota dapat dimulai dari
                            kepedulian sederhana yang dilakukan bersama.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

        <!--Footer-->
        <footer
            id="kontak"
            class="scroll-mt-20 bg-[#2E7D32] text-white"
        >   
            <div
                class="mx-auto grid max-w-7xl gap-2 px-6 py-10 lg:grid-cols-[1.5fr_1fr_1fr_1fr] lg:px-8"
            >

        <!-- Brand -->
        <div>
            <h3 class="text-xl font-bold leading-6">
                Bersama UrbanEye,
                Kita Wujudkan Kota yang Lebih Bersih & Nyaman
            </h3>

            <p class="mt-3 max-w-sm text-xs leading-5 text-green-50">
                Platform pelaporan dan pemantauan masalah lingkungan
                perkotaan berbasis crowdsourcing.
            </p>
        </div>

        <!-- Navigasi -->
        <div>
            <h4 class="text-sm font-bold">
                Navigasi
            </h4>

            <div class="mt-3 space-y-2 text-xs text-green-50">
                <button
                    type="button"
                    class="block transition hover:text-white"
                    @click="scrollToSection('beranda')"
                >
                    Beranda
                </button>

                <button
                    type="button"
                    class="block transition hover:text-white"
                    @click="scrollToSection('fitur')"
                >
                    Fitur
                </button>

                <button
                    type="button"
                    class="block transition hover:text-white"
                    @click="scrollToSection('cara-kerja')"
                >
                    Cara Kerja
                </button>

                <button
                    type="button"
                    class="block transition hover:text-white"
                    @click="scrollToSection('tentang')"
                >
                    Tentang
                </button>
            </div>
        </div>

        <!-- Informasi -->
        <div>
            <h4 class="text-sm font-bold">
                Informasi
            </h4>

            <div class="mt-3 space-y-2 text-xs text-green-50">
                <a
                    href="#"
                    class="block transition hover:text-white"
                >
                    FAQ
                </a>

                <a
                    href="#"
                    class="block transition hover:text-white"
                >
                    Kebijakan Privasi
                </a>

                <a
                    href="#"
                    class="block transition hover:text-white"
                >
                    Syarat & Ketentuan
                </a>
            </div>
        </div>

        <!-- Social Media -->
        <div>
            <h4 class="text-sm font-bold">
                Ikuti Kami
            </h4>

            <div class="mt-4 flex items-center gap-3">

                <!-- Instagram -->
                <a
                    href="#"
                    aria-label="Instagram"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:-translate-y-1 hover:bg-white/20"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect
                            x="3"
                            y="3"
                            width="18"
                            height="18"
                            rx="5"
                        />
                        <circle
                            cx="12"
                            cy="12"
                            r="4"
                        />
                        <circle
                            cx="17.5"
                            cy="6.5"
                            r="1"
                            fill="currentColor"
                            stroke="none"
                        />
                    </svg>
                </a>

                <!-- Facebook -->
                <a
                    href="#"
                    aria-label="Facebook"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:-translate-y-1 hover:bg-white/20"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M14 8h3V4h-3c-2.8 0-5 2.2-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9c0-.6.4-1 1-1Z"
                        />
                    </svg>
                </a>

                <!-- X -->
                <a
                    href="#"
                    aria-label="X"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:-translate-y-1 hover:bg-white/20"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M5 4h4.7l3.1 4.3L16.6 4H19l-5.1 6 5.5 7H14.7l-3.3-4.6L7.4 17H5l5.3-6.2L5 4Zm3.3 1.7 6.6 9.6h1.8l-6.6-9.6H8.3Z"
                        />
                    </svg>
                </a>

                <!-- YouTube -->
                <a
                    href="#"
                    aria-label="YouTube"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:-translate-y-1 hover:bg-white/20"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M21.6 7.2a2.9 2.9 0 0 0-2-2C17.8 4.7 12 4.7 12 4.7s-5.8 0-7.6.5a2.9 2.9 0 0 0-2 2C1.9 9 1.9 12 1.9 12s0 3 .5 4.8a2.9 2.9 0 0 0 2 2c1.8.5 7.6.5 7.6.5s5.8 0 7.6-.5a2.9 2.9 0 0 0 2-2c.5-1.8.5-4.8.5-4.8s0-3-.5-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"
                        />
                    </svg>
                </a>

            </div>
        </div>

    </div>

        <!-- Copyright -->
        <div
            class="border-t border-white/20 px-6 py-4 text-center text-[10px] text-green-50"
        >
            © 2026 UrbanEye. All rights reserved.
        </div>
    </footer>

    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, onMounted, ref } from 'vue'

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },

    canRegister: {
        type: Boolean,
        default: true,
    },
})

const activeSection = ref('beranda')

const features = [
    {
        title: 'Berbasis Lokasi',
        description:
            'Setiap laporan dilengkapi lokasi sehingga memudahkan proses monitoring dan tindak lanjut.',
        icon: 'location',
    },
    {
        title: 'Upload Foto Bukti',
        description:
            'Unggah foto kondisi lingkungan secara langsung untuk memperkuat laporan yang dikirimkan.',
        icon: 'camera',
    },
    {
        title: 'Realtime Tracking',
        description:
            'Pantau perkembangan laporan mulai dari diproses hingga selesai secara realtime.',
        icon: 'tracking',
    },
    {
        title: 'Notifikasi Otomatis',
        description:
            'Dapatkan notifikasi ketika status laporan mengalami perubahan.',
        icon: 'notification',
    },
]

const steps = [
    {
        number: '1',
        title: 'Laporkan Masalah',
        description:
            'Pilih kategori permasalahan lingkungan dan lengkapi informasi laporan.',
        icon: 'report',
    },
    {
        number: '2',
        title: 'Tentukan Lokasi',
        description:
            'Pilih lokasi kejadian pada peta atau aktifkan GPS otomatis.',
        icon: 'location',
    },
    {
        number: '3',
        title: 'Lampirkan Bukti',
        description:
            'Ambil atau unggah foto sebagai bukti laporan terkait kondisi lingkungan.',
        icon: 'camera',
    },
    {
        number: '4',
        title: 'Kirim Laporan',
        description:
            'Kirim laporan lalu laporan akan diterima untuk diproses oleh pihak terkait.',
        icon: 'send',
    },
]

let sectionObserver = null

function scrollToSection(id) {
    activeSection.value = id

    document.getElementById(id)?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    })
}

onMounted(() => {
    const sections = [
        'beranda',
        'fitur',
        'cara-kerja',
        'tentang',
        'kontak',
    ]
        .map((id) => document.getElementById(id))
        .filter(Boolean)

    sectionObserver = new IntersectionObserver(
        (entries) => {
            const visibleSection = entries
                .filter((entry) => entry.isIntersecting)
                .sort(
                    (a, b) =>
                        b.intersectionRatio - a.intersectionRatio
                )[0]

            if (visibleSection) {
                activeSection.value = visibleSection.target.id
            }
        },
        {
            rootMargin: '-18% 0px -60% 0px',
            threshold: [0.1, 0.25, 0.5],
        }
    )

    sections.forEach((section) => {
        sectionObserver.observe(section)
    })
})

onBeforeUnmount(() => {
    sectionObserver?.disconnect()
})
</script>

