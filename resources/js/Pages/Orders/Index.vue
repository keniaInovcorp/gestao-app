<template>
    <AuthenticatedLayout>
        <Head title="Encomendas" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Encomendas</h1>
                <Link href="/orders/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Adicionar Encomenda
                </Link>
            </div>

            <div v-if="orders && orders.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="orders.data.length > 0" v-for="order in orders.data" :key="order.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ order.order_date ? formatDate(order.order_date) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ order.number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(order.client?.name || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatCurrency(calculateTotal(order)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="order.status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                                      class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ order.status === 'closed' ? 'Fechado' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <Link :href="`/orders/${order.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                                <button @click="downloadPdf(order.id)" class="text-green-600 hover:text-green-900">PDF</button>
                                <button v-if="order.status === 'closed'" @click="convertToSupplierOrder(order.id)" class="text-purple-600 hover:text-purple-900">Converter</button>
                                <button @click="deleteOrder(order.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma encomenda encontrada. <Link href="/orders/create" class="text-blue-600 hover:text-blue-900">Criar a primeira encomenda</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="orders.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </Link>
                            <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Próximo
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium">{{ orders.from }}</span> até <span class="font-medium">{{ orders.to }}</span> de <span class="font-medium">{{ orders.total }}</span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Anterior
                                    </Link>
                                    <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { truncate } from '@/utils/truncate';

const props = defineProps({
    orders: {
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

const calculateTotal = (order) => {
    if (!order.lines || !Array.isArray(order.lines)) return 0;
    return order.lines.reduce((total, line) => {
        const subtotal = (line.quantity || 0) * (line.unit_price || 0);
        const vatRate = line.product?.vat_rate?.percentage || line.product?.vatRate?.percentage || 0;
        return total + (subtotal * (1 + (vatRate / 100)));
    }, 0);
};

const downloadPdf = (id) => {
    window.open(`/orders/${id}/pdf`, '_blank');
};

const convertToSupplierOrder = (id) => {
    if (confirm('Pretende converter esta encomenda em encomendas fornecedor?')) {
        router.post(`/orders/${id}/convert-to-supplier-order`, {}, {
            onSuccess: () => {
                alert('Encomenda convertida em encomendas fornecedor com sucesso');
            }
        });
    }
};

const deleteOrder = (id) => {
    if (confirm('Tem a certeza que pretende eliminar esta encomenda?')) {
        router.delete(`/orders/${id}`, {
            onSuccess: () => {
                alert('Encomenda eliminada com sucesso');
            }
        });
    }
};
</script>

