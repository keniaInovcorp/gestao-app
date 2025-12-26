<template>
    <AuthenticatedLayout>
        <Head title="Faturas Fornecedor" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Faturas Fornecedor</h1>
            </div>

            <div v-if="supplierInvoices && supplierInvoices.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encomenda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="supplierInvoices.data.length > 0" v-for="invoice in supplierInvoices.data" :key="invoice.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ invoice.invoice_date ? formatDate(invoice.invoice_date) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ invoice.number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(invoice.supplier?.name || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ invoice.supplier_order?.number || '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button v-if="invoice.document" @click="downloadDocument(invoice.id)" class="text-blue-600 hover:text-blue-900">
                                    Ver PDF
                                </button>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatCurrency(invoice.total_amount) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="invoice.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                      class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ invoice.status === 'paid' ? 'Paga' : 'Pendente de Pagamento' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <Link :href="`/supplier-invoices/${invoice.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                                <button @click="deleteInvoice(invoice.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma fatura encontrada. As faturas são criadas automaticamente ao converter encomendas fechadas.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="supplierInvoices.links && supplierInvoices.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="supplierInvoices.links[0].url" :href="supplierInvoices.links[0].url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </Link>
                            <Link v-if="supplierInvoices.links[supplierInvoices.links.length - 1].url" :href="supplierInvoices.links[supplierInvoices.links.length - 1].url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Seguinte
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ supplierInvoices.from }}</span>
                                    até
                                    <span class="font-medium">{{ supplierInvoices.to }}</span>
                                    de
                                    <span class="font-medium">{{ supplierInvoices.total }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Link v-for="(link, index) in supplierInvoices.links" :key="index" :href="link.url || '#'" v-html="link.label"
                                          :class="[
                                              'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                                              link.active
                                                  ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                              index === 0 ? 'rounded-l-md' : '',
                                              index === supplierInvoices.links.length - 1 ? 'rounded-r-md' : '',
                                              !link.url ? 'cursor-not-allowed opacity-50' : ''
                                          ]">
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
    supplierInvoices: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('pt-PT');
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

const downloadDocument = (id) => {
    window.location.href = `/supplier-invoices/${id}/download-document`;
};

const deleteInvoice = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta fatura?')) {
        router.delete(`/supplier-invoices/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Success handled by Inertia
            },
        });
    }
};
</script>

