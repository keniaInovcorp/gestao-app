<template>
    <AuthenticatedLayout>
        <Head title="Criar País" />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <Link href="/countries" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                ← Voltar
            </Link>
            <h1 class="text-2xl font-bold">Criar País</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form @submit="onSubmit">
                <FormField v-slot="{ componentField }" name="code">
                    <FormItem>
                        <FormLabel>Código</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                placeholder="PT"
                                maxlength="2"
                            />
                        </FormControl>
                        <FormDescription>
                            Código do país (2 caracteres)
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                placeholder="Portugal"
                            />
                        </FormControl>
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
                    <Button type="button" variant="outline" @click="router.visit('/countries')">
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

const schema = toTypedSchema(z.object({
    code: z.string().min(2).max(2, 'O código deve ter exatamente 2 caracteres'),
    name: z.string().min(1, 'O nome é obrigatório'),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit } = useForm({
    validationSchema: schema,
    initialValues: {
        code: '',
        name: '',
        status: 'active',
    },
});

const inertiaForm = useInertiaForm({
    code: '',
    name: '',
    status: 'active',
});

const onSubmit = handleSubmit((values) => {
    console.log('Submitting:', values);
    inertiaForm.code = values.code;
    inertiaForm.name = values.name;
    inertiaForm.status = values.status;
    
    inertiaForm.post('/countries', {
        onSuccess: () => {
            console.log('Success!');
        },
        onError: (errors) => {
            console.log('Errors:', errors);
        },
        onFinish: () => {
            console.log('Finished');
        }
    });
}, (errors) => {
    console.log('Validation errors:', errors);
});
</script>

