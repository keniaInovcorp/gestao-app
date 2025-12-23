<template>
    <AuthenticatedLayout>
        <Head title="Contactos" />
        <div class="py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Contactos</h1>
                <Link href="/contacts/create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Adicionar Contacto
                </Link>
            </div>

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
                                <Link :href="`/contacts/${contact.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</Link>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum contacto encontrado. <Link href="/contacts/create" class="text-blue-600 hover:text-blue-900">Criar o primeiro contacto</Link>
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
    contacts: {
        type: Object,
        default: () => ({ data: [] })
    },
});
</script>

