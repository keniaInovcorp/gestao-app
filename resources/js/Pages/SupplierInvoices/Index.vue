<template>
    <AuthenticatedLayout>
        <Head title="Faturas Fornecedor" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Faturas Fornecedor</h1>
                <Link 
                    v-if="canCreate('supplier-invoices')"
                    href="/supplier-invoices/create" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Adicionar Fatura
                </Link>
            </div>

            <FlashMessages />

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
                                <button 
                                    v-if="canRead('supplier-invoices') && invoice.document" 
                                    @click="downloadDocument(invoice.id)" 
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Ver PDF"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <Link 
                                        v-if="canRead('supplier-invoices')"
                                        :href="`/supplier-invoices/${invoice.id}`" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="Detalhes"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <Link 
                                        v-if="canUpdate('supplier-invoices')"
                                        :href="`/supplier-invoices/${invoice.id}/edit`" 
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <button
                                        v-if="canDelete('supplier-invoices')"
                                        @click="deleteInvoice(invoice)"
                                        class="text-red-600 hover:text-red-900"
                                        title="Eliminar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
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
import FlashMessages from '@/components/FlashMessages.vue';
import { truncate } from '@/utils/truncate';
import { usePermissions } from '@/composables/usePermissions';

const props = defineProps({
    supplierInvoices: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const { canCreate, canRead, canUpdate, canDelete } = usePermissions();

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

const deleteInvoice = (invoice) => {
    if (confirm(`Tem a certeza que deseja eliminar a fatura "${invoice.number}"?`)) {
        router.delete(`/supplier-invoices/${invoice.id}`, {
            preserveScroll: true,
        });
    }
};
</script>
