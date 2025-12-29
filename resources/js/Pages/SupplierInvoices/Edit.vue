<template>
    <AuthenticatedLayout>
        <Head title="Editar Fatura Fornecedor" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/supplier-invoices" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Editar Fatura Fornecedor</h1>
            </div>

                <FlashMessages />

                <div v-if="!supplierInvoice.document" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <p class="text-yellow-800">
                        <strong>Atenção:</strong> Esta fatura ainda não tem documento. Apenas faturas com documento podem ser editadas.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow p-6" v-if="supplierInvoice.document">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">Informações da Fatura</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Número</label>
                            <p class="text-base font-semibold">{{ supplierInvoice.number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fornecedor</label>
                            <p class="text-base">{{ supplierInvoice.supplier?.name || '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Encomenda Fornecedor</label>
                            <p class="text-base">{{ supplierInvoice.supplier_order?.number || '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Valor Total</label>
                            <p class="text-base font-semibold">{{ formatCurrency(supplierInvoice.total_amount) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Data da Fatura</label>
                            <p class="text-base">{{ formatDate(supplierInvoice.invoice_date) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Data de Vencimento</label>
                            <p class="text-base">{{ formatDate(supplierInvoice.due_date) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Documento</label>
                            <button v-if="supplierInvoice.document" type="button" @click="downloadDocument" class="text-blue-600 hover:text-blue-900 underline">
                                Download PDF
                            </button>
                            <span v-else class="text-gray-400">-</span>
                        </div>
                    </div>
                </div>

                    <form v-if="supplierInvoice.document" @submit.prevent="onSubmit" enctype="multipart/form-data" ref="formElement">
                        <div class="border-t pt-6">
                            <h2 class="text-lg font-semibold mb-4">Editar Pagamento</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                        <div>
                            <label class="block text-sm font-medium mb-2">Estado</label>
                            <Select :model-value="form.status" @update:model-value="handleStatusChange">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecione o estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pending">Pendente de Pagamento</SelectItem>
                                    <SelectItem value="paid">Paga</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Comprovativo de Pagamento</label>
                            <Input
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                @change="handlePaymentProofChange"
                            />
                            <p v-if="supplierInvoice.payment_proof" class="text-sm text-gray-500 mt-1">
                                Comprovativo atual: <button type="button" @click="downloadPaymentProof" class="text-blue-600 hover:text-blue-900 underline">Download</button>
                            </p>
                        </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <Link href="/supplier-invoices" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'A guardar...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { watch, ref } from 'vue';

const props = defineProps({
    supplierInvoice: {
        type: Object,
        required: true
    },
});

const originalStatus = ref(props.supplierInvoice.status);
const showPaymentProofAlert = ref(false);

const form = useForm({
    status: props.supplierInvoice.status || 'pending',
    payment_proof: null,
    send_payment_proof: '0',
});

watch(() => props.supplierInvoice.status, (newStatus) => {
    if (!form.processing && form.status !== newStatus) {
        form.status = newStatus || 'pending';
        originalStatus.value = newStatus;
    }
}, { immediate: true });

const handleStatusChange = (value) => {
    if (value === 'paid' && !(form.payment_proof instanceof File) && !props.supplierInvoice.payment_proof) {
        alert('Para marcar a fatura como "Paga", é necessário fazer upload do comprovativo de pagamento.');
        return;
    }
    
    form.status = value;
    
    if (originalStatus.value === 'pending' && value === 'paid') {
        const shouldSend = confirm('Pretende enviar o comprovativo ao Fornecedor quando guardar?\n\nNota: É necessário anexar o comprovativo de pagamento antes de guardar.');
        form.send_payment_proof = shouldSend ? '1' : '0';
    } else {
        form.send_payment_proof = '0';
    }
};

const handlePaymentProofChange = (event) => {
    const file = event.target.files[0] || null;
    if (file) {
        // Validate file type
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const fileExtension = file.name.split('.').pop()?.toLowerCase();
        
        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
            alert('Tipo de ficheiro não permitido. Apenas PDF, JPG e PNG são permitidos.');
            event.target.value = '';
            form.payment_proof = null;
            return;
        }
        
        // Validate file size (10MB = 10485760 bytes)
        if (file.size > 10485760) {
            alert('O ficheiro é demasiado grande. O tamanho máximo permitido é 10MB.');
            event.target.value = '';
            form.payment_proof = null;
            return;
        }
        
        // Ensure file is valid
        if (file.size === 0) {
            alert('O ficheiro está vazio. Por favor, selecione um ficheiro válido.');
            event.target.value = '';
            form.payment_proof = null;
            return;
        }
        
        form.payment_proof = file;
    } else {
        form.payment_proof = null;
    }
};

const downloadDocument = () => {
    window.location.href = `/supplier-invoices/${props.supplierInvoice.id}/download-document`;
};

const downloadPaymentProof = () => {
    window.location.href = `/supplier-invoices/${props.supplierInvoice.id}/download-payment-proof`;
};

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

const onSubmit = () => {
    if (!props.supplierInvoice.document) {
        alert('Esta fatura ainda não tem documento. Apenas faturas com documento podem ser editadas.');
        return;
    }
    
    if (!form.status) {
        alert('Por favor, selecione o estado');
        return;
    }
    
    if (form.status === 'paid' && !(form.payment_proof instanceof File) && !props.supplierInvoice.payment_proof) {
        alert('Para marcar a fatura como "Paga", é necessário fazer upload do comprovativo de pagamento.');
        return;
    }
    
    if (form.payment_proof instanceof File) {
        if (!form.payment_proof || form.payment_proof.size === 0) {
            alert('O ficheiro selecionado está vazio. Por favor, selecione um ficheiro válido.');
            return;
        }
        
        const formData = new FormData();
        formData.append('status', form.status);
        formData.append('send_payment_proof', form.send_payment_proof || '0');
        formData.append('payment_proof', form.payment_proof, form.payment_proof.name);
        formData.append('_method', 'PUT');
        
        router.post(`/supplier-invoices/${props.supplierInvoice.id}`, formData, {
            forceFormData: true,
            preserveScroll: true,
            onError: (errors) => {
                let errorMessage = 'Erro ao guardar a fatura. ';
                if (errors.payment_proof) {
                    errorMessage += errors.payment_proof;
                } else if (errors.status) {
                    errorMessage += errors.status;
                } else {
                    errorMessage += 'Por favor, verifique os dados e tente novamente.';
                }
                alert(errorMessage);
            },
        });
    } else {
        form.transform((data) => ({
            status: data.status,
            send_payment_proof: data.send_payment_proof || '0',
        })).put(`/supplier-invoices/${props.supplierInvoice.id}`, {
            preserveScroll: true,
            onError: (errors) => {
                let errorMessage = 'Erro ao guardar a fatura. ';
                if (errors.payment_proof) {
                    errorMessage += errors.payment_proof;
                } else if (errors.status) {
                    errorMessage += errors.status;
                } else {
                    errorMessage += 'Por favor, verifique os dados e tente novamente.';
                }
                alert(errorMessage);
            },
        });
    }
};
</script>

