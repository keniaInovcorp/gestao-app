<template>
    <AuthenticatedLayout>
        <Head title="Editar Artigo" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/products" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Editar Artigo</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="reference">
                            <FormItem class="space-y-2">
                                <FormLabel>Referência</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Ex: ART001"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem class="space-y-2">
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Nome do artigo"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="description" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Descrição</FormLabel>
                                <FormControl>
                                    <textarea
                                        v-bind="componentField"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Descrição do artigo"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="price">
                            <FormItem class="space-y-2">
                                <FormLabel>Preço</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="vat_rate_id">
                            <FormItem class="space-y-2">
                                <FormLabel>IVA</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione a taxa de IVA" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="vatRate in vatRates" :key="vatRate.id" :value="vatRate.id.toString()">
                                            {{ vatRate.name }} ({{ vatRate.percentage }}%)
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="photo" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Foto</FormLabel>
                                <div v-if="product.photo" class="mb-2">
                                    <img 
                                        :src="`/products/${product.id}/photo`" 
                                        :alt="product.name"
                                        class="h-32 w-32 object-cover rounded"
                                    />
                                </div>
                                <FormControl>
                                    <Input
                                        type="file"
                                        accept="image/*"
                                        @change="handlePhotoChange"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Formatos aceites: JPEG, PNG, JPG, GIF (máx. 2MB). Deixe em branco para manter a foto atual.
                                </FormDescription>
                                <div v-if="photoPreview" class="mt-2">
                                    <img :src="photoPreview" alt="Preview" class="h-32 w-32 object-cover rounded border" />
                                </div>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="notes" class="md:col-span-2">
                            <FormItem>
                                <FormLabel>Observações</FormLabel>
                                <FormControl>
                                    <textarea
                                        v-bind="componentField"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Observações adicionais"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="status" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Estado</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o estado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="active">Ativo</SelectItem>
                                        <SelectItem value="inactive">Inativo</SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <Button type="submit" :disabled="inertiaForm.processing">
                            {{ inertiaForm.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                        <Button type="button" variant="outline" @click="router.visit('/products')">
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
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
    vatRates: Array,
});

const photoPreview = ref(null);
const photoFile = ref(null);

const schema = toTypedSchema(z.object({
    reference: z.string().min(1, 'A referência é obrigatória'),
    name: z.string().min(1, 'O nome é obrigatório'),
    description: z.string().optional(),
    price: z.coerce.number().min(0, 'O preço não pode ser negativo'),
    vat_rate_id: z.string().min(1, 'A taxa de IVA é obrigatória'),
    notes: z.string().optional(),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        reference: props.product?.reference || '',
        name: props.product?.name || '',
        description: props.product?.description || '',
        price: typeof props.product?.price === 'string' ? parseFloat(props.product.price) : (props.product?.price || 0),
        vat_rate_id: props.product?.vat_rate_id?.toString() || '',
        notes: props.product?.notes || '',
        status: props.product?.status || 'active',
    },
});

const inertiaForm = useInertiaForm({
    reference: props.product?.reference || '',
    name: props.product?.name || '',
    description: props.product?.description || '',
    price: props.product?.price || 0,
    vat_rate_id: props.product?.vat_rate_id?.toString() || '',
    notes: props.product?.notes || '',
    status: props.product?.status || 'active',
});

const handlePhotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        photoFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const onSubmit = handleSubmit((values) => {
    const formData = {
        reference: values.reference,
        name: values.name,
        description: values.description || '',
        price: parseFloat(values.price),
        vat_rate_id: values.vat_rate_id,
        notes: values.notes || '',
        status: values.status,
    };
    
    if (photoFile.value) {
        formData.photo = photoFile.value;
    }
    
    inertiaForm.transform(() => formData).put(`/products/${props.product.id}`, {
        forceFormData: !!photoFile.value,
        preserveScroll: true,
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
    });
});
</script>

