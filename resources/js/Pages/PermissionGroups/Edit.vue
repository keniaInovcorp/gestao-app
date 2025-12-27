<template>
    <AuthenticatedLayout>
        <Head title="Editar Grupo de Permissões" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/permission-groups" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Editar Grupo de Permissões</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem class="space-y-2">
                                <FormLabel>Nome do Grupo</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Nome do grupo"
                                    />
                                </FormControl>
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

                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-4">Permissões</h2>
                        <div class="space-y-6">
                            <div v-for="(menuPermissions, menuKey) in groupedPermissions" :key="menuKey" class="border rounded-lg p-4">
                                <h3 class="font-medium mb-3">{{ menus[menuKey] || menuKey }}</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div v-for="permission in menuPermissions" :key="permission.id" class="flex items-center space-x-2">
                                        <Checkbox
                                            :id="`permission-${permission.id}`"
                                            :model-value="!!selectedPermissions[permission.name]"
                                            @update:model-value="(checked) => togglePermission(permission.name, checked)"
                                        />
                                        <label :for="`permission-${permission.id}`" class="text-sm cursor-pointer">
                                            {{ getActionLabel(permission.name) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            @click="router.visit('/permission-groups')"
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
import { Checkbox } from '@/components/ui/checkbox';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    permissionGroup: Object,
    menus: Object,
    permissions: Object,
    rolePermissions: Array,
});

const selectedPermissions = ref({});

const initializePermissions = () => {
    const permissionsObj = {};
    if (props.rolePermissions && Array.isArray(props.rolePermissions) && props.rolePermissions.length > 0) {
        props.rolePermissions.forEach(perm => {
            permissionsObj[perm] = true;
        });
    }
    selectedPermissions.value = { ...permissionsObj };
};

initializePermissions();

watch(() => props.rolePermissions, () => {
    initializePermissions();
}, { immediate: false });

const groupedPermissions = computed(() => {
    return props.permissions || {};
});

const togglePermission = (permissionName, checked) => {
    const current = { ...selectedPermissions.value };
    if (checked) {
        current[permissionName] = true;
    } else {
        delete current[permissionName];
    }
    selectedPermissions.value = current;
};

const getActionLabel = (permissionName) => {
    const parts = permissionName.split('.');
    const action = parts[parts.length - 1];
    const labels = {
        'create': 'Criar',
        'read': 'Ler',
        'update': 'Atualizar',
        'delete': 'Eliminar',
    };
    return labels[action] || action;
};

const schema = toTypedSchema(z.object({
    name: z.string().min(1, 'O nome é obrigatório'),
    status: z.enum(['active', 'inactive']),
    permissions: z.record(z.boolean()),
}));

const { handleSubmit, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        name: props.permissionGroup?.name || '',
        status: props.permissionGroup?.status || 'active',
        permissions: {},
    },
});

const inertiaForm = useInertiaForm({
    name: props.permissionGroup?.name || '',
    status: props.permissionGroup?.status || 'active',
    permissions: {},
});

watch(() => props.permissionGroup, (newGroup) => {
    if (newGroup) {
        inertiaForm.name = newGroup.name;
        inertiaForm.status = newGroup.status;
    }
}, { immediate: true });

const onSubmit = handleSubmit((values) => {
    const permissionsToSend = {};
    Object.keys(selectedPermissions.value).forEach(key => {
        if (selectedPermissions.value[key] === true) {
            permissionsToSend[key] = true;
        }
    });
    
    inertiaForm.name = values.name;
    inertiaForm.status = values.status;
    inertiaForm.permissions = permissionsToSend;
    
    inertiaForm.put(`/permission-groups/${props.permissionGroup.id}`, {
        onSuccess: () => {
            router.visit('/permission-groups');
        },
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
    });
});
</script>

