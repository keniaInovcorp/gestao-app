<template>
    <AuthenticatedLayout>
        <Head title="Editar Evento" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/calendar" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Editar Evento</h1>
            </div>

            <FlashMessages />

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="date">
                            <FormItem class="space-y-2">
                                <FormLabel>Data <span class="text-red-500">*</span></FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="date"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="time">
                            <FormItem class="space-y-2">
                                <FormLabel>Hora <span class="text-red-500">*</span></FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="time"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="duration">
                            <FormItem class="space-y-2">
                                <FormLabel>Duração (minutos) <span class="text-red-500">*</span></FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        type="number"
                                        min="1"
                                        placeholder="60"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="user_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Utilizador <span class="text-red-500">*</span></FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o utilizador" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="user in users" :key="user.id" :value="user.id.toString()">
                                                {{ user.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="entity_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Entidade</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione a entidade" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Nenhuma</SelectItem>
                                            <SelectItem v-for="entity in entities" :key="entity.id" :value="entity.id.toString()">
                                                {{ entity.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="type_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Tipo</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Nenhum</SelectItem>
                                            <SelectItem v-for="type in types" :key="type.id" :value="type.id.toString()">
                                                {{ type.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="action_id">
                            <FormItem class="space-y-2">
                                <FormLabel>Acção</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione a acção" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Nenhuma</SelectItem>
                                            <SelectItem v-for="action in actions" :key="action.id" :value="action.id.toString()">
                                                {{ action.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="status">
                            <FormItem class="space-y-2">
                                <FormLabel>Estado <span class="text-red-500">*</span></FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione o estado" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="active">Ativo</SelectItem>
                                            <SelectItem value="inactive">Inativo</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="share">
                            <FormItem class="space-y-2">
                                <FormLabel>Partilha</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="true">Sim</SelectItem>
                                            <SelectItem value="false">Não</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="knowledge">
                            <FormItem class="space-y-2">
                                <FormLabel>Conhecimento</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="true">Sim</SelectItem>
                                            <SelectItem value="false">Não</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="description" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Descrição</FormLabel>
                                <FormControl>
                                    <textarea
                                        v-bind="componentField"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Descrição do evento..."
                                    ></textarea>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <Button type="submit" :disabled="inertiaForm.processing">
                            {{ inertiaForm.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                        <Button type="button" variant="outline" @click="router.visit('/calendar')">
                            Cancelar
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router, useForm as useInertiaForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

const props = defineProps({
    event: Object,
    types: {
        type: Array,
        default: () => [],
    },
    actions: {
        type: Array,
        default: () => [],
    },
    entities: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
});

const schema = toTypedSchema(z.object({
    date: z.string().min(1, 'A data é obrigatória'),
    time: z.string().min(1, 'A hora é obrigatória'),
    duration: z.number().min(1, 'A duração deve ser pelo menos 1 minuto'),
    share: z.string().optional(),
    knowledge: z.string().optional(),
    entity_id: z.string().optional(),
    type_id: z.string().optional(),
    action_id: z.string().optional(),
    description: z.string().optional(),
    user_id: z.string().min(1, 'O utilizador é obrigatório'),
    status: z.enum(['active', 'inactive']),
}));

const eventDate = props.event?.date ? new Date(props.event.date).toISOString().split('T')[0] : '';
const eventTime = props.event?.time ? props.event.time.substring(0, 5) : '09:00';

const { handleSubmit } = useForm({
    validationSchema: schema,
    initialValues: {
        date: eventDate,
        time: eventTime,
        duration: props.event?.duration || 60,
        share: props.event?.share ? 'true' : 'false',
        knowledge: props.event?.knowledge ? 'true' : 'false',
        entity_id: props.event?.entity_id?.toString() || '',
        type_id: props.event?.type_id?.toString() || '',
        action_id: props.event?.action_id?.toString() || '',
        description: props.event?.description || '',
        user_id: props.event?.user_id?.toString() || '',
        status: props.event?.status || 'active',
    },
});

const inertiaForm = useInertiaForm({
    date: eventDate,
    time: eventTime,
    duration: props.event?.duration || 60,
    share: props.event?.share || false,
    knowledge: props.event?.knowledge || false,
    entity_id: props.event?.entity_id || null,
    type_id: props.event?.type_id || null,
    action_id: props.event?.action_id || null,
    description: props.event?.description || '',
    user_id: props.event?.user_id || '',
    status: props.event?.status || 'active',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.date = values.date;
    inertiaForm.time = values.time;
    inertiaForm.duration = values.duration;
    inertiaForm.share = values.share === 'true' || values.share === true;
    inertiaForm.knowledge = values.knowledge === 'true' || values.knowledge === true;
    inertiaForm.entity_id = values.entity_id ? parseInt(values.entity_id) : null;
    inertiaForm.type_id = values.type_id ? parseInt(values.type_id) : null;
    inertiaForm.action_id = values.action_id ? parseInt(values.action_id) : null;
    inertiaForm.description = values.description || '';
    inertiaForm.user_id = parseInt(values.user_id);
    inertiaForm.status = values.status;
    
    inertiaForm.put(`/calendar-events/${props.event.id}`);
});
</script>

