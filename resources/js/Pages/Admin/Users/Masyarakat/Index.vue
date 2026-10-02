<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    users: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
})

const search = ref('')

const filteredUsers = computed(() => {
    if (!search.value) {
        return props.users.data
    }

    const keyword = search.value.toLowerCase()

    return props.users.data.filter((user) => {
        return (
            user.name?.toLowerCase().includes(keyword) ||
            user.email?.toLowerCase().includes(keyword)
        )
    })
})
</script>

<template>
    <Head title="Kelola Masyarakat" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Kelola Masyarakat
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Kelola data pengguna masyarakat yang terdaftar di UrbanEye.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Card Utama -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">

                    <!-- Header -->
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Daftar Masyarakat
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Daftar pengguna dengan role masyarakat.
                                </p>
                            </div>

                            <!-- Search -->
                            <div class="w-full sm:w-72">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari nama atau email..."
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                />
                            </div>

                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        No
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Nama
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Email
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Terdaftar
                                    </th>

                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white">

                                <!-- Jika ada data -->
                                <tr
                                    v-for="(user, index) in filteredUsers"
                                    :key="user.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{
                                            ((users.current_page ?? 1) - 1) *
                                                (users.per_page ?? 10) +
                                            index +
                                            1
                                        }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-medium text-gray-800">
                                            {{ user.name }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ user.email }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{
                                            user.created_at
                                                ? new Date(user.created_at).toLocaleDateString(
                                                      'id-ID',
                                                      {
                                                          day: '2-digit',
                                                          month: 'long',
                                                          year: 'numeric',
                                                      }
                                                  )
                                                : '-'
                                        }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <span
                                            class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700"
                                        >
                                            Masyarakat
                                        </span>
                                    </td>
                                </tr>

                                <!-- Jika data kosong -->
                                <tr v-if="filteredUsers.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-6 py-10 text-center"
                                    >
                                        <div class="text-sm font-medium text-gray-500">
                                            Tidak ada data masyarakat.
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            {{
                                                search
                                                    ? 'Coba gunakan kata kunci pencarian lain.'
                                                    : 'Belum ada pengguna dengan role masyarakat.'
                                            }}
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="users.links && users.links.length > 3 && !search"
                        class="border-t border-gray-200 px-6 py-4"
                    >
                        <div class="flex flex-wrap items-center justify-center gap-1">
                            <template
                                v-for="(link, index) in users.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    class="rounded-lg px-3 py-2 text-sm transition"
                                    :class="
                                        link.active
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    "
                                >
                                    <span v-html="link.label"></span>
                                </Link>

                                <span
                                    v-else
                                    class="rounded-lg px-3 py-2 text-sm text-gray-400"
                                >
                                    <span v-html="link.label"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>