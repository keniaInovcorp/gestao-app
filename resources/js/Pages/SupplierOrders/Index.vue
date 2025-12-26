<template>
    <AuthenticatedLayout>
        <Head title="Encomendas Fornecedor" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Encomendas Fornecedor</h1>
            </div>

            <div v-if="supplierOrders && supplierOrders.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encomenda Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="supplierOrders.data.length > 0" v-for="supplierOrder in supplierOrders.data" :key="supplierOrder.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ supplierOrder.order_date ? formatDate(supplierOrder.order_date) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ supplierOrder.number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(supplierOrder.supplier?.name || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ supplierOrder.order?.number || '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatCurrency(calculateTotal(supplierOrder)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="supplierOrder.status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                                      class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ supplierOrder.status === 'closed' ? 'Fechado' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <Link :href="`/supplier-orders/${supplierOrder.id}`" class="text-blue-600 hover:text-blue-900">Ver</Link>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma encomenda fornecedor encontrada.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="supplierOrders.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="supplierOrders.prev_page_url" :href="supplierOrders.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </Link>
                            <Link v-if="supplierOrders.next_page_url" :href="supplierOrders.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Próximo
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium">{{ supplierOrders.from }}</span> até <span class="font-medium">{{ supplierOrders.to }}</span> de <span class="font-medium">{{ supplierOrders.total }}</span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Link v-if="supplierOrders.prev_page_url" :href="supplierOrders.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Anterior
                                    </Link>
                                    <Link v-if="supplierOrders.next_page_url" :href="supplierOrders.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Próximo
                                    </Link>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500">Carregando...</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { truncate } from '@/utils/truncate';

const props = defineProps({
    supplierOrders: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const formatDate = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString('pt-PT');
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
};

const calculateTotal = (supplierOrder) => {
    if (!supplierOrder.lines || !Array.isArray(supplierOrder.lines)) return 0;
    return supplierOrder.lines.reduce((total, line) => {
        return total + ((line.quantity || 0) * (line.unit_price || 0));
    }, 0);
};
</script>

