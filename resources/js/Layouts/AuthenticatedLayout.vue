<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <Link href="/dashboard" class="text-xl font-bold text-gray-900">
                            Gestão App
                        </Link>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-1">
                        <Link
                            href="/dashboard"
                            class="px-3 py-2 rounded-md text-sm font-medium"
                            :class="$page.url === '/dashboard' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/clientes"
                            class="px-3 py-2 rounded-md text-sm font-medium"
                            :class="$page.url.startsWith('/clientes') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        >
                            Clientes
                        </Link>
                        <Link
                            href="/fornecedores"
                            class="px-3 py-2 rounded-md text-sm font-medium"
                            :class="$page.url.startsWith('/fornecedores') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        >
                            Fornecedores
                        </Link>
                        <Link
                            href="/contacts"
                            class="px-3 py-2 rounded-md text-sm font-medium"
                            :class="$page.url.startsWith('/contacts') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        >
                            Contactos
                        </Link>
                        <div class="relative group">
                            <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 flex items-center">
                                Configurações
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <Link href="/countries" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm"
                            >
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main>
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

