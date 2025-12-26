<template>
    <AuthenticatedLayout>
        <Head title="Criar Fatura Fornecedor" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/supplier-invoices" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Criar Fatura Fornecedor</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="supplier_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Fornecedor</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o fornecedor" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id.toString()">
                                                {{ supplier.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="supplier_order_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Encomenda Fornecedor</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione a encomenda (opcional)" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Nenhuma</SelectItem>
                                            <SelectItem v-for="order in supplierOrders" :key="order.id" :value="order.id.toString()">
                                                {{ order.number }} - {{ order.supplier?.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="invoice_date">
                            <FormItem class="space-y-2">
                                <FormLabel>Data da Fatura</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="date"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="due_date">
                            <FormItem class="space-y-2">
                                <FormLabel>Data de Vencimento</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="date"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="total_amount">
                            <FormItem class="space-y-2">
                                <FormLabel>Valor Total</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="status">
                            <FormItem class="space-y-2">
                                <FormLabel>Estado</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o estado" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">Pendente de Pagamento</SelectItem>
                                            <SelectItem value="paid">Paga</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="document">
                            <FormItem class="space-y-2">
                                <FormLabel>Documento</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        @change="handleDocumentChange"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="payment_proof">
                            <FormItem class="space-y-2">
                                <FormLabel>Comprovativo de Pagamento</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        @change="handlePaymentProofChange"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <Link href="/supplier-invoices" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm as useVeeForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';

const props = defineProps({
    suppliers: {
        type: Array,
        default: () => []
    },
    supplierOrders: {
        type: Array,
        default: () => []
    },
});

const formSchema = toTypedSchema(z.object({
    supplier_id: z.string().min(1, 'O fornecedor é obrigatório'),
    supplier_order_id: z.string().optional(),
    invoice_date: z.string().min(1, 'A data da fatura é obrigatória'),
    due_date: z.string().min(1, 'A data de vencimento é obrigatória'),
    total_amount: z.string().min(1, 'O valor total é obrigatório'),
    document: z.any().optional(),
    payment_proof: z.any().optional(),
    status: z.enum(['pending', 'paid'], {
        required_error: 'O estado é obrigatório',
    }),
}));

const form = useForm({
    supplier_id: '',
    supplier_order_id: '',
    invoice_date: '',
    due_date: '',
    total_amount: '',
    document: null,
    payment_proof: null,
    status: 'pending',
});

const { handleSubmit } = useVeeForm({
    validationSchema: formSchema,
});

const handleDocumentChange = (event) => {
    form.document = event.target.files[0] || null;
};

const handlePaymentProofChange = (event) => {
    form.payment_proof = event.target.files[0] || null;
};

const onSubmit = handleSubmit(() => {
    form.post('/supplier-invoices', {
        forceFormData: true,
        preserveScroll: true,
    });
});
</script>

