<template>
    <AuthenticatedLayout>
        <Head title="Criar Utilizador" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/users" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Criar Utilizador</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem class="space-y-2">
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Nome completo"
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

                        <FormField v-slot="{ componentField }" name="role_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Tipo de Utilizador</FormLabel>
                                <Select v-bind="componentField" @update:model-value="handleRoleChange">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o tipo de utilizador" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="role in roles" :key="role.id" :value="role.id.toString()">
                                            {{ role.name === 'admin' ? 'Administrador' : 'Utilizador' }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="permission_group_id" v-if="selectedRoleId && isRegularUser">
                            <FormItem class="space-y-2">
                                <FormLabel>Grupo de Permissões</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o grupo de permissões (opcional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Nenhum</SelectItem>
                                        <SelectItem v-for="group in permissionGroups" :key="group.id" :value="group.id.toString()">
                                            {{ group.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="status">
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
                            @click="router.visit('/users')"
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
    roles: Array,
    permissionGroups: Array,
});

const selectedRoleId = ref('');

const isRegularUser = computed(() => {
    if (!selectedRoleId.value) return false;
    const selectedRole = props.roles.find(r => r.id.toString() === selectedRoleId.value);
    return selectedRole?.name === 'regular';
});

const handleRoleChange = (value) => {
    selectedRoleId.value = value;
    const selectedRole = props.roles.find(r => r.id.toString() === value);
    if (!selectedRole || selectedRole.name !== 'regular') {
        inertiaForm.permission_group_id = '';
    }
};

const schema = toTypedSchema(z.object({
    name: z.string().min(1, 'O nome é obrigatório'),
    email: z.string().email('Email inválido').min(1, 'O email é obrigatório'),
    mobile: z.string().optional(),
    role_id: z.string().min(1, 'O tipo de utilizador é obrigatório'),
    permission_group_id: z.string().optional(),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        name: '',
        email: '',
        mobile: '',
        role_id: '',
        permission_group_id: '',
        status: 'active',
    },
});

const inertiaForm = useInertiaForm({
    name: '',
    email: '',
    mobile: '',
    role_id: '',
    permission_group_id: '',
    status: 'active',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.name = values.name;
    inertiaForm.email = values.email;
    inertiaForm.mobile = values.mobile || '';
    inertiaForm.role_id = values.role_id || '';
    inertiaForm.permission_group_id = values.permission_group_id || '';
    inertiaForm.status = values.status;
    
    inertiaForm.post('/users', {
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
    });
});
</script>

