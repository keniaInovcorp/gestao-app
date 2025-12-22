<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Bem-vindo" />
    <div class="min-h-screen bg-gray-100">
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Gestão App
                </h1>
                <p class="text-gray-600 mb-8">
                    Sistema de Gestão Empresarial
                </p>
                <Link
                    href="/login"
                    class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Entrar
                </Link>
            </div>
        </div>
    </div>
</template>

