<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <Sidebar ref="sidebarRef" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Navbar -->
            <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <button
                            @click="toggleSidebar"
                            class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100 transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="flex-1"></div>

                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700 hidden sm:block">
                                {{ $page.props.auth.user?.name }}
                            </span>
                            <form @submit.prevent="logout" method="post" action="/logout">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm font-medium"
                                >
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Sidebar from '@/components/Sidebar.vue';

const sidebarRef = ref(null);

const toggleSidebar = () => {
    if (sidebarRef.value) {
        sidebarRef.value.toggleSidebar();
    }
};

const logout = () => {
    router.post('/logout');
};
</script>

