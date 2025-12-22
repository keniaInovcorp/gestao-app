<template>
    <Head title="Dashboard" />
    <div class="min-h-screen bg-gray-100">
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestão App</h1>
                        <p class="text-sm text-gray-600">Sistema de Gestão Empresarial</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">{{ $page.props.auth.user?.name }}</span>
                        <form @submit.prevent="logout" method="post" action="/logout">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                            >
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Bem-vindo ao Dashboard</h2>
                <p class="text-gray-600">
                    Login realizado com sucesso! Esta é a página principal da aplicação.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';

const logout = () => {
    router.post('/logout');
};
</script>

