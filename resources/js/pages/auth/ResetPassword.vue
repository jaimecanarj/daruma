<script lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';

export default { layout: AuthLayout };
</script>

<script setup lang="ts">
import SecretInput from '@/components/formComponents/SecretInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const toast = useToast();

const props = defineProps<{ token: string; email: string }>();

const show = ref<boolean>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    passwordConfirmation: '',
});

const onSubmit = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            toast.add({
                title: 'Contraseña restablecida',
                description: 'La contraseña se restableció correctamente.',
                icon: 'lucide:circle-check',
                color: 'success',
            });
        },
        onError: (e) => {
            console.log(e);
            toast.add({
                title: 'Hubo un problema.',
                description: 'Es posible que el token haya expirado.',
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
    <Head title="Restablecer contraseña" />

    <UForm @submit="onSubmit" :state="form" class="mt-10">
        <UFormField label="Nueva contraseña" name="password" class="mt-4" required>
            <SecretInput v-model="form.password" v-model:show="show" />
        </UFormField>
        <UFormField label="Confirmar nueva contraseña" name="passwordConfirmation" class="mt-4" required>
            <SecretInput v-model="form.passwordConfirmation" v-model:show="show" />
        </UFormField>
        <UButton type="submit" :loading="form.processing" class="mt-8 w-full justify-center text-base">
            {{ form.processing ? 'Restableciendo' : 'Restablecer contraseña' }}
        </UButton>
    </UForm>
</template>
