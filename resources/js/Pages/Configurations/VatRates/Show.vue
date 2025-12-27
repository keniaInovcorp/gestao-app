<template>
    <AuthenticatedLayout>
        <Head title="Detalhes da Taxa de IVA" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/vat-rates" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes da Taxa de IVA</h1>
                    <div class="flex space-x-2" v-if="canUpdate('vat-rates') || canDelete('vat-rates')">
                        <Link 
                            v-if="canUpdate('vat-rates')"
                            :href="`/vat-rates/${vatRate.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('vat-rates')"
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
                        <p class="text-lg">{{ vatRate.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Percentagem</h3>
                        <p class="text-lg">{{ vatRate.percentage }}%</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="vatRate.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ vatRate.status === 'active' ? 'Ativo' : 'Inativo' }}
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
    vatRate: Object,
});

const { canUpdate, canDelete } = usePermissions();

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar a taxa de IVA "${props.vatRate.name}"?`)) {
        router.delete(`/vat-rates/${props.vatRate.id}`);
    }
};
</script>

