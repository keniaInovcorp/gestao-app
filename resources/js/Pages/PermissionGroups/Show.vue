<template>
    <AuthenticatedLayout>
        <Head title="Detalhes do Grupo de Permissões" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/permission-groups" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes do Grupo de Permissões</h1>
                    <div class="flex space-x-2" v-if="canUpdate('permission-groups') || canDelete('permission-groups')">
                        <Link 
                            v-if="canUpdate('permission-groups')"
                            :href="`/permission-groups/${permissionGroup.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('permission-groups')"
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Nome do Grupo</h3>
                        <p class="text-lg">{{ permissionGroup.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Utilizadores Relacionados</h3>
                        <p class="text-lg">{{ permissionGroup.users_count }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="permissionGroup.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ permissionGroup.status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                </div>

                <div v-if="permissionGroup.permissions && permissionGroup.permissions.length > 0" class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Permissões Atribuídas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        <div v-for="permission in permissionGroup.permissions" :key="permission" 
                             class="bg-gray-50 px-3 py-2 rounded-md text-sm">
                            {{ formatPermission(permission) }}
                        </div>
                    </div>
                </div>
                <div v-else class="mt-6">
                    <p class="text-gray-500">Nenhuma permissão atribuída a este grupo.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/composables/usePermissions';

const props = defineProps({
    permissionGroup: Object,
});

const { canUpdate, canDelete } = usePermissions();

const formatPermission = (permission) => {
    const parts = permission.split('.');
    const menu = parts[0];
    const action = parts[1];
    
    const menuLabels = {
        'clients': 'Clientes',
        'suppliers': 'Fornecedores',
        'contacts': 'Contactos',
        'products': 'Artigos',
        'quotations': 'Propostas',
        'orders': 'Encomendas',
        'supplier-orders': 'Encomendas Fornecedor',
        'supplier-invoices': 'Faturas Fornecedor',
        'users': 'Utilizadores',
        'permission-groups': 'Permissões',
        'countries': 'Países',
        'contact-functions': 'Funções de Contacto',
        'vat-rates': 'Taxas de IVA',
    };
    
    const actionLabels = {
        'create': 'Criar',
        'read': 'Ler',
        'update': 'Atualizar',
        'delete': 'Eliminar',
    };
    
    return `${menuLabels[menu] || menu} - ${actionLabels[action] || action}`;
};

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar o grupo de permissões "${props.permissionGroup.name}"?`)) {
        router.delete(`/permission-groups/${props.permissionGroup.id}`);
    }
};
</script>

