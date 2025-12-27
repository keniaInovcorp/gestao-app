<template>
    <AuthenticatedLayout>
        <Head :title="entity.type === 'client' ? 'Detalhes do Cliente' : 'Detalhes do Fornecedor'" />
        <div class="py-8">
            <div class="mb-6">
                <Link :href="entity.type === 'client' ? '/clients' : '/suppliers'" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">{{ entity.type === 'client' ? 'Detalhes do Cliente' : 'Detalhes do Fornecedor' }}</h1>
                    <div class="flex space-x-2" v-if="canUpdate(resourceName) || canDelete(resourceName)">
                        <Link 
                            v-if="canUpdate(resourceName)"
                            :href="`/entities/${entity.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete(resourceName)"
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">NIF</h3>
                        <p class="text-lg">{{ entity.tax_number }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Nome</h3>
                        <p class="text-lg">{{ entity.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Morada</h3>
                        <p class="text-lg">{{ entity.address || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Código Postal</h3>
                        <p class="text-lg">{{ entity.postal_code || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Localidade</h3>
                        <p class="text-lg">{{ entity.city || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">País</h3>
                        <p class="text-lg">{{ entity.country?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Telefone</h3>
                        <p class="text-lg">{{ entity.phone || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Telemóvel</h3>
                        <p class="text-lg">{{ entity.mobile || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Website</h3>
                        <p class="text-lg">{{ entity.website || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Email</h3>
                        <p class="text-lg">{{ entity.email || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="entity.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ entity.status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <div v-if="entity.notes" class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Observações</h3>
                        <p class="text-lg">{{ entity.notes }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/composables/usePermissions';
import { computed } from 'vue';

const props = defineProps({
    entity: Object,
});

const { canUpdate, canDelete } = usePermissions();

const resourceName = computed(() => {
    return props.entity.type === 'client' ? 'clients' : 'suppliers';
});

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar a entidade "${props.entity.name}"?`)) {
        router.delete(`/entities/${props.entity.id}`);
    }
};
</script>

