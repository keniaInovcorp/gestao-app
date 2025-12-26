<template>
    <AuthenticatedLayout>
        <Head title="Detalhes da Fatura Fornecedor" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/supplier-invoices" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Detalhes da Fatura Fornecedor</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Número</h3>
                        <p class="text-lg font-semibold">{{ supplierInvoice.number }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="supplierInvoice.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                              class="px-2 py-1 text-xs font-semibold rounded-full">
                            {{ supplierInvoice.status === 'paid' ? 'Paga' : 'Pendente de Pagamento' }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fornecedor</h3>
                        <p class="text-base">{{ supplierInvoice.supplier?.name || '-' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Encomenda Fornecedor</h3>
                        <p class="text-base">{{ supplierInvoice.supplier_order?.number || '-' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Data da Fatura</h3>
                        <p class="text-base">{{ formatDate(supplierInvoice.invoice_date) }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Data de Vencimento</h3>
                        <p class="text-base">{{ formatDate(supplierInvoice.due_date) }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Valor Total</h3>
                        <p class="text-lg font-semibold">{{ formatCurrency(supplierInvoice.total_amount) }}</p>
                    </div>

                    <div v-if="supplierInvoice.document">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Documento</h3>
                        <button @click="downloadDocument" class="text-blue-600 hover:text-blue-900">
                            Download
                        </button>
                    </div>

                    <div v-if="supplierInvoice.payment_proof">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Comprovativo de Pagamento</h3>
                        <button @click="downloadPaymentProof" class="text-blue-600 hover:text-blue-900">
                            Download
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <Link :href="`/supplier-invoices/${supplierInvoice.id}/edit`" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Editar
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    supplierInvoice: {
        type: Object,
        required: true
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

const downloadDocument = () => {
    window.location.href = `/supplier-invoices/${props.supplierInvoice.id}/download-document`;
};

const downloadPaymentProof = () => {
    window.location.href = `/supplier-invoices/${props.supplierInvoice.id}/download-payment-proof`;
};
</script>

