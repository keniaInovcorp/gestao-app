<template>
    <AuthenticatedLayout>
        <Head title="Perfil" />
        <div class="py-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Meu Perfil</h1>
                <p class="text-gray-600 mt-1">Gerir os seus dados pessoais e password</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informações do Perfil -->
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
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

const props = defineProps({
    user: Object,
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
</script>

