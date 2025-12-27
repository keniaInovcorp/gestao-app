<template>
    <AuthenticatedLayout>
        <Head title="Detalhes do Artigo" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/products" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes do Artigo</h1>
                    <div class="flex space-x-2" v-if="canUpdate('products') || canDelete('products')">
                        <Link 
                            v-if="canUpdate('products')"
                            :href="`/products/${product.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('products')"
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
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Referência</h3>
                        <p class="text-lg">{{ product.reference }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Nome</h3>
                        <p class="text-lg">{{ product.name }}</p>
                    </div>
                    <div v-if="product.photo" class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Foto</h3>
                        <img 
                            :src="`/products/${product.id}/photo`" 
                            :alt="product.name"
                            class="h-32 w-32 object-cover rounded"
                        />
                    </div>
                    <div v-if="product.description" class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Descrição</h3>
                        <p class="text-lg">{{ product.description }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Preço</h3>
                        <p class="text-lg font-semibold">{{ formatPrice(product.price) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Taxa de IVA</h3>
                        <p class="text-lg">{{ product.vat_rate?.name || '-' }} ({{ product.vat_rate?.percentage || 0 }}%)</p>
                    </div>
                    <div v-if="product.notes" class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Observações</h3>
                        <p class="text-lg">{{ product.notes }}</p>
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
    product: Object,
});

const { canUpdate, canDelete } = usePermissions();

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar o artigo "${props.product.name}"?`)) {
        router.delete(`/products/${props.product.id}`);
    }
};
</script>

