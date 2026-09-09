<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    stats: Object
});

console.log('Props yang diterima:', props);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Test data -->
                        <div class="mb-4 p-4 bg-green-100 rounded">
                            <h3 class="text-lg font-bold text-green-800">✅ Dashboard Berhasil!</h3>
                            <p class="mt-2">Nama: <strong>{{ user?.name }}</strong></p>
                            <p>Role: <strong>{{ user?.role }}</strong></p>
                            <p>Test: <strong>{{ stats?.test }}</strong></p>
                            <p>Total Users: <strong>{{ stats?.total_users }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>