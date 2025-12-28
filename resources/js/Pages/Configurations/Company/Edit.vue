<template>
    <AuthenticatedLayout>
        <Head title="Empresa" />
        <div class="py-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Empresa</h1>
            </div>

            <FlashMessages />

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="onSubmit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-medium">Logotipo</label>
                            <div class="space-y-4">
                                <div v-if="company?.logo" class="mb-4">
                                    <img 
                                        :src="`/company/logo`" 
                                        alt="Logo" 
                                        class="h-20 w-auto object-contain"
                                    />
                                </div>
                                <Input
                                    type="file"
                                    accept="image/*"
                                    @change="handleLogoChange"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Nome <span class="text-red-500">*</span></label>
                            <Input
                                v-model="inertiaForm.name"
                                placeholder="Nome da empresa"
                            />
                            <p v-if="inertiaForm.errors.name" class="text-sm text-red-600">{{ inertiaForm.errors.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Número Contribuinte</label>
                            <Input
                                v-model="inertiaForm.tax_number"
                                placeholder="Número de contribuinte"
                            />
                            <p v-if="inertiaForm.errors.tax_number" class="text-sm text-red-600">{{ inertiaForm.errors.tax_number }}</p>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-medium">Morada</label>
                            <Input
                                v-model="inertiaForm.address"
                                placeholder="Morada"
                            />
                            <p v-if="inertiaForm.errors.address" class="text-sm text-red-600">{{ inertiaForm.errors.address }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Código Postal</label>
                            <Input
                                v-model="inertiaForm.postal_code"
                                placeholder="XXXX-XXX"
                            />
                            <p v-if="inertiaForm.errors.postal_code" class="text-sm text-red-600">{{ inertiaForm.errors.postal_code }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Localidade</label>
                            <Input
                                v-model="inertiaForm.city"
                                placeholder="Localidade"
                            />
                            <p v-if="inertiaForm.errors.city" class="text-sm text-red-600">{{ inertiaForm.errors.city }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <Button type="submit" :disabled="isProcessing">
                            {{ isProcessing ? 'A guardar...' : 'Guardar' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { Head, useForm as useInertiaForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';

const props = defineProps({
    company: {
        type: Object,
        default: null,
    },
});

const isProcessing = ref(false);

const inertiaForm = useInertiaForm({
    name: props.company?.name || '',
    address: props.company?.address || '',
    postal_code: props.company?.postal_code || '',
    city: props.company?.city || '',
    tax_number: props.company?.tax_number || '',
    logo: null,
});

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        inertiaForm.logo = file;
    }
};

const onSubmit = () => {
    if (inertiaForm.logo) {
        isProcessing.value = true;
        
        const formData = new FormData();
        formData.append('name', inertiaForm.name || '');
        formData.append('address', inertiaForm.address || '');
        formData.append('postal_code', inertiaForm.postal_code || '');
        formData.append('city', inertiaForm.city || '');
        formData.append('tax_number', inertiaForm.tax_number || '');
        formData.append('logo', inertiaForm.logo);
        formData.append('_method', 'PUT');
        
        router.post('/company', formData, {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => {
                isProcessing.value = false;
            },
            onError: () => {
                isProcessing.value = false;
            },
        });
    } else {
        inertiaForm.put('/company', {
            preserveScroll: true,
        });
    }
};
</script>
