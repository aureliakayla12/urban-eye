<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    Settings,
    Info,
    Gift,
    MessageCircle,
    Save,
    CheckCircle,
    LockKeyhole,
    Eye,
    EyeOff,
} from 'lucide-vue-next'

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const showPasswordSuccess = ref(false)

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({
            app_name: 'Urban Eye',
            app_description: 'Platform pelaporan dan pemantauan masalah lingkungan.',
            reward_enabled: true,
            admin_whatsapp: '',
        }),
    },
})

const form = useForm({
    reward_enabled: props.settings.reward_enabled,
    admin_whatsapp: props.settings.admin_whatsapp ?? '',
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submitPassword = () => {
    showPasswordSuccess.value = false

    passwordForm.put(route('admin.settings.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset()
            showPasswordSuccess.value = true

            setTimeout(() => {
                showPasswordSuccess.value = false
            }, 3000)
        },
    })
}

const saved = ref(false)
const editingWhatsapp = ref(false)
const originalWhatsapp = ref(form.admin_whatsapp)

const editWhatsapp = () => {
    originalWhatsapp.value = form.admin_whatsapp
    editingWhatsapp.value = true
    saved.value = false
}

const cancelWhatsapp = () => {
    form.admin_whatsapp = originalWhatsapp.value
    form.clearErrors('admin_whatsapp')
    editingWhatsapp.value = false
}

const submit = () => {
    saved.value = false

    form.put(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            saved.value = true
            originalWhatsapp.value = form.admin_whatsapp
            editingWhatsapp.value = false
        },
    })
}
</script>

<template>
    <Head title="Pengaturan Sistem" />
    <div
        v-if="showPasswordSuccess"
        class="fixed right-5 top-5 z-50 rounded-xl bg-green-700 px-5 py-4 text-sm font-medium text-white shadow-lg"
        role="status"
    >
        Password berhasil diubah!
    </div>

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl space-y-6">
                <!-- Header -->
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700">
                        <Settings class="h-6 w-6" />
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Pengaturan Sistem
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Kelola informasi dan pengaturan aplikasi Urban Eye.
                        </p>
                    </div>
                </div>

                <!-- Informasi Aplikasi -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-lg bg-green-100 p-2 text-green-700">
                            <Info class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Informasi Aplikasi
                            </h2>
                            <p class="text-sm text-gray-500">
                                Informasi dasar mengenai aplikasi.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Nama Aplikasi
                            </label>
                            <input
                                :value="settings.app_name"
                                type="text"
                                readonly
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-gray-600"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Deskripsi
                            </label>
                            <textarea
                                :value="settings.app_description"
                                rows="3"
                                readonly
                                class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-gray-600"
                            />
                        </div>
                    </div>
                </section>

                <!-- Pengaturan Reward -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-lg bg-green-100 p-2 text-green-700">
                            <Gift class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Pengaturan Umum
                            </h2>
                            <p class="text-sm text-gray-500">
                                Atur fitur gamifikasi aplikasi.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-medium text-gray-800">
                                Sistem Reward
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Aktifkan atau nonaktifkan fitur reward untuk pengguna.
                            </p>
                        </div>

                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.reward_enabled"
                            @click="form.reward_enabled = !form.reward_enabled"
                            :class="form.reward_enabled ? 'bg-green-700' : 'bg-gray-300'"
                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                        >
                            <span
                                :class="form.reward_enabled ? 'translate-x-6' : 'translate-x-1'"
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            />
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        Status saat ini:
                        <span
                            :class="form.reward_enabled ? 'text-green-700' : 'text-red-600'"
                            class="font-semibold"
                        >
                            {{ form.reward_enabled ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </p>
                </section>

                <!-- Kontak Admin -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-lg bg-green-100 p-2 text-green-700">
                            <MessageCircle class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Kontak Admin
                            </h2>
                            <p class="text-sm text-gray-500">
                                Kontak yang digunakan petugas untuk menghubungi admin.
                            </p>
                        </div>
                    </div>

                    <div v-if="!editingWhatsapp">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Nomor WhatsApp Admin
                        </label>

                        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-700">
                                <MessageCircle class="h-5 w-5" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-800">
                                    {{ form.admin_whatsapp || 'Belum diatur' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Nomor kontak admin yang tersimpan.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="editWhatsapp"
                                class="shrink-0 rounded-lg border border-green-700 px-4 py-2 text-sm font-semibold text-green-800 transition hover:bg-green-50"
                            >
                                Ubah Nomor
                            </button>
                        </div>
                    </div>

                    <div v-else>
                        <label
                            for="admin_whatsapp"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Nomor WhatsApp Admin
                        </label>

                        <input
                            id="admin_whatsapp"
                            v-model="form.admin_whatsapp"
                            type="tel"
                            maxlength="20"
                            placeholder="Contoh: 6281234567890"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                        />

                        <p class="mt-2 text-xs text-gray-500">
                            Gunakan kode negara 62 tanpa tanda +, spasi, atau tanda hubung.
                        </p>

                        <p
                            v-if="form.errors.admin_whatsapp"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.admin_whatsapp }}
                        </p>

                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <button
                                type="button"
                                @click="cancelWhatsapp"
                                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            >
                                Batal
                            </button>

                            <button
                                type="button"
                                @click="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-green-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900 disabled:opacity-60"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Nomor' }}
                            </button>
                        </div>
                    </div>
                </section>
                
                
                <!-- Ubah Password -->
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-lg bg-green-100 p-2 text-green-700">
                            <LockKeyhole class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">Ubah Password</h2>
                            <p class="text-sm text-gray-500">
                                Perbarui password akun admin untuk menjaga keamanan akun.
                            </p>
                        </div>
                    </div>

                    <form @submit.prevent="submitPassword" class="space-y-4">
                        <!-- Password Saat Ini -->
                        <div>
                            <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Password Saat Ini
                            </label>

                            <div class="relative">
                                <input
                                    id="current_password"
                                    v-model="passwordForm.current_password"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 focus:border-green-600 focus:ring-green-600"
                                />
                                <button
                                    type="button"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-green-700"
                                    :aria-label="showCurrentPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                >
                                    <EyeOff v-if="showCurrentPassword" class="h-5 w-5" />
                                    <Eye v-else class="h-5 w-5" />
                                </button>
                            </div>

                            <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.current_password }}
                            </p>
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Password Baru
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="passwordForm.password"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    autocomplete="new-password"
                                    required
                                    minlength="8"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 focus:border-green-600 focus:ring-green-600"
                                />
                                <button
                                    type="button"
                                    @click="showNewPassword = !showNewPassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-green-700"
                                    :aria-label="showNewPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                >
                                    <EyeOff v-if="showNewPassword" class="h-5 w-5" />
                                    <Eye v-else class="h-5 w-5" />
                                </button>
                            </div>

                            <p
                                v-if="passwordForm.password.length > 0 && passwordForm.password.length < 8"
                                class="mt-1 text-xs text-amber-600"
                            >
                                Password harus minimal 8 karakter.
                            </p>

                            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password }}
                            </p>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Konfirmasi Password Baru
                            </label>

                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    v-model="passwordForm.password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    autocomplete="new-password"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 focus:border-green-600 focus:ring-green-600"
                                />
                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-green-700"
                                    :aria-label="showConfirmPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                >
                                    <EyeOff v-if="showConfirmPassword" class="h-5 w-5" />
                                    <Eye v-else class="h-5 w-5" />
                                </button>
                            </div>

                            <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Tombol Ubah Password -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="rounded-lg bg-green-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-900 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{ passwordForm.processing ? 'Memperbarui...' : 'Ubah Password' }}
                            </button>
                        </div>
                    </form>
                </section>

                <!-- Tombol Simpan -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p
                        v-if="saved"
                        class="flex items-center gap-2 text-sm font-medium text-green-700"
                    >
                        <CheckCircle class="h-4 w-4" />
                        Pengaturan berhasil disimpan.
                    </p>
                    <p
                        v-else-if="form.hasErrors"
                        class="text-sm text-red-600"
                    >
                        Periksa kembali data yang kamu masukkan.
                    </p>
                    <span v-else />

                    <button
                        type="submit"
                        :disabled="passwordForm.processing"
                        class="rounded-lg bg-green-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-900 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ passwordForm.processing ? 'Memperbarui...' : 'Ubah Password' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>