<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Bem-vindo ao Dashboard</h2>
                <p class="text-gray-600">
                    Login realizado com sucesso! Esta é a página principal da aplicação.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

