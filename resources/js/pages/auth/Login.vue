<script lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';

export default { layout: AuthLayout };
</script>

<script setup lang="ts">
import SecretInput from '@/components/SecretInput.vue';
import { loginSchema } from '@/utils/zodSchemas';
import { Head, useForm } from '@inertiajs/vue3';

const toast = useToast();

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const onSubmit = () => {
    form.post(route('login'), {
        onError: () => {
            toast.add({ title: 'El usuario o la contraseña son incorrectos' });
        },
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <UForm @submit="onSubmit" :state="form" :schema="loginSchema" class="mt-10">
        <UFormField label="Email" name="email">
            <UInput type="email" v-model="form.email" class="w-full" />
        </UFormField>
        <UFormField label="Contraseña" name="password" class="mt-4">
            <SecretInput v-model="form.password" />
        </UFormField>
        <ULink v-if="canResetPassword" to="/forgot-password" class="text-muted text-sm"> ¿Has olvidado tu contraseña?</ULink>
        <UFormField name="remember" class="mt-4">
            <UCheckbox label="Recordarme" v-model="form.remember" />
        </UFormField>
        <UButton type="submit" :loading="form.processing" class="mt-8 w-full justify-center text-base">
            {{ form.processing ? 'Iniciando' : 'Iniciar' }} sesión
        </UButton>
        <p class="text-muted mt-2 text-center text-sm">
            ¿No tienes cuenta?
            <ULink to="/register" class="text-highlighted">Regístrate</ULink>
        </p>
    </UForm>
</template>
