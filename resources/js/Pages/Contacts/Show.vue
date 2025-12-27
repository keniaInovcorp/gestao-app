<template>
    <AuthenticatedLayout>
        <Head title="Detalhes do Contacto" />
        <div class="py-8">
            <div class="mb-6">
                <Link href="/contacts" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalhes do Contacto</h1>
                    <div class="flex space-x-2" v-if="canUpdate('contacts') || canDelete('contacts')">
                        <Link 
                            v-if="canUpdate('contacts')"
                            :href="`/contacts/${contact.id}/edit`" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Editar
                        </Link>
                        <button
                            v-if="canDelete('contacts')"
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
                        <p class="text-lg">{{ contact.first_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Apelido</h3>
                        <p class="text-lg">{{ contact.last_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Entidade</h3>
                        <p class="text-lg">{{ contact.entity?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Função</h3>
                        <p class="text-lg">{{ contact.contact_function?.name || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Telefone</h3>
                        <p class="text-lg">{{ contact.phone || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Telemóvel</h3>
                        <p class="text-lg">{{ contact.mobile || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Email</h3>
                        <p class="text-lg">{{ contact.email || '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <span :class="contact.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                              class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ contact.status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <div v-if="contact.notes" class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Observações</h3>
                        <p class="text-lg">{{ contact.notes }}</p>
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
    contact: Object,
});

const { canUpdate, canDelete } = usePermissions();

const confirmDelete = () => {
    if (confirm(`Tem certeza que deseja eliminar o contacto "${props.contact.first_name} ${props.contact.last_name}"?`)) {
        router.delete(`/contacts/${props.contact.id}`);
    }
};
</script>

