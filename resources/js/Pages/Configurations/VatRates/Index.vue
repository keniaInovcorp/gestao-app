<template>
    <AuthenticatedLayout>
        <Head title="Taxas de IVA" />
        <div class="py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Taxas de IVA</h1>
            <Link href="/vat-rates/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Adicionar Taxa de IVA
            </Link>
        </div>

        <div v-if="vatRates && vatRates.data" class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentagem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="vatRates.data.length > 0" v-for="vatRate in vatRates.data" :key="vatRate.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(vatRate.name) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(String(vatRate.percentage) + '%') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span :class="vatRate.status === 'active' ? 'text-green-600' : 'text-red-600'">
                                {{ vatRate.status === 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <Link :href="`/vat-rates/${vatRate.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Nenhuma taxa de IVA encontrada. <Link href="/vat-rates/create" class="text-blue-600 hover:text-blue-900">Criar a primeira taxa</Link>
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
    vatRates: {
        type: Object,
        default: () => ({ data: [] })
    },
});
</script>

