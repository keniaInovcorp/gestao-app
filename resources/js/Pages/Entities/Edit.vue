<template>
    <AuthenticatedLayout>
        <Head :title="entity.type === 'client' ? 'Editar Cliente' : 'Editar Fornecedor'" />
        <div class="py-8">
            <div class="mb-6">
                <Link :href="entity.type === 'client' ? '/clients' : '/suppliers'" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Editar {{ entity.type === 'client' ? 'Cliente' : 'Fornecedor' }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="tax_number">
                            <FormItem class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <FormLabel>NIF</FormLabel>
                                    <FormDescription class="text-xs text-muted-foreground">
                                        Número de Identificação Fiscal
                                    </FormDescription>
                                </div>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="123456789"
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
                                        placeholder="Nome da entidade"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="address" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Morada</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Rua, número"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="postal_code">
                            <FormItem class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <FormLabel>Código Postal</FormLabel>
                                    <FormDescription class="text-xs text-muted-foreground">
                                        Formato: XXXX-XXX
                                    </FormDescription>
                                </div>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="1234-567"
                                        maxlength="8"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="city">
                            <FormItem class="space-y-2">
                                <FormLabel>Localidade</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Cidade"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="country_id">
                            <FormItem class="space-y-2">
                                <FormLabel>País</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o país" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="country in countries" :key="country.id" :value="country.id.toString()">
                                            {{ country.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="phone">
                            <FormItem class="space-y-2">
                                <FormLabel>Telefone</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="+351 123 456 789"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="mobile">
                            <FormItem class="space-y-2">
                                <FormLabel>Telemóvel</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="+351 912 345 678"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="website">
                            <FormItem class="space-y-2">
                                <FormLabel>Website</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="https://exemplo.pt"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="email">
                            <FormItem class="space-y-2">
                                <FormLabel>Email</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="email"
                                        placeholder="exemplo@email.com"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="notes" class="md:col-span-2">
                            <FormItem class="space-y-2">
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
                        <Button type="button" variant="outline" @click="router.visit(entity.type === 'client' ? '/clients' : '/suppliers')">
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
    entity: Object,
    countries: Array,
});

const schema = toTypedSchema(z.object({
    type: z.enum(['client', 'supplier']),
    tax_number: z.string().min(1, 'O NIF é obrigatório'),
    name: z.string().min(1, 'O nome é obrigatório'),
    address: z.string().min(1, 'A morada é obrigatória'),
    postal_code: z.string().regex(/^\d{4}-\d{3}$/, 'Formato inválido (XXXX-XXX)'),
    city: z.string().min(1, 'A localidade é obrigatória'),
    country_id: z.string().min(1, 'O país é obrigatório'),
    phone: z.string().optional(),
    mobile: z.string().optional(),
    website: z.string().refine((val) => !val || z.string().url().safeParse(val).success, { message: 'URL inválida' }).optional(),
    email: z.string().refine((val) => !val || z.string().email().safeParse(val).success, { message: 'Email inválido' }).optional(),
    notes: z.string().optional(),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit } = useForm({
    validationSchema: schema,
    initialValues: {
        type: props.entity?.type || 'client',
        tax_number: props.entity?.tax_number || '',
        name: props.entity?.name || '',
        address: props.entity?.address || '',
        postal_code: props.entity?.postal_code || '',
        city: props.entity?.city || '',
        country_id: props.entity?.country_id?.toString() || '',
        phone: props.entity?.phone || '',
        mobile: props.entity?.mobile || '',
        website: props.entity?.website || '',
        email: props.entity?.email || '',
        notes: props.entity?.notes || '',
        status: props.entity?.status || 'active',
    },
});

const inertiaForm = useInertiaForm({
    type: props.entity?.type || 'cliente',
    tax_number: props.entity?.tax_number || '',
    name: props.entity?.name || '',
    address: props.entity?.address || '',
    postal_code: props.entity?.postal_code || '',
    city: props.entity?.city || '',
    country_id: props.entity?.country_id?.toString() || '',
    phone: props.entity?.phone || '',
    mobile: props.entity?.mobile || '',
    website: props.entity?.website || '',
    email: props.entity?.email || '',
    gdpr_consent: props.entity?.gdpr_consent || false,
    notes: props.entity?.notes || '',
    status: props.entity?.status || 'active',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.type = values.type;
    inertiaForm.tax_number = values.tax_number;
    inertiaForm.name = values.name;
    inertiaForm.address = values.address;
    inertiaForm.postal_code = values.postal_code;
    inertiaForm.city = values.city;
    inertiaForm.country_id = values.country_id;
    inertiaForm.phone = values.phone || '';
    inertiaForm.mobile = values.mobile || '';
    inertiaForm.website = values.website || '';
    inertiaForm.email = values.email || '';
    inertiaForm.notes = values.notes || '';
    inertiaForm.status = values.status;
    
    inertiaForm.put(`/entities/${props.entity.id}`);
});
</script>

