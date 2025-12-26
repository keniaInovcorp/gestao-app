<template>
    <AuthenticatedLayout>
        <Head title="Criar Proposta" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/quotations" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Criar Proposta</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="client_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Cliente</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField" @update:model-value="handleClientChange">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o cliente" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="client in clients" :key="client.id" :value="client.id.toString()">
                                                {{ client.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="quotation_date">
                            <FormItem class="space-y-2">
                                <FormLabel>Data da Proposta</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="date"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="validity">
                            <FormItem class="space-y-2">
                                <FormLabel>Validade</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="date"
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
                                            <SelectItem value="draft">Rascunho</SelectItem>
                                            <SelectItem value="closed">Fechado</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">Linhas de Artigos</h2>
                            <Button type="button" @click="addLine" variant="outline">
                                Adicionar Linha
                            </Button>
                        </div>

                        <div v-if="lines.length === 0" class="text-center py-8 text-gray-500">
                            Nenhuma linha adicionada. Clique em "Adicionar Linha" para começar.
                        </div>

                        <div v-for="(line, index) in lines" :key="index" class="border rounded-lg p-4 mb-4 relative">
                            <Button 
                                type="button" 
                                @click="removeLine(index)" 
                                variant="destructive" 
                                size="sm"
                                class="absolute top-2 right-2 h-6 w-6 p-0 flex items-center justify-center hover:opacity-80 transition-opacity"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </Button>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Artigo</label>
                                    <div class="relative">
                                        <Input
                                            v-model="line.searchTerm"
                                            @input="searchProduct(index)"
                                            placeholder="Pesquisar por referência ou nome..."
                                            class="w-full"
                                        />
                                        <div v-if="line.showSuggestions && line.suggestions.length > 0" 
                                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                            <div v-for="suggestion in line.suggestions" 
                                                 :key="suggestion.id"
                                                 @click="selectProduct(index, suggestion)"
                                                 class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                <div class="font-medium">{{ suggestion.reference }}</div>
                                                <div class="text-sm text-gray-500">{{ suggestion.name }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="line.product" class="mt-2 text-sm">
                                        <strong>{{ line.product.reference }}</strong> - {{ line.product.name }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Fornecedor</label>
                                    <Select v-model="line.supplier_id">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Selecione o fornecedor" class="truncate" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Nenhum</SelectItem>
                                            <SelectItem v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id.toString()" class="truncate">
                                                <span class="truncate block">{{ supplier.name }}</span>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <div v-if="line.product" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Quantidade</label>
                                    <Input
                                        v-model.number="line.quantity"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        @input="updateLineTotal(index)"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Preço Unitário</label>
                                    <Input
                                        v-model.number="line.unit_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        @input="updateLineTotal(index)"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Preço de Custo</label>
                                    <Input
                                        v-model.number="line.cost_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                    />
                                </div>
                            </div>

                            <div v-if="line.product" class="mt-2 text-sm text-gray-600">
                                <strong>Total:</strong> {{ formatCurrency((line.quantity || 0) * (line.unit_price || 0)) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <Button
                            type="submit"
                            :disabled="inertiaForm.processing || lines.length === 0"
                            class="cursor-pointer"
                        >
                            {{ inertiaForm.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.visit('/quotations')"
                            class="cursor-pointer"
                        >
                            Cancelar
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router, useForm as useInertiaForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { ref, computed } from 'vue';

const props = defineProps({
    clients: Array,
    products: Array,
    suppliers: Array,
});

const lines = ref([]);

const schema = toTypedSchema(z.object({
    client_id: z.string().min(1, 'O cliente é obrigatório'),
    quotation_date: z.string().optional(),
    validity: z.string().min(1, 'A validade é obrigatória'),
    status: z.enum(['draft', 'closed']),
}));

const { handleSubmit, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        client_id: '',
        quotation_date: '',
        validity: '',
        status: 'draft',
    },
});

const inertiaForm = useInertiaForm({
    client_id: '',
    quotation_date: '',
    validity: '',
    status: 'draft',
    lines: [],
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
};

const addLine = () => {
    lines.value.push({
        product_id: null,
        product: null,
        supplier_id: '',
        quantity: 1,
        unit_price: 0,
        cost_price: null,
        searchTerm: '',
        suggestions: [],
        showSuggestions: false,
    });
};

const removeLine = (index) => {
    lines.value.splice(index, 1);
};

const searchProduct = (index) => {
    const line = lines.value[index];
    if (!line.searchTerm || line.searchTerm.length < 2) {
        line.suggestions = [];
        line.showSuggestions = false;
        return;
    }

    const term = line.searchTerm.toLowerCase();
    line.suggestions = props.products.filter(product => 
        product.reference.toLowerCase().includes(term) || 
        product.name.toLowerCase().includes(term)
    ).slice(0, 10);
    line.showSuggestions = true;
};

const selectProduct = (index, product) => {
    const line = lines.value[index];
    line.product = product;
    line.product_id = product.id;
    line.unit_price = product.price;
    line.searchTerm = product.reference + ' - ' + product.name;
    line.suggestions = [];
    line.showSuggestions = false;
    updateLineTotal(index);
};

const updateLineTotal = (index) => {
    // Trigger reactivity
};

const handleClientChange = (value) => {
    if (value && !inertiaForm.quotation_date) {
        const today = new Date();
        inertiaForm.quotation_date = today.toISOString().split('T')[0];
        
        const validityDate = new Date(today);
        validityDate.setDate(validityDate.getDate() + 30);
        inertiaForm.validity = validityDate.toISOString().split('T')[0];
    }
};

const onSubmit = handleSubmit((values) => {
    if (lines.value.length === 0) {
        alert('Adicione pelo menos uma linha de artigo');
        return;
    }

    const validLines = lines.value.filter(line => line.product_id);
    if (validLines.length === 0) {
        alert('Todas as linhas devem ter um produto selecionado');
        return;
    }

    inertiaForm.client_id = values.client_id;
    inertiaForm.quotation_date = values.quotation_date || null;
    inertiaForm.validity = values.validity;
    inertiaForm.status = values.status;
    inertiaForm.lines = validLines.map(line => ({
        product_id: line.product_id,
        supplier_id: line.supplier_id || null,
        quantity: parseFloat(line.quantity),
        unit_price: parseFloat(line.unit_price),
        cost_price: line.cost_price ? parseFloat(line.cost_price) : null,
    }));
    
    inertiaForm.post('/quotations', {
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
    });
});
</script>

