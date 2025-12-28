<template>
    <AuthenticatedLayout>
        <Head title="Detalhes do Evento" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/calendar" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes do Evento</h1>
                    <div class="flex space-x-2" v-if="canUpdate('calendar-events') || canDelete('calendar-events')">
                        <Link 
                            v-if="canUpdate('calendar-events')"
                            :href="`/calendar-events/${event.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('calendar-events')"
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <FlashMessages />

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Data</h3>
                        <p class="text-lg">{{ formatDate(event.date) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Hora</h3>
                        <p class="text-lg">{{ formatTime(event.time) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Duração</h3>
                        <p class="text-lg">{{ event.duration }} minutos</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Utilizador</h3>
                        <p class="text-lg">{{ event.user?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Entidade</h3>
                        <p class="text-lg">{{ event.entity?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Tipo</h3>
                        <p class="text-lg">{{ event.type?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Acção</h3>
                        <p class="text-lg">{{ event.action?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="event.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ event.status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Partilha</h3>
                        <span :class="event.share ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ event.share ? 'Sim' : 'Não' }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Conhecimento</h3>
                        <span :class="event.knowledge ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ event.knowledge ? 'Sim' : 'Não' }}
                        </span>
                    </div>
                    <div class="md:col-span-2" v-if="event.description">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Descrição</h3>
                        <p class="text-lg whitespace-pre-wrap">{{ event.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { usePermissions } from '@/composables/usePermissions';

const props = defineProps({
    event: Object,
});

const { canUpdate, canDelete } = usePermissions();

const formatDate = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString('pt-PT', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (time) => {
    if (!time) return '-';
    if (typeof time === 'string') {
        return time.substring(0, 5);
    }
    return time;
};

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar este evento?`)) {
        router.delete(`/calendar-events/${props.event.id}`, {
            onSuccess: () => {
                router.visit('/calendar');
            }
        });
    }
};
</script>

