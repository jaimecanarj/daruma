<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { store } from '@/routes/login';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const toast = useToast();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const onSubmit = () => {
    form.post(store.form().action, {
        onError: () => {
            toast.add({
                title: 'Hubo un problema.',
                description: 'El usuario o la contraseña son incorrectos',
                icon: 'lucide:circle-x',
                color: 'error',
            });
        },
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <UForm @submit="onSubmit" :state="form" class="mt-10">
        <UFormField label="Email" name="email">
            <UInput type="email" v-model="form.email" class="w-full" />
        </UFormField>
        <UFormField label="Contraseña" name="password" class="mt-4">
            <SecretInput v-model="form.password" />
        </UFormField>
        <ULink
            v-if="canResetPassword"
            to="/forgot-password"
            class="text-sm text-muted"
        >
            ¿Has olvidado tu contraseña?</ULink
        >
        <UFormField name="remember" class="mt-4">
            <UCheckbox label="Recordarme" v-model="form.remember" />
        </UFormField>
        <UButton
            type="submit"
            :loading="form.processing"
            class="mt-8 w-full justify-center text-base"
        >
            {{ form.processing ? 'Iniciando' : 'Iniciar' }} sesión
        </UButton>
    </UForm>
</template>
