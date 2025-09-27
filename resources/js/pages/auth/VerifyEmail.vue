<script lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';

export default { layout: AuthLayout };
</script>

<script setup lang="ts">
import { Auth } from '@/types';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const toast = useToast();

const resendVerificationEmail = async () => {
    try {
        await axios.post('/email/verification-notification');
        toast.add({
            title: 'Email enviado',
            description: 'Revisa tu correo para verificar tu cuenta.',
            icon: 'lucide:mail-check',
            color: 'success',
        });
    } catch (error) {
        toast.add({
            title: 'Hubo un problema',
            description: 'Por favor inténtalo de nuevo.',
            icon: 'lucide:circle-x',
            color: 'error',
        });
    }
};
</script>

<template>
    <h2 class="mt-4 text-center text-2xl font-extrabold">Verifica tu <br />dirección de email</h2>
    <p class="mt-3 text-center text-sm">
        Hemos enviado un enlace de verificación a <span class="text-base font-semibold">{{ (page.props.auth as Auth).user.email }}</span
        >.
    </p>
    <p class="mt-1 text-center text-sm">
        Haz clic en el enlace para completar el proceso de verificación. Es posible que necesites
        <span class="font-semibold">revisar tu carpeta de spam</span>.
    </p>
    <UButton icon="lucide:mail" label="Reenviar correo" class="mt-6 w-full justify-center" @click="resendVerificationEmail" />
</template>
