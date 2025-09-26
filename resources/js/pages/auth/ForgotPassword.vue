<script lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';

export default { layout: AuthLayout };
</script>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const toast = useToast();

const form = useForm({
    email: '',
});

const onSubmit = () => {
    form.post(route('password.email'), {
        onSuccess: () => {
            toast.add({
                title: 'Correo enviado',
                description: 'Si el correo existe, recibirás un enlace de recuperación.',
                icon: 'lucide:circle-check',
                color: 'success',
            });
        },
        onError: () => {
            toast.add({
                title: 'Hubo un problema.',
                description: 'Por favor inténtalo de nuevo.',
                icon: 'lucide:circle-x',
                color: 'error',
            });
        },
        onFinish: () => {
            form.reset('email');
        },
    });
};
</script>

<template>
    <Head title="Recuperar contraseña" />
    <UForm @submit="onSubmit" :state="form" class="mt-10">
        <UFormField label="Email" name="email" required>
            <UInput type="email" v-model="form.email" class="w-full" />
        </UFormField>
        <UButton type="submit" :loading="form.processing" class="mt-8 w-full justify-center text-base">
            {{ form.processing ? 'Enviando' : 'Enviar correo' }}
        </UButton>
        <p class="text-muted mt-2 text-center text-sm">
            <ULink to="/login" class="text-highlighted">Iniciar sesión</ULink>
        </p>
    </UForm>
</template>
