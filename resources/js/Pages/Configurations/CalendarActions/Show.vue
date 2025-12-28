<template>
    <AuthenticatedLayout>
        <Head title="Detalhes da Ação de Calendário" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/calendar-actions" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes da Ação de Calendário</h1>
                    <div class="flex space-x-2" v-if="canUpdate('calendar-actions') || canDelete('calendar-actions')">
                        <Link 
                            v-if="canUpdate('calendar-actions')"
                            :href="`/calendar-actions/${calendarAction.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('calendar-actions')"
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
                        <p class="text-lg">{{ calendarAction.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="calendarAction.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ calendarAction.status === 'active' ? 'Ativo' : 'Inativo' }}
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
    calendarAction: Object,
});

const { canUpdate, canDelete } = usePermissions();

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar a ação "${props.calendarAction.name}"?`)) {
        router.delete(`/calendar-actions/${props.calendarAction.id}`);
    }
};
</script>

