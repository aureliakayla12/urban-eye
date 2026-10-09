<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Gift } from 'lucide-vue-next'

const form = useForm({
    name: '',
    reward_type: '',
    description: '',
    point_cost: '',
    stock: 0,
    image: '',
    status: true,
})

const submit = () => {
    form.post(route('admin.gamification.rewards.store'))
}
</script>

<template>
    <Head title="Tambah Reward" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Tambah Reward
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan reward yang dapat ditukarkan menggunakan poin.
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">

                <div class="rounded-xl bg-white shadow-sm">

                    <!-- Header Form -->
                    <div class="border-b px-6 py-5">
                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100 text-green-600"
                            >
                                <Gift class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    Informasi Reward
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Isi informasi reward dengan lengkap.
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Form -->
                    <form
                        @submit.prevent="submit"
                        class="space-y-6 p-6"
                    >

                        <!-- Nama -->
                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Nama Reward
                            </label>

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Voucher Belanja Rp50.000"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            />

                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Jenis -->
                        <div>
                            <label
                                for="reward_type"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Jenis Reward
                            </label>

                            <select
                                id="reward_type"
                                v-model="form.reward_type"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            >
                                <option value="" disabled>
                                    Pilih jenis reward
                                </option>

                                <option value="voucher">
                                    Voucher
                                </option>

                                <option value="pulsa">
                                    Pulsa
                                </option>

                                <option value="bibit">
                                    Bibit Tanaman
                                </option>

                                <option value="lainnya">
                                    Lainnya
                                </option>
                            </select>

                            <p
                                v-if="form.errors.reward_type"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.reward_type }}
                            </p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label
                                for="description"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Deskripsi
                            </label>

                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Jelaskan reward yang akan diberikan..."
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            ></textarea>

                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Poin & Stok -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                            <div>
                                <label
                                    for="point_cost"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Poin yang Dibutuhkan
                                </label>

                                <input
                                    id="point_cost"
                                    v-model="form.point_cost"
                                    type="number"
                                    min="1"
                                    placeholder="Contoh: 500"
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                />

                                <p
                                    v-if="form.errors.point_cost"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.point_cost }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="stock"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Stok
                                </label>

                                <input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    placeholder="Contoh: 10"
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                />

                                <p
                                    v-if="form.errors.stock"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.stock }}
                                </p>
                            </div>

                        </div>

                        <!-- Image -->
                        <div>
                            <label
                                for="image"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Gambar Reward
                                <span class="font-normal text-gray-400">
                                    (opsional)
                                </span>
                            </label>

                            <input
                                id="image"
                                v-model="form.image"
                                type="text"
                                placeholder="Nama/path gambar jika diperlukan"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                            />

                            <p
                                v-if="form.errors.image"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.image }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Status
                            </label>

                            <label class="inline-flex cursor-pointer items-center gap-3">
                                <input
                                    v-model="form.status"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                />

                                <span class="text-sm text-gray-600">
                                    Reward aktif dan dapat ditukarkan
                                </span>
                            </label>

                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 border-t pt-6">

                            <Link
                                :href="route('admin.gamification.rewards')"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                Kembali
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-green-600 px-5 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Reward' }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>