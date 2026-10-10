<script setup>

import { Link, Form } from '@inertiajs/vue3'

import { ref } from 'vue'

import {
    Menu,
    Bell,
    UserCircle,
    LayoutDashboard,
    ClipboardList,
    FileText,
    History,
    BarChart3,
    Settings,
    CircleHelp,
} from 'lucide-vue-next'

const profileOpen = ref(false)

</script>

<template>

    <div class="min-h-screen bg-gray-100">

        <div class="flex min-h-screen">

            <!-- SIDEBAR -->

            <aside class="hidden md:flex w-64 bg-[#06202B] flex-col">

                <!-- LOGO -->

                <div class="h-20 flex items-center px-5">

                    <Link
                        href="/dashboard"
                        class="flex items-center"
                    >
                        <img
                            src="/images/urbaneye-logo-navy.png"
                            alt="UrbanEye"
                            class="w-[150px] h-auto object-contain"
                        />
                    </Link>

                </div>


                <!-- USER SIDEBAR -->

                <div class="px-5 py-5">

                    <div class="flex items-center gap-3">

                        <UserCircle
                            class="w-10 h-10 text-gray-200"
                        />

                        <div>

                            <p class="font-semibold text-sm text-white">
                                Hallo, Petugas!<span class="ml-1">👋</span>
                            </p>

                            <p class="text-xs text-gray-300">
                                Petugas
                            </p>

                        </div>

                    </div>

                </div>


                <!-- MENU -->

                <nav class="flex-1 px-3 py-3 space-y-1">

                    <!-- DASHBOARD -->

                    <Link
                        href="/dashboard"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                        :class="{
                            'bg-white/10 text-white': $page.url === '/dashboard'
                        }"
                    >

                        <LayoutDashboard class="w-4 h-4" />

                        <span class="text-sm">
                            Dashboard
                        </span>

                    </Link>


                    <!-- TUGAS SAYA -->

                    <Link
                        href="/petugas/tasks"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                        :class="{
                            'bg-white/10 text-white': $page.url.startsWith('/petugas/tasks')
                        }"
                    >

                        <ClipboardList class="w-4 h-4" />

                        <span class="text-sm">
                            Tugas Saya
                        </span>

                    </Link>


                    <!-- PETA LAPORAN -->

                    <Link
                        href="/dashboard#peta-laporan"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                    >

                        <FileText class="w-4 h-4" />

                        <span class="text-sm">
                            Peta Laporan
                        </span>

                    </Link>


                    <!-- RIWAYAT PENANGANAN -->

                    <Link
                        href="/petugas/history"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                        :class="{
                            'bg-white/10 text-white': $page.url.startsWith('/petugas/history')
                        }"
                    >

                        <History class="w-4 h-4" />

                        <span class="text-sm">
                            Riwayat Penanganan
                        </span>

                    </Link>


                    <!-- STATISTIK -->

                    <Link
                        href="/petugas/statistics"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                        :class="{
                            'bg-white/10 text-white': $page.url.startsWith('/petugas/statistics')
                        }"
                    >

                        <BarChart3 class="w-4 h-4" />

                        <span class="text-sm">
                            Statistik
                        </span>

                    </Link>


                    <!-- PENGATURAN -->

                    <Link
                        href="/petugas/settings"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                        :class="{
                            'bg-white/10 text-white': $page.url.startsWith('/petugas/settings')
                        }"
                    >

                        <Settings class="w-4 h-4" />

                        <span class="text-sm">
                            Pengaturan
                        </span>

                    </Link>


                    <!-- BANTUAN -->

                    <Link
                        href="/petugas/help"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white"
                        :class="{
                            'bg-white/10 text-white': $page.url.startsWith('/petugas/help')
                        }"
                    >

                        <CircleHelp class="w-4 h-4" />

                        <span class="text-sm">
                            Bantuan
                        </span>

                    </Link>

                </nav>

            </aside>


            <!-- MAIN CONTENT -->

            <main class="flex-1 min-w-0">

                <!-- HEADER -->

                <div class="bg-white border-b border-gray-200">

                    <div class="min-h-20 px-6 lg:px-8 flex items-center justify-between">

                        <!-- KIRI -->

                        <div class="flex items-center gap-3">

                            <button
                                type="button"
                                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                            >
                                <Menu class="w-6 h-6" />
                            </button>


                            <div>

                                <h2 class="text-xl font-semibold text-gray-800">
                                    Dashboard Petugas
                                </h2>

                                <p class="text-sm text-gray-500">
                                    Laporan permasalahan di sekitar Anda
                                </p>

                            </div>

                        </div>


                        <!-- KANAN -->

                        <div class="flex items-center gap-5">

                            <!-- NOTIFIKASI -->

                            <button
                                type="button"
                                class="relative p-2 text-gray-500 hover:text-gray-700"
                            >

                                <Bell class="w-5 h-5" />

                                <span
                                    class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"
                                ></span>

                            </button>


                            <!-- PROFILE -->

                            <div class="relative">

                                <!-- PROFILE BUTTON -->

                                <button
                                    type="button"
                                    class="flex items-center gap-2 hover:bg-gray-100 rounded-lg px-2 py-1.5"
                                    @click="profileOpen = !profileOpen"
                                >

                                    <UserCircle
                                        class="w-8 h-8 text-gray-400"
                                    />

                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $page.props.auth.user.name }}
                                    </span>

                                    <svg
                                        class="w-4 h-4 text-gray-500 transition-transform"
                                        :class="{ 'rotate-180': profileOpen }"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >

                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />

                                    </svg>

                                </button>


                                <!-- DROPDOWN -->

                                <div
                                    v-if="profileOpen"
                                    class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50"
                                >

                                    <Link
                                        href="/profile"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        Profil
                                    </Link>


                                    <Form
                                        method="post"
                                        action="/logout"
                                    >

                                        <button
                                            type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                        >
                                            Logout
                                        </button>

                                    </Form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ISI HALAMAN -->

                <div>

                    <slot />

                </div>

            </main>

        </div>

    </div>

</template>