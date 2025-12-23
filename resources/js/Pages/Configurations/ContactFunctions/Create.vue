<template>
    <AuthenticatedLayout>
        <Head title="Criar Função de Contacto" />
        <div class="py-8">
        <div class="mb-6">
            <Link href="/contact-functions" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                ← Voltar
            </Link>
            <h1 class="text-2xl font-bold">Criar Função de Contacto</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form @submit="onSubmit">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                placeholder="Ex: Diretor Comercial"
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
                    <Button type="button" variant="outline" @click="router.visit('/contact-functions')">
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

const schema = toTypedSchema(z.object({
    name: z.string().min(1, 'O nome é obrigatório'),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit } = useForm({
    validationSchema: schema,
    initialValues: {
        name: '',
        status: 'active',
    },
});

const inertiaForm = useInertiaForm({
    name: '',
    status: 'active',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.name = values.name;
    inertiaForm.status = values.status;
    
    inertiaForm.post('/contact-functions');
});
</script>

