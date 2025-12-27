<template>
    <AuthenticatedLayout>
        <Head title="Países" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Países</h1>
                <Link 
                    v-if="canCreate('countries')"
                    href="/countries/create" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Adicionar País
                </Link>
            </div>

            <FlashMessages />

            <div v-if="countries && countries.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="countries.data.length > 0" v-for="country in countries.data" :key="country.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(country.code) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(country.name) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="country.status === 'active' ? 'text-green-600' : 'text-red-600'">
                                    {{ country.status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <Link 
                                        v-if="canRead('countries')"
                                        :href="`/countries/${country.id}`" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="Detalhes"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <Link 
                                        v-if="canUpdate('countries')"
                                        :href="`/countries/${country.id}/edit`" 
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <button
                                        v-if="canDelete('countries')"
                                        @click="confirmDelete(country)"
                                        class="text-red-600 hover:text-red-900"
                                        title="Eliminar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum país encontrado. 
                                <Link v-if="canCreate('countries')" href="/countries/create" class="text-blue-600 hover:text-blue-900">Criar o primeiro país</Link>
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
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { truncate } from '@/utils/truncate';
import { usePermissions } from '@/composables/usePermissions';

const props = defineProps({
    countries: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const { canCreate, canRead, canUpdate, canDelete } = usePermissions();

const confirmDelete = (country) => {
    if (confirm(`Tem certeza que deseja eliminar o país "${country.name}"?`)) {
        router.delete(`/countries/${country.id}`);
    }
};
</script>
