<template>
    <AuthenticatedLayout>
        <Head title="Detalhes da Encomenda Fornecedor" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/supplier-orders" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Encomenda Fornecedor {{ supplierOrder.number }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fornecedor</h3>
                        <p class="text-lg">{{ supplierOrder.supplier?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Encomenda Cliente</h3>
                        <p class="text-lg">{{ supplierOrder.order?.number || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Data da Encomenda</h3>
                        <p class="text-lg">{{ supplierOrder.order_date ? formatDate(supplierOrder.order_date) : '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="supplierOrder.status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ supplierOrder.status === 'closed' ? 'Fechado' : 'Rascunho' }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Valor Total</h3>
                        <p class="text-lg font-semibold">{{ formatCurrency(calculateTotal(supplierOrder)) }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h2 class="text-lg font-semibold mb-4">Linhas de Artigos</h2>
                    <div v-if="supplierOrder.lines && supplierOrder.lines.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantidade</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preço Unit.</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(line, index) in supplierOrder.lines" :key="line.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ line.product?.reference || '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ line.product?.name || '-' }}</td>
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    supplierOrder: Object,
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

const calculateTotal = (supplierOrder) => {
    if (!supplierOrder.lines || !Array.isArray(supplierOrder.lines)) return 0;
    return supplierOrder.lines.reduce((total, line) => {
        return total + ((line.quantity || 0) * (line.unit_price || 0));
    }, 0);
};
</script>

