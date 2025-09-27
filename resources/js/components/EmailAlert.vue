<script setup lang="ts">
import { Auth } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { useUrlSearchParams } from '@vueuse/core';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const page = usePage();
const toast = useToast();
const params = useUrlSearchParams('history');

const isDismissed = ref(false);

const user = computed(() => (page.props.auth as Auth).user);

const shouldShowAlert = computed(() => {
    return user.value && !user.value.emailVerifiedAt && !isDismissed.value;
});

onMounted(() => {
    if (params.verified === '1') {
        toast.add({
            title: 'Email verificado',
            description: 'Tu cuenta ha sido verificada correctamente.',
            icon: 'lucide:mail-check',
            color: 'success',
        });
    }
});

const resendVerificationEmail = async () => {
    try {
        await axios.post('/email/verification-notification');
        toast.add({
            title: 'Email enviado',
            description: 'Revisa tu correo para verificar tu cuenta.',
            icon: 'lucide:mail-check',
            color: 'success',
        });
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
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
    <UAlert
        v-if="shouldShowAlert"
        variant="soft"
        title="Verifica tu email"
        description="Tu cuenta no está verificada. Por favor, verifica tu dirección de email para acceder a todas las funciones."
        icon="lucide:mail-question-mark"
        close
        @update:open="isDismissed = true"
    >
        <template #actions>
            <UButton label="Reenviar correo" variant="subtle" size="xs" @click="resendVerificationEmail" />
        </template>
    </UAlert>
</template>
