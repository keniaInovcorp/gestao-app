<template>
    <AuthenticatedLayout>
        <Head title="Perfil" />
        <div class="py-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Meu Perfil</h1>
                <p class="text-gray-600 mt-1">Gerir os seus dados pessoais e password</p>
            </div>

            <FlashMessages />

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Profile Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Informações do Perfil</h2>
                    <form @submit.prevent="onSubmitProfile">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Nome <span class="text-red-500">*</span></label>
                                <Input
                                    v-model="profileForm.name"
                                    placeholder="Seu nome"
                                />
                                <p v-if="profileForm.errors.name" class="text-sm text-red-600">{{ profileForm.errors.name }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Email <span class="text-red-500">*</span></label>
                                <Input
                                    v-model="profileForm.email"
                                    type="email"
                                    placeholder="seu@email.com"
                                />
                                <p v-if="profileForm.errors.email" class="text-sm text-red-600">{{ profileForm.errors.email }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Telemóvel</label>
                                <Input
                                    v-model="profileForm.mobile"
                                    placeholder="+351 912 345 678"
                                />
                                <p v-if="profileForm.errors.mobile" class="text-sm text-red-600">{{ profileForm.errors.mobile }}</p>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="cursor-pointer"
                                >
                                    {{ profileForm.processing ? 'A guardar...' : 'Guardar Alterações' }}
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Two-Factor Authentication -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Autenticação de Dois Fatores</h2>
                    
                    <div v-if="twoFactorEnabled" class="space-y-4">
                        <div v-if="!showDisablePasswordConfirm" class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-green-800 font-medium">2FA Ativado</span>
                            </div>
                            <Button
                                type="button"
                                variant="destructive"
                                size="sm"
                                @click="showDisablePasswordConfirm = true"
                                :disabled="isProcessing2FA"
                            >
                                Desativar
                            </Button>
                        </div>

                        <div v-if="showDisablePasswordConfirm" class="space-y-4">
                            <p class="text-sm font-medium">Por favor, confirme a sua password para desativar o 2FA:</p>
                            <div class="space-y-2">
                                <Input
                                    v-model="disablePasswordConfirm"
                                    type="password"
                                    placeholder="Password"
                                    @keyup.enter="disableTwoFactor"
                                />
                                <p v-if="twoFactorError" class="text-sm text-red-600">{{ twoFactorError }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="cancelDisablePasswordConfirm"
                                    class="flex-1"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="button"
                                    variant="destructive"
                                    @click="disableTwoFactor"
                                    :disabled="!disablePasswordConfirm || isProcessing2FA"
                                    class="flex-1"
                                >
                                    {{ isProcessing2FA ? 'A processar...' : 'Desativar 2FA' }}
                                </Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Button
                                type="button"
                                variant="outline"
                                @click="showRecoveryCodesList"
                                class="w-full"
                            >
                                Ver Códigos de Recuperação
                            </Button>
                        </div>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-if="!showQRCode && !showPasswordConfirm" class="space-y-4">
                            <p class="text-sm text-gray-600">
                                A autenticação de dois fatores adiciona uma camada extra de segurança à sua conta.
                            </p>
                            <Button
                                type="button"
                                @click="showPasswordConfirm = true"
                                :disabled="isProcessing2FA"
                                class="w-full"
                            >
                                {{ isProcessing2FA ? 'A processar...' : 'Ativar 2FA' }}
                            </Button>
                        </div>

                        <div v-if="showPasswordConfirm && !showQRCode" class="space-y-4">
                            <p class="text-sm font-medium">Por favor, confirme a sua password para ativar o 2FA:</p>
                            <div class="space-y-2">
                                <Input
                                    v-model="passwordConfirm"
                                    type="password"
                                    placeholder="Password"
                                    @keyup.enter="enableTwoFactor"
                                />
                                <p v-if="twoFactorError" class="text-sm text-red-600">{{ twoFactorError }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="cancelPasswordConfirm"
                                    class="flex-1"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="button"
                                    @click="enableTwoFactor"
                                    :disabled="!passwordConfirm || isProcessing2FA"
                                    class="flex-1"
                                >
                                    {{ isProcessing2FA ? 'A processar...' : 'Confirmar' }}
                                </Button>
                            </div>
                        </div>

                        <div v-if="showQRCode && qrCode" class="space-y-4">
                            <div class="text-center">
                                <p class="text-sm font-medium mb-2">Escaneie este código QR com a sua aplicação autenticadora:</p>
                                <div class="flex justify-center p-4 bg-white border rounded-lg" v-html="qrCode"></div>
                                <p class="text-xs text-gray-500 mt-2">Ou insira esta chave manualmente:</p>
                                <p class="text-xs font-mono bg-gray-100 p-2 rounded mt-1 break-all">{{ secretKey }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Código de Confirmação <span class="text-red-500">*</span></label>
                                <Input
                                    v-model="twoFactorCode"
                                    placeholder="000000"
                                    maxlength="6"
                                    class="text-center text-lg tracking-widest"
                                />
                                <p v-if="twoFactorError" class="text-sm text-red-600">{{ twoFactorError }}</p>
                            </div>

                            <div class="flex gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="cancelTwoFactorSetup"
                                    class="flex-1"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="button"
                                    @click="confirmTwoFactor"
                                    :disabled="!twoFactorCode || twoFactorCode.length !== 6 || isProcessing2FA"
                                    class="flex-1"
                                >
                                    {{ isProcessing2FA ? 'A confirmar...' : 'Confirmar' }}
                                </Button>
                            </div>
                        </div>

                        <div v-if="showRecoveryCodes && recoveryCodes" class="space-y-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm font-medium text-yellow-800 mb-2">
                                    Guarde estes códigos de recuperação num local seguro. Eles podem ser usados para aceder à sua conta se perder o acesso ao seu dispositivo.
                                </p>
                                <div class="bg-white p-3 rounded border border-yellow-300">
                                    <ul class="list-disc list-inside space-y-1 text-sm font-mono">
                                        <li v-for="code in recoveryCodes" :key="code">{{ code }}</li>
                                    </ul>
                                </div>
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                @click="showRecoveryCodes = false"
                                class="w-full"
                            >
                                Fechar
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Alterar Password</h2>
                    <form @submit="onSubmitPassword">
                        <div class="space-y-4">
                            <FormField v-slot="{ componentField }" name="current_password">
                                <FormItem class="space-y-2">
                                    <FormLabel>Password Atual</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            type="password"
                                            placeholder="Password atual"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="password">
                                <FormItem class="space-y-2">
                                    <FormLabel>Nova Password</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            type="password"
                                            placeholder="Nova password"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="password_confirmation">
                                <FormItem class="space-y-2">
                                    <FormLabel>Confirmar Nova Password</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            type="password"
                                            placeholder="Confirme a nova password"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <div class="flex gap-4 pt-4">
                                <Button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="cursor-pointer"
                                >
                                    {{ passwordForm.processing ? 'A guardar...' : 'Alterar Password' }}
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, useForm as useInertiaForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    twoFactorEnabled: {
        type: Boolean,
        default: false,
    },
});

const passwordSchema = toTypedSchema(z.object({
    current_password: z.string().min(1, 'A password atual é obrigatória'),
    password: z.string().min(8, 'A password deve ter pelo menos 8 caracteres'),
    password_confirmation: z.string().min(1, 'A confirmação da password é obrigatória'),
}).refine((data) => data.password === data.password_confirmation, {
    message: 'As passwords não coincidem',
    path: ['password_confirmation'],
}));

const profileForm = useInertiaForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    mobile: props.user?.mobile || '',
});

const { handleSubmit: handlePasswordSubmit, setFieldError: setPasswordFieldError } = useForm({
    validationSchema: passwordSchema,
    initialValues: {
        current_password: '',
        password: '',
        password_confirmation: '',
    },
});

const passwordForm = useInertiaForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const onSubmitProfile = () => {
    profileForm.put('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['auth', 'user'] });
        },
    });
};

const onSubmitPassword = handlePasswordSubmit((values) => {
    passwordForm.current_password = values.current_password;
    passwordForm.password = values.password;
    passwordForm.password_confirmation = values.password_confirmation;
    
    passwordForm.put('/profile/password', {
        onSuccess: () => {
            passwordForm.reset();
        },
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setPasswordFieldError(field, errors[field]);
            });
        },
    });
});

const twoFactorEnabled = ref(props.twoFactorEnabled || false);
const showQRCode = ref(false);
const showPasswordConfirm = ref(false);
const passwordConfirm = ref('');
const showDisablePasswordConfirm = ref(false);
const disablePasswordConfirm = ref('');
const qrCode = ref(null);
const secretKey = ref(null);
const twoFactorCode = ref('');
const twoFactorError = ref('');
const isProcessing2FA = ref(false);
const showRecoveryCodes = ref(false);
const recoveryCodes = ref(null);

const cancelPasswordConfirm = () => {
    showPasswordConfirm.value = false;
    passwordConfirm.value = '';
    twoFactorError.value = '';
};

const enableTwoFactor = async () => {
    if (twoFactorEnabled.value) {
        twoFactorError.value = 'O 2FA já está ativado';
        return;
    }
    
    if (!passwordConfirm.value) {
        twoFactorError.value = 'Por favor, insira a sua password';
        return;
    }
    
    isProcessing2FA.value = true;
    twoFactorError.value = '';
    
    try {
        await axios.post('/user/confirm-password', { password: passwordConfirm.value });
        
        await axios.post('/user/two-factor-authentication');
        
        const qrCodeResponse = await axios.get('/user/two-factor-qr-code', {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        const secretKeyResponse = await axios.get('/user/two-factor-secret-key', {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        qrCode.value = qrCodeResponse.data?.svg || qrCodeResponse.data;
        secretKey.value = secretKeyResponse.data?.plainTextSecretKey || secretKeyResponse.data;
        
        showPasswordConfirm.value = false;
        passwordConfirm.value = '';
        showQRCode.value = true;
    } catch (error) {
        console.error('Erro ao ativar 2FA:', error);
        
        if (error.response?.status === 423 || error.response?.status === 403) {
            twoFactorError.value = 'Password incorreta ou sessão expirada. Por favor, tente novamente.';
        } else if (error.response?.status === 429) {
            twoFactorError.value = 'Muitas tentativas. Por favor, aguarde alguns minutos antes de tentar novamente.';
        } else if (error.response?.data?.message) {
            twoFactorError.value = error.response.data.message;
        } else {
            twoFactorError.value = `Erro ao ativar 2FA (${error.response?.status || 'desconhecido'}). Verifique o console para mais detalhes.`;
        }
    } finally {
        isProcessing2FA.value = false;
    }
};

const confirmTwoFactor = async () => {
    if (!twoFactorCode.value || twoFactorCode.value.length !== 6) {
        twoFactorError.value = 'Por favor, insira o código de 6 dígitos';
        return;
    }
    
    isProcessing2FA.value = true;
    twoFactorError.value = '';
    
    try {
        await axios.post('/user/confirmed-two-factor-authentication', {
            code: twoFactorCode.value,
        });
        
        const recoveryCodesResponse = await axios.get('/user/two-factor-recovery-codes');
        recoveryCodes.value = recoveryCodesResponse.data;
        
        twoFactorEnabled.value = true;
        showQRCode.value = false;
        showRecoveryCodes.value = true;
        twoFactorCode.value = '';
    } catch (error) {
        console.error('Erro ao confirmar 2FA:', error);
        twoFactorError.value = error.response?.data?.message || 'Código inválido. Por favor, tente novamente.';
    } finally {
        isProcessing2FA.value = false;
    }
};

const cancelDisablePasswordConfirm = () => {
    showDisablePasswordConfirm.value = false;
    disablePasswordConfirm.value = '';
    twoFactorError.value = '';
};

const disableTwoFactor = async () => {
    if (!disablePasswordConfirm.value) {
        twoFactorError.value = 'Por favor, insira a sua password';
        return;
    }
    
    isProcessing2FA.value = true;
    twoFactorError.value = '';
    
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
        
        await axios.post('/user/confirm-password', { password: disablePasswordConfirm.value });
        
        await axios.delete('/user/two-factor-authentication');
        
        twoFactorEnabled.value = false;
        showQRCode.value = false;
        showRecoveryCodes.value = false;
        showDisablePasswordConfirm.value = false;
        disablePasswordConfirm.value = '';
        qrCode.value = null;
        secretKey.value = null;
        recoveryCodes.value = null;
        router.reload({ only: ['auth', 'user', 'twoFactorEnabled'] });
    } catch (error) {
        console.error('Erro ao desativar 2FA:', error);
        if (error.response?.status === 419) {
            twoFactorError.value = 'Sessão expirada. Por favor, recarregue a página e tente novamente.';
        } else if (error.response?.status === 403 || error.response?.status === 423) {
            twoFactorError.value = error.response?.data?.message || 'Password incorreta. Por favor, tente novamente.';
        } else {
            twoFactorError.value = error.response?.data?.message || 'Erro ao desativar 2FA';
        }
    } finally {
        isProcessing2FA.value = false;
    }
};

const cancelTwoFactorSetup = () => {
    showQRCode.value = false;
    showPasswordConfirm.value = false;
    passwordConfirm.value = '';
    qrCode.value = null;
    secretKey.value = null;
    twoFactorCode.value = '';
    twoFactorError.value = '';
    
    axios.delete('/user/two-factor-authentication').catch(() => {});
};

const showRecoveryCodesList = async () => {
    try {
        const response = await axios.get('/user/two-factor-recovery-codes');
        recoveryCodes.value = response.data;
        showRecoveryCodes.value = true;
    } catch (error) {
        console.error('Erro ao obter códigos de recuperação:', error);
        alert('Erro ao obter códigos de recuperação');
    }
};
</script>

