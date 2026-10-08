<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ArrowLeft, Save, UserRoundCog } from 'lucide-vue-next'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
})

function submit() {
    form.put(
        route('admin.users.petugas.update', props.user.id)
    )
}
</script>

<template>
    <Head title="Edit Petugas" />

    <AuthenticatedLayout>
        <!-- Header di samping garis tiga -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Edit Petugas
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Perbarui data petugas yang terdaftar di UrbanEye.
                    </p>
                </div>
            </div>
        </template>

        <!-- Isi halaman -->
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Card utama -->
                <div class="max-w-2xl overflow-hidden rounded-xl bg-white shadow-sm">

                    <!-- Header card -->
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100"
                            >
                                <UserRoundCog class="h-5 w-5 text-green-700" />
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Data Petugas
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Perbarui informasi petugas di bawah ini.
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Form -->
                    <div class="px-6 py-6">
                        <form
                            @submit.prevent="submit"
                            class="space-y-5"
                        >

                            <!-- Nama -->
                            <div>
                                <label
                                    for="name"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Nama
                                </label>

                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Masukkan nama petugas"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                />

                                <p
                                    v-if="form.errors.name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Email
                                </label>

                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="Masukkan email petugas"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-200"
                                />

                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Tombol -->
                            <div class="flex justify-end gap-3 border-t border-gray-200 pt-5">

                                <Link
                                    :href="route('admin.users.petugas.index')"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                >
                                    <ArrowLeft class="h-4 w-4" />
                                    Kembali
                                </Link>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition disabled:cursor-not-allowed disabled:opacity-50"
                                    style="background-color: #15803d;"
                                >
                                    <Save class="h-4 w-4" />

                                    {{
                                        form.processing
                                            ? 'Menyimpan...'
                                            : 'Simpan Perubahan'
                                    }}
                                </button>

                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>