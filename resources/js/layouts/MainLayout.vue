<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { es } from '@nuxt/ui/locale';
</script>

<template>
    <UApp :locale="es">
        <Navbar />
        <UContainer class="my-3">
            <slot />
        </UContainer>
    </UApp>
</template>
