<template>
    <AuthenticatedLayout>
        <Head :title="type === 'client' ? 'Clientes' : 'Fornecedores'" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">{{ type === 'client' ? 'Clientes' : 'Fornecedores' }}</h1>
                <Link :href="type === 'client' ? '/clients/create' : '/suppliers/create'" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Adicionar {{ type === 'client' ? 'Cliente' : 'Fornecedor' }}
                </Link>
            </div>

            <div v-if="entities && entities.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIF</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telemóvel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Website</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="entities.data.length > 0" v-for="entity in entities.data" :key="entity.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(entity.tax_number) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(entity.name) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(entity.phone || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(entity.mobile || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(entity.website || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(entity.email || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <Link :href="`/entities/${entity.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum {{ type === 'client' ? 'cliente' : 'fornecedor' }} encontrado. <Link :href="type === 'client' ? '/clients/create' : '/suppliers/create'" class="text-blue-600 hover:text-blue-900">Criar o primeiro {{ type === 'client' ? 'cliente' : 'fornecedor' }}</Link>
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
    entities: {
        type: Object,
        default: () => ({ data: [] })
    },
    type: String,
});
</script>

