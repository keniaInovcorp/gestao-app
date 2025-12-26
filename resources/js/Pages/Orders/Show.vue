<template>
    <AuthenticatedLayout>
        <Head title="Detalhes da Encomenda" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/orders" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Encomenda {{ order.number }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Cliente</h3>
                        <p class="text-lg">{{ order.client?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Data da Encomenda</h3>
                        <p class="text-lg">{{ order.order_date ? formatDate(order.order_date) : '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="order.status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ order.status === 'closed' ? 'Fechado' : 'Rascunho' }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Valor Total</h3>
                        <p class="text-lg font-semibold">{{ formatCurrency(calculateTotal(order)) }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h2 class="text-lg font-semibold mb-4">Linhas de Artigos</h2>
                    <div v-if="order.lines && order.lines.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantidade</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preço Unit.</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(line, index) in order.lines" :key="line.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ line.product?.reference || '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ line.product?.name || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ line.supplier?.name || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ formatNumber(line.quantity) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ formatCurrency(line.unit_price) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ formatCurrency(line.quantity * line.unit_price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        Nenhuma linha encontrada.
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <Link :href="`/orders/${order.id}/edit`" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Editar
                    </Link>
                    <button @click="downloadPdf" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Download PDF
                    </button>
                    <button v-if="order.status === 'closed'" @click="convertToSupplierOrder" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        Converter para Encomendas Fornecedor
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    order: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString('pt-PT');
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

const calculateTotal = (order) => {
    if (!order.lines || !Array.isArray(order.lines)) return 0;
    return order.lines.reduce((total, line) => {
        const subtotal = (line.quantity || 0) * (line.unit_price || 0);
        const vatRate = line.product?.vat_rate?.percentage || line.product?.vatRate?.percentage || 0;
        return total + (subtotal * (1 + (vatRate / 100)));
    }, 0);
};

const downloadPdf = () => {
    window.open(`/orders/${props.order.id}/pdf`, '_blank');
};

const convertToSupplierOrder = () => {
    if (confirm('Pretende converter esta encomenda em encomendas fornecedor?')) {
        router.post(`/orders/${props.order.id}/convert-to-supplier-order`, {}, {
            onSuccess: () => {
                alert('Encomenda convertida em encomendas fornecedor com sucesso');
            }
        });
    }
};
</script>

