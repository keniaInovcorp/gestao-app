<template>
    <AuthenticatedLayout>
        <Head title="Detalhes do Utilizador" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/users" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes do Utilizador</h1>
                    <div class="flex space-x-2" v-if="canUpdate('users') || canDelete('users')">
                        <Link 
                            v-if="canUpdate('users')"
                            :href="`/users/${user.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('users')"
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
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Nome</h3>
                        <p class="text-lg">{{ user.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Email</h3>
                        <p class="text-lg">{{ user.email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Telemóvel</h3>
                        <p class="text-lg">{{ user.mobile || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Tipo de Utilizador</h3>
                        <p class="text-lg">
                            <span v-if="user.roles && user.roles.length > 0">
                                {{ user.roles.map(r => r.name === 'admin' ? 'Administrador' : r.name === 'regular' ? 'Utilizador' : r.name).join(', ') }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ user.status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
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

const props = defineProps({
    user: Object,
});

const { canUpdate, canDelete } = usePermissions();

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar o utilizador "${props.user.name}"?`)) {
        router.delete(`/users/${props.user.id}`);
    }
};
</script>

