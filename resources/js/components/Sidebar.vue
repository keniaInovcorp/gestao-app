<template>
    <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0"
           :class="{ '-translate-x-full lg:translate-x-0': !isOpen, 'translate-x-0': isOpen }">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Gestão App</h1>
                    <p class="text-xs text-gray-500">Sistema de Gestão</p>
                </div>
                <button
                    @click="toggleSidebar"
                    class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3">
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <Link
                            href="/dashboard"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/dashboard') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/clients"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/clients') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Clientes
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/suppliers"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/suppliers') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Fornecedores
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/contacts"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/contacts') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Contactos
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/quotations"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/quotations') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Propostas
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/orders"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/orders') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Encomendas
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/supplier-orders"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
                            :class="isActive('/supplier-orders') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Encomendas Fornecedor
                        </Link>
                    </li>

                    <li class="pt-4">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Financeiro
                        </div>
                    </li>

                    <li>
                        <Link
                            href="/supplier-invoices"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ml-2"
                            :class="isActive('/supplier-invoices') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Faturas Fornecedor
                        </Link>
                    </li>

                    <li class="pt-4">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Configurações
                        </div>
                    </li>

                    <li>
                        <Link
                            href="/countries"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ml-2"
                            :class="isActive('/countries') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Países
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/contact-functions"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ml-2"
                            :class="isActive('/contact-functions') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Funções de Contacto
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/vat-rates"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ml-2"
                            :class="isActive('/vat-rates') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Taxas de IVA
                        </Link>
                    </li>

                    <li>
                        <Link
                            href="/products"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ml-2"
                            :class="isActive('/products') 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-700 hover:bg-gray-100'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Artigos
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div
        v-if="isOpen"
        @click="toggleSidebar"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
    ></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const isOpen = ref(false);

const isActive = (path) => {
    return page.url.startsWith(path);
};

const toggleSidebar = () => {
    isOpen.value = !isOpen.value;
};

const handleResize = () => {
    if (window.innerWidth >= 1024) {
        isOpen.value = true; 
    } else {
        isOpen.value = false; 
    }
};

onMounted(() => {
    if (window.innerWidth >= 1024) {
        isOpen.value = true;
    }
    window.addEventListener('resize', handleResize);
    handleResize();
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

defineExpose({
    toggleSidebar
});
</script>

