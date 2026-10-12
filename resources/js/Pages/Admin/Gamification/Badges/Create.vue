<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Award, Save } from 'lucide-vue-next'

const form = useForm({
    name: '',
    description: '',
    required_points: 0,
    required_reports: 0,
    icon: 'award',
})

const submit = () => {
    form.post(route('admin.gamification.badges.store'))
}
</script>

<template>
    <Head title="Tambah Badge" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl space-y-6">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.gamification.badges')"
                        class="rounded-lg border border-gray-200 bg-white p-2 text-gray-600 transition hover:bg-gray-100"
                    >
                        <ArrowLeft class="h-5 w-5" />
                    </Link>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Tambah Badge
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Buat badge baru untuk pengguna Urban Eye.
                        </p>
                    </div>
                </div>

                <form
                    @submit.prevent="submit"
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >
                    <div class="flex items-center gap-3 border-b border-gray-100 p-6">
                        <div class="rounded-lg bg-green-100 p-2 text-green-700">
                            <Award class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Informasi Badge
                            </h2>
                            <p class="text-sm text-gray-500">
                                Isi informasi dan persyaratan badge.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-5 p-6">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                                Nama Badge <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Pelapor Aktif"
                                required
                                maxlength="255"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                                Deskripsi
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Jelaskan cara pengguna mendapatkan badge ini..."
                                class="w-full resize-y rounded-lg border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="required_points" class="mb-2 block text-sm font-medium text-gray-700">
                                    Poin yang Dibutuhkan
                                </label>
                                <input
                                    id="required_points"
                                    v-model.number="form.required_points"
                                    type="number"
                                    min="0"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                                />
                                <p v-if="form.errors.required_points" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.required_points }}
                                </p>
                            </div>

                            <div>
                                <label for="required_reports" class="mb-2 block text-sm font-medium text-gray-700">
                                    Laporan yang Dibutuhkan
                                </label>
                                <input
                                    id="required_reports"
                                    v-model.number="form.required_reports"
                                    type="number"
                                    min="0"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                                />
                                <p v-if="form.errors.required_reports" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.required_reports }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="icon" class="mb-2 block text-sm font-medium text-gray-700">
                                Nama Ikon
                            </label>
                            <input
                                id="icon"
                                v-model="form.icon"
                                type="text"
                                placeholder="Contoh: award, leaf, shield-check"
                                maxlength="255"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Isi nama ikon yang akan digunakan oleh halaman badge.
                            </p>
                            <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">
                                {{ form.errors.icon }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                        <Link
                            :href="route('admin.gamification.badges')"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-100"
                        >
                            Batal
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <Save class="h-4 w-4" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Badge' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>