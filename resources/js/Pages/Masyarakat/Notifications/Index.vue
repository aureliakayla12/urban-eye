
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { Bell, Check, CheckCheck } from 'lucide-vue-next'

const props = defineProps({
    notifications: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            current_page: 1,
            last_page: 1,
        }),
    },
    unreadCount: {
        type: Number,
        default: 0,
    },
})

const markAsRead = (id) => {
    router.patch(
        route('masyarakat.notifications.read', id),
        {},
        {
            preserveScroll: true,
        },
    )
}

const markAllAsRead = () => {
    router.patch(
        route('masyarakat.notifications.read-all'),
        {},
        {
            preserveScroll: true,
        },
    )
}

const formatDate = (date) => {
    if (!date) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(date))
}
</script>

<template>
    <Head title="Notifikasi" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Notifikasi
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pantau perkembangan laporan dan informasi terbaru dari UrbanEye.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100"
                        >
                            <Bell class="h-6 w-6 text-green-700" />
                        </div>

                        <div>
                            <h1 class="text-lg font-semibold text-gray-800">
                                Semua Notifikasi
                            </h1>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ unreadCount }} notifikasi belum dibaca
                            </p>
                        </div>
                    </div>

                    <button
                        v-if="unreadCount > 0"
                        type="button"
                        @click="markAllAsRead"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-800"
                    >
                        <CheckCheck class="h-4 w-4" />
                        Tandai semua dibaca
                    </button>
                </div>

                <!-- Empty State -->
                <div
                    v-if="!notifications.data || notifications.data.length === 0"
                    class="rounded-xl bg-white px-6 py-14 text-center shadow-sm"
                >
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100"
                    >
                        <Bell class="h-8 w-8 text-gray-400" />
                    </div>

                    <h3 class="text-base font-semibold text-gray-800">
                        Belum ada notifikasi
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                        Notifikasi tentang perkembangan laporanmu akan muncul
                        di sini ketika tersedia.
                    </p>
                </div>

                <!-- Notification List -->
                <div v-else class="space-y-3">
                    <div
                        v-for="notification in notifications.data"
                        :key="notification.id"
                        class="rounded-xl border p-5 shadow-sm transition"
                        :class="
                            notification.is_read
                                ? 'border-gray-200 bg-white'
                                : 'border-green-200 bg-green-50/50'
                        "
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                                :class="
                                    notification.is_read
                                        ? 'bg-gray-100'
                                        : 'bg-green-100'
                                "
                            >
                                <Bell
                                    class="h-5 w-5"
                                    :class="
                                        notification.is_read
                                            ? 'text-gray-500'
                                            : 'text-green-700'
                                    "
                                />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"
                                >
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-semibold text-gray-800">
                                                {{ notification.title }}
                                            </h3>

                                            <span
                                                v-if="!notification.is_read"
                                                class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800"
                                            >
                                                Baru
                                            </span>
                                        </div>

                                        <p
                                            class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600"
                                        >
                                            {{ notification.message }}
                                        </p>

                                        <p
                                            v-if="notification.report_id"
                                            class="mt-2 text-xs font-medium text-gray-500"
                                        >
                                            Laporan #{{ notification.report_id }}
                                        </p>

                                        <p class="mt-2 text-xs text-gray-400">
                                            {{ formatDate(notification.created_at) }}
                                        </p>
                                    </div>

                                    <button
                                        v-if="!notification.is_read"
                                        type="button"
                                        @click="markAsRead(notification.id)"
                                        class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-lg border border-green-200 bg-white px-3 py-2 text-xs font-medium text-green-800 transition hover:bg-green-50"
                                    >
                                        <Check class="h-4 w-4" />
                                        Tandai dibaca
                                    </button>

                                    <span
                                        v-else
                                        class="inline-flex shrink-0 items-center gap-1 self-start text-xs text-gray-400"
                                    >
                                        <CheckCheck class="h-4 w-4" />
                                        Sudah dibaca
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="notifications.links && notifications.last_page > 1"
                        class="flex flex-wrap justify-center gap-2 pt-4"
                    >
                        <button
                            v-for="(link, index) in notifications.links"
                            :key="index"
                            type="button"
                            :disabled="!link.url"
                            @click="link.url && router.visit(link.url, { preserveScroll: true })"
                            class="rounded-lg border px-3 py-2 text-sm transition disabled:cursor-not-allowed disabled:opacity-40"
                            :class="
                                link.active
                                    ? 'border-green-700 bg-green-700 text-white'
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                            "
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
