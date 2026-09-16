<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    LayoutDashboard,
    ClipboardList,
    Users,
    UserRoundCog,
    ChartNoAxesCombined,
    Map,
    Trophy,
    Settings,
    FileText,
    History,
    CircleHelp,
    PlusCircle,
    Gift,
    LogOut,
    Bell,
    UserCircle,
    Menu,
    X,
} from 'lucide-vue-next'

const page = usePage()

const showingUserMenu = ref(false)
const showingMobileMenu = ref(false)

const user = computed(() => page.props.auth?.user)
const role = computed(() => page.props.auth?.role)

const menuItems = computed(() => {
    if (role.value === 'admin') {
        return [
            {
                label: 'Dashboard',
                route: 'dashboard',
                icon: LayoutDashboard,
            },
            {
                label: 'Kelola Laporan',
                route: 'admin.reports.index',
                icon: ClipboardList,
            },
            {
                label: 'Kelola Pengguna',
                icon: Users,
                children: [
                    {
                        label: 'Masyarakat',
                        route: 'admin.users.masyarakat.index',
                        icon: Users,
                    },
                    {
                        label: 'Petugas',
                        route: 'admin.users.petugas.index',
                        icon: UserRoundCog,
                    },
                ],
            },
            {
                label: 'Statistik & Grafik',
                route: 'admin.statistics.index',
                icon: ChartNoAxesCombined,
            },
            {
                label: 'Peta Monitoring',
                route: 'admin.map.index',
                icon: Map,
            },
            {
                label: 'Reward & Leaderboard',
                route: 'admin.gamification.rewards',
                icon: Trophy,
            },
            {
                label: 'Pengaturan Sistem',
                route: 'admin.settings.index',
                icon: Settings,
            },
            {
                label: 'Laporan & Export',
                route: 'admin.export.index',
                icon: FileText,
            },
        ]
    }

    if (role.value === 'petugas') {
        return [
            {
                label: 'Dashboard',
                route: 'dashboard',
                icon: LayoutDashboard,
            },
            {
                label: 'Tugas Saya',
                route: 'petugas.tasks.index',
                icon: ClipboardList,
            },
            {
                label: 'Peta Laporan',
                route: 'petugas.map.index',
                icon: Map,
            },
            {
                label: 'Riwayat Penanganan',
                route: 'petugas.history.index',
                icon: History,
            },
            {
                label: 'Statistik',
                route: 'petugas.statistics.index',
                icon: ChartNoAxesCombined,
            },
            {
                label: 'Pengaturan',
                route: 'petugas.settings.index',
                icon: Settings,
            },
            {
                label: 'Bantuan',
                route: 'petugas.help.index',
                icon: CircleHelp,
            },
        ]
    }

    return [
        {
            label: 'Dashboard',
            route: 'dashboard',
            icon: LayoutDashboard,
        },
        {
            label: 'Buat Laporan',
            route: 'masyarakat.reports.create',
            icon: PlusCircle,
        },
        {
            label: 'Riwayat Laporan',
            route: 'masyarakat.reports.index',
            icon: History,
        },
        {
            label: 'Leaderboard',
            route: 'masyarakat.leaderboard.index',
            icon: Trophy,
        },
        {
            label: 'Reward Saya',
            route: 'masyarakat.rewards.index',
            icon: Gift,
        },
        {
            label: 'Pengaturan',
            route: 'profile.edit',
            icon: Settings,
        },
        {
            label: 'Bantuan',
            route: 'masyarakat.help.index',
            icon: CircleHelp,
        },
    ]
})

const isActive = (routeName) => {
    if (!routeName) {
        return false
    }

    return route().current(routeName)
}

const isChildActive = (children) => {
    return children?.some((child) => isActive(child.route))
}

const closeMobileMenu = () => {
    showingMobileMenu.value = false
}
</script>

<template>
    <div class="min-h-screen bg-[#F4F6F8]">

        <!-- Mobile Overlay -->
        <div
            v-if="showingMobileMenu"
            class="fixed inset-0 z-40 bg-black/30 lg:hidden"
            @click="closeMobileMenu"
        ></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col bg-[#0B1F2A] text-white transition-transform duration-200 lg:translate-x-0"
            :class="
                showingMobileMenu
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0'
            "
        >

            <!-- Logo -->
            <div class="flex h-20 shrink-0 items-center border-b border-white/10 px-7">
                <Link
                    :href="route('dashboard')"
                    class="flex items-center"
                    @click="closeMobileMenu"
                >
                    <img
                        src="/images/urbaneye-logo-navy.png"
                        alt="UrbanEye"
                        class="h-[58px] w-auto object-contain"
                    />
                </Link>

                <button
                    type="button"
                    class="ml-auto rounded-lg p-1 text-white lg:hidden"
                    @click="closeMobileMenu"
                >
                    <X :size="22" />
                </button>
            </div>

            <!-- Profile Petugas & Masyarakat -->
            <div
                v-if="role !== 'admin'"
                class="shrink-0 px-5 py-6"
            >
                <div class="flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-white"
                    >
                        <UserCircle
                            :size="31"
                            stroke-width="1.8"
                        />
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-[15px] font-semibold">
                            Halo, {{ user?.name }}!
                        </p>

                        <p class="mt-0.5 text-sm text-gray-300">
                            {{ role }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto px-4 pb-5">
                <div class="space-y-1.5">

                    <template
                        v-for="item in menuItems"
                        :key="item.label"
                    >

                        <!-- Single Menu -->
                        <Link
                            v-if="item.route"
                            :href="route(item.route)"
                            class="group flex h-12 items-center gap-4 rounded-xl px-4 text-[15px] transition"
                            :class="
                                isActive(item.route)
                                    ? 'bg-[#1B5E20] font-medium text-white'
                                    : 'text-gray-200 hover:bg-white/10 hover:text-white'
                            "
                            @click="closeMobileMenu"
                        >
                            <component
                                :is="item.icon"
                                :size="22"
                                stroke-width="1.8"
                                class="shrink-0"
                            />

                            <span>
                                {{ item.label }}
                            </span>
                        </Link>

                        <!-- Parent Menu -->
                        <div v-else>

                            <div
                                class="flex h-12 items-center gap-4 rounded-xl px-4 text-[15px]"
                                :class="
                                    isChildActive(item.children)
                                        ? 'text-white'
                                        : 'text-gray-200'
                                "
                            >
                                <component
                                    :is="item.icon"
                                    :size="22"
                                    stroke-width="1.8"
                                    class="shrink-0"
                                />

                                <span>
                                    {{ item.label }}
                                </span>
                            </div>

                            <div class="ml-8 space-y-1">

                                <Link
                                    v-for="child in item.children"
                                    :key="child.label"
                                    :href="route(child.route)"
                                    class="flex h-10 items-center gap-3 rounded-lg px-3 text-sm transition"
                                    :class="
                                        isActive(child.route)
                                            ? 'bg-white/10 text-white'
                                            : 'text-gray-400 hover:bg-white/10 hover:text-white'
                                    "
                                    @click="closeMobileMenu"
                                >
                                    <component
                                        :is="child.icon"
                                        :size="18"
                                        stroke-width="1.8"
                                    />

                                    <span>
                                        {{ child.label }}
                                    </span>
                                </Link>

                            </div>
                        </div>

                    </template>

                </div>
            </nav>

            <!-- Admin Profile Bottom -->
            <div
                v-if="role === 'admin'"
                class="shrink-0 border-t border-white/10 p-5"
            >
                <div class="flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-white"
                    >
                        <UserCircle
                            :size="31"
                            stroke-width="1.8"
                        />
                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-sm font-semibold">
                            {{ user?.name }}
                        </p>

                        <p class="mt-0.5 text-xs text-gray-300">
                            Super Admin
                        </p>

                    </div>

                </div>
            </div>

        </aside>

        <!-- Main -->
        <div class="min-h-screen lg:ml-[260px]">

            <!-- Header -->
            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-gray-200 bg-white px-5 shadow-sm sm:px-8"
            >

                <div class="flex items-center gap-4">

                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-700 hover:bg-gray-100 lg:hidden"
                        @click="showingMobileMenu = true"
                    >
                        <Menu :size="24" />
                    </button>

                    <div>
                        <slot name="header" />
                    </div>

                </div>

                <div class="relative flex items-center gap-5">

                    <!-- Notification -->
                    <button
                        type="button"
                        class="relative text-gray-900"
                    >
                        <Bell
                            :size="21"
                            stroke-width="1.9"
                        />

                        <span
                            class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-red-500"
                        ></span>
                    </button>

                    <!-- Header User -->
                    <button
                        type="button"
                        class="flex items-center gap-2"
                        @click="showingUserMenu = !showingUserMenu"
                    >
                        <UserCircle
                            :size="25"
                            class="text-black"
                            stroke-width="1.8"
                        />

                        <span
                            class="hidden text-sm font-semibold text-gray-900 sm:block"
                        >
                            {{ user?.name }}
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

                    <!-- User Dropdown -->
                    <div
                        v-if="showingUserMenu"
                        class="absolute right-0 top-12 w-44 rounded-xl border border-gray-100 bg-white p-2 shadow-lg"
                    >
                        <Link
                            :href="route('profile.edit')"
                            class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                            Profil
                        </Link>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                        >
                            <LogOut :size="17" />
                            Keluar
                        </Link>
                    </div>

                </div>

            </header>

            <!-- Content -->
            <main class="p-5 sm:p-7 lg:p-8">
                <slot />
            </main>

        </div>

    </div>
</template>