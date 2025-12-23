<template>
    <div class="min-h-screen bg-gray-100">
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestão App</h1>
                        <p class="text-sm text-gray-600">Sistema de Gestão Empresarial</p>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="hidden md:flex items-center space-x-4">
                            <Link
                                href="/dashboard"
                                class="px-3 py-2 rounded-md text-sm font-medium transition"
                                :class="$page.url === '/dashboard' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                            >
                                Dashboard
                            </Link>
                            <Link
                                href="/clients"
                                class="px-3 py-2 rounded-md text-sm font-medium transition"
                                :class="$page.url.startsWith('/clients') || $page.url.startsWith('/entities') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                            >
                                Clientes
                            </Link>
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md text-sm font-medium transition flex items-center"
                                    :class="$page.url.startsWith('/countries') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'">
                                    Configurações
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <Link 
                                        href="/countries" 
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        :class="$page.url.startsWith('/countries') ? 'bg-gray-50' : ''"
                                    >
                                        Países
                                    </Link>
                                </div>
                            </div>
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
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const logout = () => {
    router.post('/logout');
};
</script>

