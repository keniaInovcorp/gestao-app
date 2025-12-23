<template>
    <AuthenticatedLayout>
        <Head title="Criar Contacto" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/contacts" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Criar Contacto</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="entity_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Entidade</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione a entidade" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="entity in entities" :key="entity.id" :value="entity.id.toString()">
                                            {{ entity.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="function_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Função</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione a função" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="contactFunction in contactFunctions" :key="contactFunction.id" :value="contactFunction.id.toString()">
                                            {{ contactFunction.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="first_name">
                            <FormItem class="space-y-2">
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Nome"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="last_name">
                            <FormItem class="space-y-2">
                                <FormLabel>Apelido</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Apelido"
                                    />
                                </FormControl>
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

                        <FormField v-slot="{ componentField }" name="email" class="md:col-span-2">
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

                        <FormField v-slot="{ componentField }" name="gdpr_consent" class="md:col-span-2">
                            <FormItem class="flex flex-row items-start space-x-3 space-y-0">
                                <FormControl>
                                    <Checkbox
                                        :model-value="componentField.value"
                                        @update:model-value="componentField.onChange"
                                    />
                                </FormControl>
                                <div class="space-y-1 leading-none">
                                    <FormLabel>
                                        Consentimento RGPD
                                    </FormLabel>
                                    <FormDescription>
                                        Autoriza o tratamento dos dados pessoais
                                    </FormDescription>
                                </div>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <Button 
                            type="submit" 
                            :disabled="inertiaForm.processing"
                            class="cursor-pointer"
                        >
                            {{ inertiaForm.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                        <Button 
                            type="button" 
                            variant="outline" 
                            @click="router.visit('/contacts')"
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
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

const props = defineProps({
    entities: Array,
    contactFunctions: Array,
});

const schema = toTypedSchema(z.object({
    entity_id: z.string().min(1, 'A entidade é obrigatória'),
    first_name: z.string().min(1, 'O nome é obrigatório'),
    last_name: z.string().min(1, 'O apelido é obrigatório'),
    function_id: z.string().min(1, 'A função é obrigatória'),
    phone: z.string().optional(),
    mobile: z.string().optional(),
    email: z.string().refine((val) => !val || z.string().email().safeParse(val).success, { message: 'Email inválido' }).optional(),
    gdpr_consent: z.boolean().refine((val) => val === true, {
        message: 'É obrigatório aceitar o consentimento RGPD',
    }),
    notes: z.string().optional(),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        entity_id: '',
        first_name: '',
        last_name: '',
        function_id: '',
        phone: '',
        mobile: '',
        email: '',
        gdpr_consent: false,
        notes: '',
        status: 'active',
    },
});

const inertiaForm = useInertiaForm({
    entity_id: '',
    first_name: '',
    last_name: '',
    function_id: '',
    phone: '',
    mobile: '',
    email: '',
    gdpr_consent: false,
    notes: '',
    status: 'active',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.entity_id = values.entity_id;
    inertiaForm.first_name = values.first_name;
    inertiaForm.last_name = values.last_name;
    inertiaForm.function_id = values.function_id;
    inertiaForm.phone = values.phone || '';
    inertiaForm.mobile = values.mobile || '';
    inertiaForm.email = values.email || '';
    inertiaForm.gdpr_consent = values.gdpr_consent || false;
    inertiaForm.notes = values.notes || '';
    inertiaForm.status = values.status;
    
    inertiaForm.post('/contacts', {
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
    });
});
</script>

