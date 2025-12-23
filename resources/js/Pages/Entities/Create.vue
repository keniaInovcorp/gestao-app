<template>
    <AuthenticatedLayout>
        <Head :title="type === 'client' ? 'Criar Cliente' : 'Criar Fornecedor'" />
        <div class="py-8">
            <div class="mb-6">
                <Link :href="type === 'client' ? '/clients' : '/suppliers'" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                    ← Voltar
                </Link>
                <h1 class="text-2xl font-bold">Criar {{ type === 'client' ? 'Cliente' : 'Fornecedor' }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit="onSubmit">
                    <FormField v-slot="{ componentField }" name="type">
                        <input type="hidden" v-bind="componentField" />
                    </FormField>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormField v-slot="{ componentField }" name="tax_number">
                            <FormItem class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <FormLabel>NIF</FormLabel>
                                    <FormDescription class="text-xs text-muted-foreground">
                                        Número de Identificação Fiscal
                                    </FormDescription>
                                </div>
                                <FormControl>
                                    <Input
                                        ref="taxNumberInput"
                                        v-bind="componentField"
                                        placeholder="123456789"
                                        @blur="handleTaxNumberBlur"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem class="space-y-2">
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Nome da entidade"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="address" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Morada</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Rua, número"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="postal_code">
                            <FormItem class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <FormLabel>Código Postal</FormLabel>
                                    <FormDescription class="text-xs text-muted-foreground">
                                        Formato: XXXX-XXX
                                    </FormDescription>
                                </div>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="1234-567"
                                        maxlength="8"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="city">
                            <FormItem class="space-y-2">
                                <FormLabel>Localidade</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="Cidade"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="country_id">
                            <FormItem class="space-y-2">
                                <FormLabel>País</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o país" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="country in countries" :key="country.id" :value="country.id.toString()">
                                            {{ country.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="phone">
                            <FormItem class="space-y-2">
                                <FormLabel>Telefone</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="+351 123 456 789"
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

                        <FormField v-slot="{ componentField }" name="website">
                            <FormItem class="space-y-2">
                                <FormLabel>Website</FormLabel>
                                <FormControl>
                                    <Input
                                        v-bind="componentField"
                                        placeholder="https://exemplo.pt"
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
                                        placeholder="exemplo@email.com"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="notes" class="md:col-span-2">
                            <FormItem>
                                <FormLabel>Observações</FormLabel>
                                <FormControl>
                                    <textarea
                                        v-bind="componentField"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Observações adicionais"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="status" class="md:col-span-2">
                            <FormItem class="space-y-2">
                                <FormLabel>Estado</FormLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o estado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="active">Ativo</SelectItem>
                                        <SelectItem value="inactive">Inativo</SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="gdpr_consent" class="md:col-span-2">
                            <FormItem class="flex flex-row items-start space-x-3 space-y-0">
                                <FormControl>
                                    <Checkbox
                                        :model-value="componentField.value"
                                        @update:model-value="componentField.onChange"
                                    />
                                </FormControl>
                                <div class="space-y-1 leading-none">
                                    <FormLabel>
                                        Consentimento RGPD
                                    </FormLabel>
                                    <FormDescription>
                                        Autoriza o tratamento dos dados pessoais
                                    </FormDescription>
                                </div>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <Button
                            type="submit"
                            :disabled="inertiaForm.processing"
                            class="cursor-pointer"
                        >
                            {{ inertiaForm.processing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.visit(type === 'client' ? '/clients' : '/suppliers')"
                            class="cursor-pointer"
                        >
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
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { ref } from 'vue';

const props = defineProps({
    type: String,
    countries: Array,
});

const taxNumberInput = ref(null);

const schema = toTypedSchema(z.object({
    type: z.enum(['client', 'supplier']),
    tax_number: z.string().min(1, 'O NIF é obrigatório'),
    name: z.string().min(1, 'O nome é obrigatório'),
    address: z.string().min(1, 'A morada é obrigatória'),
    postal_code: z.string().regex(/^\d{4}-\d{3}$/, 'Formato inválido (XXXX-XXX)'),
    city: z.string().min(1, 'A localidade é obrigatória'),
    country_id: z.string().min(1, 'O país é obrigatório'),
    phone: z.string().optional(),
    mobile: z.string().optional(),
    website: z.string().refine((val) => !val || z.string().url().safeParse(val).success, { message: 'URL inválida' }).optional(),
    email: z.string().refine((val) => !val || z.string().email().safeParse(val).success, { message: 'Email inválido' }).optional(),
    gdpr_consent: z.boolean().refine((val) => val === true, {
        message: 'É obrigatório aceitar o consentimento RGPD',
    }),
    notes: z.string().optional(),
    status: z.enum(['active', 'inactive']),
}));

const { handleSubmit, setValues, getValues, setFieldError } = useForm({
    validationSchema: schema,
    initialValues: {
        type: props.type || 'client',
        tax_number: '',
        name: '',
        address: '',
        postal_code: '',
        city: '',
        country_id: '',
        phone: '',
        mobile: '',
        website: '',
        email: '',
        gdpr_consent: false,
        notes: '',
        status: 'active',
    },
});

const inertiaForm = useInertiaForm({
    type: props.type || 'client',
    tax_number: '',
    name: '',
    address: '',
    postal_code: '',
    city: '',
    country_id: '',
    phone: '',
    mobile: '',
    website: '',
    email: '',
    gdpr_consent: false,
    notes: '',
    status: 'active',
});

const handleTaxNumberBlur = () => {
    let value = '';

    if (taxNumberInput.value) {
        if (taxNumberInput.value.$el) {
            value = taxNumberInput.value.$el.value || '';
        } else if (taxNumberInput.value.value !== undefined) {
            value = taxNumberInput.value.value || '';
        } else if (typeof taxNumberInput.value === 'string') {
            value = taxNumberInput.value;
        }
    }

    if (!value) {
        const formValues = getValues();
        value = formValues.tax_number || '';
    }

    if (!value) {
        value = inertiaForm.tax_number || '';
    }

    validateVies(value);
};

const validateVies = async (value) => {
    const taxNumber = value || '';

    if (!taxNumber || taxNumber.length < 9) {
        return;
    }

    try {
        const response = await fetch('/entities/validate-vies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: JSON.stringify({
                nif: taxNumber,
                country_code: 'PT',
            }),
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();

        if (!data || Object.keys(data).length === 0) {
            return;
        }

        if (data && data.valid === true) {
            const updates = {};

            if (data.name && data.name.trim() !== '') {
                updates.name = data.name.trim();
                inertiaForm.name = data.name.trim();
            }

            if (data.address && data.address.trim() !== '') {
                const addressParts = data.address.split('\n');
                if (addressParts.length > 0) {
                    updates.address = addressParts[0].trim();
                    inertiaForm.address = addressParts[0].trim();
                }

                if (addressParts.length > 1) {
                    const postalCity = addressParts[1].trim();
                    const postalMatch = postalCity.match(/(\d{4}-\d{3})\s*(.+)/);
                    if (postalMatch) {
                        updates.postal_code = postalMatch[1];
                        inertiaForm.postal_code = postalMatch[1];
                        updates.city = postalMatch[2].trim();
                        inertiaForm.city = postalMatch[2].trim();
                    } else {
                        updates.city = postalCity;
                        inertiaForm.city = postalCity;
                    }
                }
            }

            if (Object.keys(updates).length > 0) {
                setValues(updates);
            }
        }
    } catch (error) {
    }
};

const onSubmit = handleSubmit((values) => {
    inertiaForm.type = values.type;
    inertiaForm.tax_number = values.tax_number;
    inertiaForm.name = values.name;
    inertiaForm.address = values.address;
    inertiaForm.postal_code = values.postal_code;
    inertiaForm.city = values.city;
    inertiaForm.country_id = values.country_id;
    inertiaForm.phone = values.phone || '';
    inertiaForm.mobile = values.mobile || '';
    inertiaForm.website = values.website || '';
    inertiaForm.email = values.email || '';
    inertiaForm.gdpr_consent = values.gdpr_consent || false;
    inertiaForm.notes = values.notes || '';
    inertiaForm.status = values.status;
    
    inertiaForm.post('/entities', {
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                setFieldError(field, errors[field]);
            });
        },
    });
});
</script>
