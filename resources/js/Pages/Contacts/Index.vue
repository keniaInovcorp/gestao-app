<template>
    <AuthenticatedLayout>
        <Head title="Contactos" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Contactos</h1>
                <Link 
                    v-if="canCreate('contacts')"
                    href="/contacts/create" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Adicionar Contacto
                </Link>
            </div>

            <FlashMessages />

            <div v-if="contacts && contacts.data" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apelido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Função</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entidade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telemóvel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="contacts.data.length > 0" v-for="contact in contacts.data" :key="contact.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.first_name) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.last_name) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.contact_function?.name || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.entity?.name || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.phone || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.mobile || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ truncate(contact.email || '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <Link 
                                        v-if="canRead('contacts')"
                                        :href="`/contacts/${contact.id}`" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="Detalhes"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <Link 
                                        v-if="canUpdate('contacts')"
                                        :href="`/contacts/${contact.id}/edit`" 
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <button
                                        v-if="canDelete('contacts')"
                                        @click="confirmDelete(contact)"
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
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum contacto encontrado. 
                                <Link v-if="canCreate('contacts')" href="/contacts/create" class="text-blue-600 hover:text-blue-900">Criar o primeiro contacto</Link>
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
    contacts: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const { canCreate, canRead, canUpdate, canDelete } = usePermissions();

const confirmDelete = (contact) => {
    if (confirm(`Tem certeza que deseja eliminar o contacto "${contact.first_name} ${contact.last_name}"?`)) {
        router.delete(`/contacts/${contact.id}`);
    }
};
</script>
