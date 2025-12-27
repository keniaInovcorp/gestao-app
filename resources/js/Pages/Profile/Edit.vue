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
                    <form @submit="onSubmitProfile">
                        <div class="space-y-4">
                            <FormField v-slot="{ componentField }" name="name">
                                <FormItem class="space-y-2">
                                    <FormLabel>Nome</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            placeholder="Seu nome"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="email">
                                <FormItem class="space-y-2">
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            type="email"
                                            placeholder="seu@email.com"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="mobile">
                                <FormItem class="space-y-2">
                                    <FormLabel>Telemóvel</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            placeholder="+351 912 345 678"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

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

const profileSchema = toTypedSchema(z.object({
    name: z.string().min(1, 'O nome é obrigatório'),
    email: z.string().email('Email inválido').min(1, 'O email é obrigatório'),
    mobile: z.string().optional(),
}));

const passwordSchema = toTypedSchema(z.object({
    current_password: z.string().min(1, 'A password atual é obrigatória'),
    password: z.string().min(8, 'A password deve ter pelo menos 8 caracteres'),
    password_confirmation: z.string().min(1, 'A confirmação da password é obrigatória'),
}).refine((data) => data.password === data.password_confirmation, {
    message: 'As passwords não coincidem',
    path: ['password_confirmation'],
}));

const { handleSubmit: handleProfileSubmit, setFieldError: setProfileFieldError } = useForm({
    validationSchema: profileSchema,
    initialValues: {
        name: props.user?.name || '',
        email: props.user?.email || '',
        mobile: props.user?.mobile || '',
    },
});

const { handleSubmit: handlePasswordSubmit, setFieldError: setPasswordFieldError } = useForm({
    validationSchema: passwordSchema,
    initialValues: {
        current_password: '',
        password: '',
        password_confirmation: '',
    },
});

const profileForm = useInertiaForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    mobile: props.user?.mobile || '',
});

const passwordForm = useInertiaForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const onSubmitProfile = handleProfileSubmit((values) => {
    profileForm.name = values.name;
    profileForm.email = values.email;
    profileForm.mobile = values.mobile || '';
    
    profileForm.put('/profile', {
        onSuccess: () => {
            router.reload({ only: ['user'] });
        },
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setProfileFieldError(field, errors[field]);
            });
        },
    });
});

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

