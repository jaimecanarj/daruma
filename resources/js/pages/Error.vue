<script lang="ts">
import ErrorLayout from '@/layouts/ErrorLayout.vue';

export default { layout: ErrorLayout };
</script>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ status: Number });

const title = computed(() => {
    return {
        503: 'Servicio no disponible',
        500: 'Error en el servidor',
        404: 'Página no encontrada',
        403: 'Acceso prohibido',
    }[props.status];
});

const description = computed(() => {
    return {
        503: 'Perdón, estamos haciendo mantenimiento. Por favor prueba en unos minutos.',
        500: 'Ups, algo falló en nuestros servidores.',
        404: 'Perdón, la página que estás buscando no existe.',
        403: 'Perdón, no tienes permiso para acceder a esta página.',
    }[props.status];
});
</script>

<template>
    <div class="flex flex-col items-center justify-center space-y-2">
        <h1 class="text-9xl font-light">{{ status }}</h1>
        <h2 class="text-3xl font-semibold">{{ title }}</h2>
        <div class="text-center">{{ description }}</div>
        <UButton v-if="status === 403 && !$page.props.auth.user" label="Iniciar sesión" size="xl" @click="router.visit('/login')" />
        <UButton v-else label="Ir a inicio" size="xl" @click="router.visit('/')" />
    </div>
</template>
