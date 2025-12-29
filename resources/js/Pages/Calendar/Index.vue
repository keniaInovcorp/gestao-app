<template>
    <AuthenticatedLayout>
        <Head title="Calendário" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Calendário</h1>
                <Link 
                    v-if="canCreate('calendar-events')"
                    href="/calendar-events/create" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Adicionar Evento
                </Link>
            </div>

            <FlashMessages />

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Utilizador</label>
                        <select
                            v-model="selectedUserId"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Todos os utilizadores</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Entidade</label>
                        <select
                            v-model="selectedEntityId"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Todas as entidades</option>
                            <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                                {{ entity.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <FullCalendar :options="calendarOptions" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { usePermissions } from '@/composables/usePermissions';
import { ref, computed, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    entities: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const { canCreate } = usePermissions();

const selectedUserId = ref(props.filters.user_id || '');
const selectedEntityId = ref(props.filters.entity_id || '');

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
    initialView: 'dayGridMonth',
    locale: 'pt',
    buttonText: {
        today: 'Hoje',
        month: 'Mês',
        week: 'Semana',
        day: 'Dia',
    },
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    events: props.events,
    eventClick: (info) => {
        router.visit(`/calendar-events/${info.event.id}`);
    },
    dateClick: (info) => {
        if (canCreate('calendar-events')) {
            const date = info.dateStr;
            router.visit(`/calendar-events/create?date=${date}`);
        }
    },
    eventDisplay: 'block',
    height: 'auto',
}));

const applyFilters = () => {
    const params = {};
    if (selectedUserId.value) {
        params.user_id = selectedUserId.value;
    }
    if (selectedEntityId.value) {
        params.entity_id = selectedEntityId.value;
    }
    
    router.get('/calendar', params, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

