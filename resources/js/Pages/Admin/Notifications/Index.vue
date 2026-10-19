
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Bell, Check, CheckCheck, Inbox } from 'lucide-vue-next'

defineProps({
    notifications: {
        type: Object,
        required: true,
    },
    unreadCount: {
        type: Number,
        default: 0,
    },
})

const markRead = (id) => {
    router.patch(
        route('admin.notifications.read', id),
        {},
        { preserveScroll: true }
    )
}

const markAllRead = () => {
    router.patch(
        route('admin.notifications.read-all'),
        {},
        { preserveScroll: true }
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
    <Head title="Notifikasi Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    Notifikasi
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi terbaru mengenai laporan UrbanEye.
                </p>
            </div>
        </template>

        <div class="space-y-6">
          
            <div
                class="flex flex-col justify-between gap-4 rounded-2xl
                       border border-gray-200 bg-white p-5 shadow-sm
                       sm:flex-row sm:items-center"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center
                               rounded-xl bg-green-50 text-green-700"
                    >
                        <Bell :size="24" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900">
                            Pemberitahuan Admin
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ unreadCount }} notifikasi belum dibaca
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    :disabled="unreadCount === 0"
                    @click="markAllRead"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg bg-[#1B5E20] px-4 py-2.5
                           text-sm font-medium text-white transition
                           hover:bg-green-800
                           disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <CheckCheck :size="17" />
                    Tandai Semua Dibaca
                </button>
            </div>

        
            <div
                class="overflow-hidden rounded-2xl border border-gray-200
                       bg-white shadow-sm"
            >
                <div
                    class="border-b border-gray-100 px-5 py-4"
                >
                    <h2 class="font-semibold text-gray-900">
                        Semua Notifikasi
                    </h2>
                </div>

                <div
                    v-if="notifications.data.length > 0"
                    class="divide-y divide-gray-100"
                >
                    <div
                        v-for="notification in notifications.data"
                        :key="notification.id"
                        class="flex flex-col gap-4 p-5 transition
                               sm:flex-row sm:items-start sm:justify-between"
                        :class="
                            notification.is_read
                                ? 'bg-white'
                                : 'bg-green-50/60'
                        "
                    >
                        <div class="flex min-w-0 gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center
                                       justify-center rounded-full"
                                :class="
                                    notification.is_read
                                        ? 'bg-gray-100 text-gray-500'
                                        : 'bg-green-100 text-green-700'
                                "
                            >
                                <Bell :size="19" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ notification.title }}
                                    </h3>

                                    <span
                                        v-if="!notification.is_read"
                                        class="rounded-full bg-green-100
                                               px-2 py-0.5 text-xs
                                               font-medium text-green-800"
                                    >
                                        Baru
                                    </span>
                                </div>

                                <p class="mt-1 text-sm leading-6 text-gray-600">
                                    {{ notification.message }}
                                </p>

                                <p class="mt-2 text-xs text-gray-400">
                                    {{ formatDate(notification.created_at) }}
                                </p>

                                <Link
                                    v-if="notification.report_id"
                                    :href="
                                        route('admin.reports.show', {
                                            id: notification.report_id,
                                        })
                                    "
                                    class="mt-3 inline-block text-sm
                                           font-medium text-green-800
                                           hover:underline"
                                >
                                    Lihat laporan
                                </Link>
                            </div>
                        </div>

                        <button
                            v-if="!notification.is_read"
                            type="button"
                            @click="markRead(notification.id)"
                            class="inline-flex shrink-0 items-center gap-2
                                   self-start rounded-lg border
                                   border-gray-200 bg-white px-3 py-2
                                   text-sm font-medium text-gray-700
                                   transition hover:bg-gray-50"
                        >
                            <Check :size="16" />
                            Tandai Dibaca
                        </button>
                    </div>
                </div>

               
                <div
                    v-else
                    class="flex flex-col items-center px-5 py-16
                           text-center"
                >
                    <Inbox :size="42" class="text-gray-300" />

                    <h3 class="mt-4 font-semibold text-gray-800">
                        Belum Ada Notifikasi
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Notifikasi laporan baru akan muncul di sini.
                    </p>
                </div>

               
                <div
                    v-if="notifications.last_page > 1"
                    class="flex flex-wrap items-center justify-center
                           gap-2 border-t border-gray-100 p-4"
                >
                    <template
                        v-for="(link, index) in notifications.links"
                        :key="index"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            class="rounded-lg border px-3 py-2 text-sm"
                            :class="
                                link.active
                                    ? 'border-[#1B5E20] bg-[#1B5E20] text-white'
                                    : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                            "
                            v-html="link.label"
                        />

                        <span
                            v-else
                            class="rounded-lg border border-gray-100
                                   px-3 py-2 text-sm text-gray-300"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
