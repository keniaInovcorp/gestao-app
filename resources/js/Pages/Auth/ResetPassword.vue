<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Definir Password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Por favor, defina a sua password para completar o registo
                </p>
            </div>
            <form class="mt-8 space-y-6" @submit="onSubmit">
                <div class="rounded-md shadow-sm -space-y-px">
                    <FormField v-slot="{ componentField }" name="email">
                        <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                                <Input
                                    v-bind="componentField"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="Email"
                                    class="relative block w-full rounded-t-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="password">
                        <FormItem>
                            <FormLabel>Password</FormLabel>
                            <FormControl>
                                <Input
                                    v-bind="componentField"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Password"
                                    class="relative block w-full border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="password_confirmation">
                        <FormItem>
                            <FormLabel>Confirmar Password</FormLabel>
                            <FormControl>
                                <Input
                                    v-bind="componentField"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Confirmar Password"
                                    class="relative block w-full rounded-b-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <div>
                    <Button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        :disabled="inertiaForm.processing"
                    >
                        {{ inertiaForm.processing ? 'A definir password...' : 'Definir Password' }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm as useInertiaForm } from '@inertiajs/vue3';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

const props = defineProps({
    token: String,
    email: String,
});

const schema = toTypedSchema(z.object({
    token: z.string().min(1, 'Token é obrigatório'),
    email: z.string().email('Email inválido').min(1, 'Email é obrigatório'),
    password: z.string().min(8, 'A password deve ter pelo menos 8 caracteres'),
    password_confirmation: z.string().min(1, 'Confirmação de password é obrigatória'),
}).refine((data) => data.password === data.password_confirmation, {
    message: 'As passwords não coincidem',
    path: ['password_confirmation'],
}));

const { handleSubmit, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        token: props.token || '',
        email: props.email || '',
        password: '',
        password_confirmation: '',
    },
});

const inertiaForm = useInertiaForm({
    token: props.token || '',
    email: props.email || '',
    password: '',
    password_confirmation: '',
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.token = values.token;
    inertiaForm.email = values.email;
    inertiaForm.password = values.password;
    inertiaForm.password_confirmation = values.password_confirmation;

    inertiaForm.post('/reset-password', {
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
        onSuccess: () => {
            window.location.href = '/login';
        },
    });
});
</script>

