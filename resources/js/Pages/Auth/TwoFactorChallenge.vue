<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div v-if="company?.has_logo" class="flex justify-center mb-6">
                    <img 
                        src="/company/logo" 
                        :alt="company?.name || 'Logo'" 
                        class="h-20 w-auto object-contain"
                    />
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Autenticação de Dois Fatores
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Por favor, insira o código de autenticação gerado pela sua aplicação autenticadora.
                </p>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div>
                    <label for="code" class="sr-only">Código de Autenticação</label>
                    <input
                        id="code"
                        v-model="form.code"
                        type="text"
                        required
                        maxlength="6"
                        autofocus
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm text-center text-lg tracking-widest"
                        placeholder="000000"
                    />
                </div>

                <div v-if="form.errors.code" class="text-red-600 text-sm text-center">
                    <p>{{ form.errors.code }}</p>
                </div>

                <div class="flex flex-col space-y-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        {{ form.processing ? 'A verificar...' : 'Verificar' }}
                    </button>

                    <button
                        type="button"
                        @click="useRecoveryCode"
                        class="text-sm text-indigo-600 hover:text-indigo-500"
                    >
                        Usar código de recuperação
                    </button>
                </div>

                <div v-if="showRecoveryCode" class="space-y-4">
                    <div>
                        <label for="recovery_code" class="sr-only">Código de Recuperação</label>
                        <input
                            id="recovery_code"
                            v-model="form.recovery_code"
                            type="text"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Código de recuperação"
                        />
                    </div>

                    <button
                        type="button"
                        @click="submitRecoveryCode"
                        :disabled="form.processing || !form.recovery_code"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        {{ form.processing ? 'A verificar...' : 'Verificar Código de Recuperação' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const company = computed(() => page.props.company);

const showRecoveryCode = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const submit = () => {
    form.post('/two-factor-challenge', {
        onFinish: () => form.reset('code'),
    });
};

const useRecoveryCode = () => {
    showRecoveryCode.value = true;
    form.reset('code');
};

const submitRecoveryCode = () => {
    form.post('/two-factor-challenge', {
        onFinish: () => {
            form.reset('recovery_code');
            showRecoveryCode.value = false;
        },
    });
};
</script>

