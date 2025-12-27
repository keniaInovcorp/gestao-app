<template>
    <AuthenticatedLayout>
        <Head title="Logs" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Logs</h1>
            </div>

            <FlashMessages />

            <div v-if="logs && logs.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilizador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Menu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispositivo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="logs.data.length > 0" v-for="log in logs.data" :key="log.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.user }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.menu }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.action }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.device }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ log.ip_address }}</td>
                        </tr>
                        <tr v-else>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum log encontrado
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="logs.links && logs.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link
                                v-if="logs.prev_page_url"
                                :href="logs.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Anterior
                            </Link>
                            <Link
                                v-if="logs.next_page_url"
                                :href="logs.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Próximo
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ logs.from || 0 }}</span>
                                    até
                                    <span class="font-medium">{{ logs.to || 0 }}</span>
                                    de
                                    <span class="font-medium">{{ logs.total || 0 }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Link
                                        v-for="link in logs.links"
                                        :key="link.label"
                                        :href="link.url || '#'"
                                        v-html="link.label"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                                        :class="{
                                            'z-10 bg-blue-50 border-blue-500 text-blue-600': link.active,
                                            'pointer-events-none opacity-50': !link.url
                                        }"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    logs: {
        type: Object,
        required: true,
    },
});
</script>

