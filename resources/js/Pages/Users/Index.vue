<template>
    <AuthenticatedLayout>
        <Head title="Utilizadores" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Utilizadores</h1>
                <Link href="/users/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Adicionar Utilizador
                </Link>
            </div>

            <div v-if="users && users.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telemóvel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo de Permissões</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="users.data.length > 0" v-for="user in users.data" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(user.name) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(user.email) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(user.mobile || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span v-if="user.roles && user.roles.length > 0">
                                    {{ user.roles.map(r => r.name === 'admin' ? 'Administrador' : r.name === 'regular' ? 'Utilizador' : r.name).join(', ') }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="user.status === 'active' ? 'text-green-600' : 'text-red-600'">
                                    {{ user.status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <Link :href="`/users/${user.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum utilizador encontrado. <Link href="/users/create" class="text-blue-600 hover:text-blue-900">Criar o primeiro utilizador</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500">Carregando...</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { truncate } from '@/utils/truncate';

const props = defineProps({
    users: {
        type: Object,
        default: () => ({ data: [] })
    },
});
</script>

