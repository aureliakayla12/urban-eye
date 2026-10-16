<script setup>
import { Link, Form } from '@inertiajs/vue3'
import { ref } from 'vue'

import {
    Menu,
    Bell,
    UserCircle,
    LayoutDashboard,
    ClipboardList,
    Map,
    History,
    ChartNoAxesCombined,
    Settings,
    CircleHelp,
    X,
    LogOut,
} from 'lucide-vue-next'

const profileOpen = ref(false)
const showingSidebar = ref(true)
const showingMobileMenu = ref(false)

const toggleSidebar = () => {
    if (window.innerWidth < 1024) {
        showingMobileMenu.value = !showingMobileMenu.value
    } else {
        showingSidebar.value = !showingSidebar.value
    }
}

const closeMobileMenu = () => {
    showingMobileMenu.value = false
}
</script>

<template>
    <div class="min-h-screen bg-[#F4F6F8]">
        <!-- MOBILE OVERLAY -->
        <div
            v-if="showingMobileMenu"
            class="fixed inset-0 z-40 bg-black/30 lg:hidden"
            @click="closeMobileMenu"
        ></div>

        <!-- SIDEBAR -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col overflow-hidden bg-[#0B1F2A] text-white transition-all duration-300 lg:translate-x-0"
            :class="[
                showingMobileMenu
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0',
                showingSidebar ? 'w-[260px]' : 'w-[80px]',
            ]"
        >
            <!-- LOGO -->
            <div
                class="flex h-20 shrink-0 items-center border-b border-white/10 transition-all duration-300"
                :class="
                    showingSidebar
                        ? 'px-7'
                        : 'justify-center px-3'
                "
            >
                <Link
                    href="/dashboard"
                    class="flex items-center"
                    @click="closeMobileMenu"
                >
                    <img
                        src="/images/urbaneye-logo-navy.png"
                        alt="UrbanEye"
                        class="shrink-0 object-contain transition-all duration-300"
                        :class="
                            showingSidebar
                                ? 'h-[58px] w-auto'
                                : 'h-[42px] w-[42px]'
                        "
                    />
                </Link>

                <button
                    type="button"
                    class="ml-auto rounded-lg p-1 text-white lg:hidden"
                    @click="closeMobileMenu"
                    aria-label="Tutup sidebar"
                >
                    <X :size="22" />
                </button>
            </div>

            <!-- PROFIL PETUGAS -->
            <div
                class="shrink-0 transition-all duration-300"
                :class="
                    showingSidebar
                        ? 'px-5 py-6'
                        : 'px-3 py-5'
                "
            >
                <div
                    class="flex items-center"
                    :class="
                        showingSidebar
                            ? 'gap-3'
                            : 'justify-center'
                    "
                >
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-white"
                    >
                        <UserCircle
                            :size="31"
                            stroke-width="1.8"
                        />
                    </div>

                    <div
                        v-if="showingSidebar"
                        class="min-w-0"
                    >
                        <p class="truncate text-[15px] font-semibold">
                            Halo, Petugas! 👋
                        </p>

                        <p class="mt-0.5 text-sm text-gray-300">
                            Petugas
                        </p>
                    </div>
                </div>
            </div>

            <!-- MENU SIDEBAR -->
            <nav class="flex-1 overflow-y-auto px-4 pb-5">
                <div class="space-y-1.5">
                    <!-- DASHBOARD -->
                    <Link
                        href="/dashboard"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',
                            $page.url === '/dashboard'
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Dashboard"
                        @click="closeMobileMenu"
                    >
                        <LayoutDashboard
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Dashboard
                        </span>
                    </Link>

                    <!-- TUGAS SAYA -->
                    <Link
                        href="/petugas/tasks"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',

                            $page.url.startsWith('/petugas/tasks') &&
                            !$page.url.includes('from=history')
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Tugas Saya"
                        @click="closeMobileMenu"
                    >
                        <ClipboardList
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Tugas Saya
                        </span>
                    </Link>

                    <!-- PETA LAPORAN -->
                    <Link
                        :href="route('petugas.map.index')"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',

                            $page.url.startsWith('/petugas/map')
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Peta Laporan"
                        @click="closeMobileMenu"
                    >
                        <Map
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Peta Laporan
                        </span>
                    </Link>

                    <!-- RIWAYAT PENANGANAN -->
                    <Link
                        href="/petugas/history"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',

                            (
                                $page.url.startsWith('/petugas/history') ||
                                (
                                    $page.url.startsWith('/petugas/tasks') &&
                                    $page.url.includes('from=history')
                                )
                            )
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Riwayat Penanganan"
                        @click="closeMobileMenu"
                    >
                        <History
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Riwayat Penanganan
                        </span>
                    </Link>

                    <!-- STATISTIK -->
                    <Link
                        href="/petugas/statistics"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',

                            $page.url.startsWith('/petugas/statistics')
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Statistik"
                        @click="closeMobileMenu"
                    >
                        <ChartNoAxesCombined
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Statistik
                        </span>
                    </Link>

                    <!-- PENGATURAN -->
                    <Link
                        href="/petugas/settings"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',

                            $page.url.startsWith('/petugas/settings')
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Pengaturan"
                        @click="closeMobileMenu"
                    >
                        <Settings
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Pengaturan
                        </span>
                    </Link>

                    <!-- BANTUAN -->
                    <Link
                        href="/petugas/help"
                        class="group flex h-12 items-center rounded-xl text-[15px] transition"
                        :class="[
                            showingSidebar
                                ? 'gap-4 px-4'
                                : 'justify-center px-0',

                            $page.url.startsWith('/petugas/help')
                                ? 'bg-[#1B5E20] font-medium text-white'
                                : 'text-gray-200 hover:bg-white/10 hover:text-white',
                        ]"
                        title="Bantuan"
                        @click="closeMobileMenu"
                    >
                        <CircleHelp
                            :size="22"
                            stroke-width="1.8"
                            class="shrink-0"
                        />

                        <span
                            v-if="showingSidebar"
                            class="whitespace-nowrap"
                        >
                            Bantuan
                        </span>
                    </Link>
                </div>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <div
            class="min-h-screen transition-all duration-300"
            :class="
                showingSidebar
                    ? 'lg:ml-[260px]'
                    : 'lg:ml-[80px]'
            "
        >
            <!-- HEADER -->
            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-gray-200 bg-white px-5 shadow-sm transition-all duration-300 sm:px-8"
            >
                <!-- KIRI -->
                <div class="flex items-center gap-4">
                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-700 transition hover:bg-gray-100"
                        @click="toggleSidebar"
                        aria-label="Buka atau tutup sidebar"
                        title="Buka atau tutup sidebar"
                    >
                        <Menu :size="24" />
                    </button>

                    <div class="min-w-0">
                        <slot name="header">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Dashboard Petugas
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Laporan permasalahan di sekitar Anda
                                </p>
                            </div>
                        </slot>
                    </div>
                </div>

                <!-- KANAN -->
                <div class="relative flex items-center gap-5">
                    <!-- NOTIFIKASI -->
                    <button
                        type="button"
                        class="relative text-gray-900"
                        aria-label="Notifikasi"
                    >
                        <Bell
                            :size="21"
                            stroke-width="1.9"
                        />

                        <span
                            class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-red-500"
                        ></span>
                    </button>

                    <!-- PROFILE -->
                    <div class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-2"
                            @click="profileOpen = !profileOpen"
                        >
                            <UserCircle
                                :size="25"
                                class="text-black"
                                stroke-width="1.8"
                            />

                            <span class="hidden text-sm font-semibold text-gray-900 sm:block">
                                {{ $page.props.auth.user.name }}
                            </span>

                            <svg
                                class="hidden h-4 w-4 sm:block"
                                viewBox="0 0 20 20"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path d="m6 8 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- PROFILE DROPDOWN -->
                        <div
                            v-if="profileOpen"
                            class="absolute right-0 top-12 z-50 w-44 rounded-xl border border-gray-100 bg-white p-2 shadow-lg"
                        >
                            <Link
                                href="/profile"
                                class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                @click="profileOpen = false"
                            >
                                Profil
                            </Link>

                            <Form
                                method="post"
                                action="/logout"
                            >
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    <LogOut :size="17" />
                                    Keluar
                                </button>
                            </Form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ISI HALAMAN -->
            <main class="p-5 sm:p-7 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>