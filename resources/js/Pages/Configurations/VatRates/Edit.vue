<template>
    <AuthenticatedLayout>
        <Head title="Editar Taxa de IVA" />
        <div class="py-8">
        <div class="mb-6">
            <Link href="/vat-rates" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                ← Voltar
            </Link>
            <h1 class="text-2xl font-bold">Editar Taxa de IVA</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form @submit="onSubmit">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                placeholder="Ex: IVA Normal"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="percentage">
                    <FormItem>
                        <FormLabel>Percentagem</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                placeholder="23.00"
                            />
                        </FormControl>
                        <FormDescription>
                            Percentagem da taxa de IVA (0 a 100)
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="status">
                    <FormItem>
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

                <div class="flex gap-4 mt-6">
                    <Button type="submit" :disabled="inertiaForm.processing">
                        {{ inertiaForm.processing ? 'A guardar...' : 'Guardar' }}
                    </Button>
                    <Button type="button" variant="outline" @click="router.visit('/vat-rates')">
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

const props = defineProps({
    vatRate: Object,
});

const schema = toTypedSchema(z.object({
    name: z.string().min(1, 'O nome é obrigatório'),
    percentage: z.number().min(0, 'A percentagem não pode ser negativa').max(100, 'A percentagem não pode ser superior a 100%'),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit } = useForm({
    validationSchema: schema,
    initialValues: {
        name: props.vatRate?.name || '',
        percentage: props.vatRate?.percentage || 0,
        status: props.vatRate?.status || 'active',
    },
});

const inertiaForm = useInertiaForm({
    name: props.vatRate?.name || '',
    percentage: props.vatRate?.percentage || 0,
    status: props.vatRate?.status || 'active',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.name = values.name;
    inertiaForm.percentage = values.percentage;
    inertiaForm.status = values.status;
    
    inertiaForm.put(`/vat-rates/${props.vatRate.id}`);
});
</script>

