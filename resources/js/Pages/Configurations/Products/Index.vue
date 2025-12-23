<template>
    <AuthenticatedLayout>
        <Head title="Artigos" />
        <div class="py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Artigos</h1>
            <Link href="/products/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Adicionar Artigo
            </Link>
        </div>

        <div v-if="products && products.data" class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="products.data.length > 0" v-for="product in products.data" :key="product.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.reference }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <img 
                                v-if="product.photo" 
                                :src="`/products/${product.id}/photo`" 
                                :alt="product.name"
                                class="h-12 w-12 object-cover rounded"
                            />
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.name }}</td>
                        <td class="px-6 py-4 text-sm">{{ product.description || '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatPrice(product.price) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <Link :href="`/products/${product.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Nenhum artigo encontrado. <Link href="/products/create" class="text-blue-600 hover:text-blue-900">Criar o primeiro artigo</Link>
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

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};
</script>

