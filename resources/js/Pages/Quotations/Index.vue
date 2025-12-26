<template>
    <AuthenticatedLayout>
        <Head title="Propostas" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Propostas</h1>
                <Link href="/quotations/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Adicionar Proposta
                </Link>
            </div>

            <div v-if="quotations && quotations.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Validade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="quotations.data.length > 0" v-for="quotation in quotations.data" :key="quotation.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ quotation.quotation_date ? formatDate(quotation.quotation_date) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ quotation.number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatDate(quotation.validity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(quotation.client?.name || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatCurrency(calculateTotal(quotation)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="quotation.status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                                      class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ quotation.status === 'closed' ? 'Fechado' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <Link :href="`/quotations/${quotation.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                                <button @click="downloadPdf(quotation.id)" class="text-green-600 hover:text-green-900">PDF</button>
                                <button v-if="quotation.status === 'closed'" @click="convertToOrder(quotation.id)" class="text-purple-600 hover:text-purple-900">Converter</button>
                                <button @click="deleteQuotation(quotation.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma proposta encontrada. <Link href="/quotations/create" class="text-blue-600 hover:text-blue-900">Criar a primeira proposta</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="quotations.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="quotations.prev_page_url" :href="quotations.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </Link>
                            <Link v-if="quotations.next_page_url" :href="quotations.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Próximo
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium">{{ quotations.from }}</span> até <span class="font-medium">{{ quotations.to }}</span> de <span class="font-medium">{{ quotations.total }}</span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Link v-if="quotations.prev_page_url" :href="quotations.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Anterior
                                    </Link>
                                    <Link v-if="quotations.next_page_url" :href="quotations.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
    quotations: {
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

const calculateTotal = (quotation) => {
    if (!quotation.lines || !Array.isArray(quotation.lines)) return 0;
    return quotation.lines.reduce((total, line) => {
        const subtotal = (line.quantity || 0) * (line.unit_price || 0);
        const vatRate = line.product?.vat_rate?.percentage || line.product?.vatRate?.percentage || 0;
        return total + (subtotal * (1 + (vatRate / 100)));
    }, 0);
};

const downloadPdf = (id) => {
    window.open(`/quotations/${id}/pdf`, '_blank');
};

const convertToOrder = (id) => {
    if (confirm('Pretende converter esta proposta em encomenda?')) {
        router.post(`/quotations/${id}/convert-to-order`, {}, {
            onSuccess: () => {
                alert('Proposta convertida em encomenda com sucesso');
            }
        });
    }
};

const deleteQuotation = (id) => {
    if (confirm('Tem a certeza que pretende eliminar esta proposta?')) {
        router.delete(`/quotations/${id}`, {
            onSuccess: () => {
                alert('Proposta eliminada com sucesso');
            }
        });
    }
};
</script>

