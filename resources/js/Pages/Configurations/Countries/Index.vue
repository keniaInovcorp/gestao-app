<template>
    <AuthenticatedLayout>
        <Head title="Países" />
        <div class="py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Países</h1>
            <Link href="/countries/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Adicionar País
            </Link>
        </div>

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
                            <Link :href="`/countries/${country.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Nenhum país encontrado. <Link href="/countries/create" class="text-blue-600 hover:text-blue-900">Criar o primeiro país</Link>
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
    countries: {
        type: Object,
        default: () => ({ data: [] })
    },
});
</script>

