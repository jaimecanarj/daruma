<script lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';

export default { layout: AuthLayout };
</script>

<script setup lang="ts">
import SecretInput from '@/components/SecretInput.vue';
import UserImageSelector from '@/components/UserImageSelector.vue';
import { registerSchema } from '@/utils/zodSchemas';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const show = ref<boolean>();

const form = useForm({
    name: '',
    email: '',
    avatar: undefined,
    password: '',
    passwordConfirmation: '',
});

const onSubmit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'passwordConfirmation'),
    });
};
</script>

<template>
    <Head title="Registro" />

    <UForm @submit="onSubmit" :state="form" :schema="registerSchema" class="mt-10">
        <div class="flex justify-center">
            <UserImageSelector v-model="form.avatar" />
        </div>
        <UFormField label="Nombre" name="name" class="mt-4" required>
            <UInput v-model="form.name" class="w-full" />
        </UFormField>
        <UFormField label="Email" name="email" class="mt-4" required>
            <UInput type="email" v-model="form.email" class="w-full" />
        </UFormField>
        <UFormField label="Contraseña" name="password" class="mt-4" required>
            <SecretInput v-model="form.password" v-model:show="show" />
        </UFormField>
        <UFormField label="Confirmar contraseña" name="passwordConfirmation" class="mt-4" required>
            <SecretInput v-model="form.passwordConfirmation" v-model:show="show" />
        </UFormField>
        <UButton type="submit" :loading="form.processing" class="mt-8 w-full justify-center text-base">
            {{ form.processing ? 'Creando' : 'Crear' }} cuenta
        </UButton>
        <p class="text-muted mt-2 text-center text-sm">
            ¿Ya tienes una cuenta?
            <ULink to="/login" class="text-highlighted">Inicia sesión</ULink>
        </p>
    </UForm>
</template>
