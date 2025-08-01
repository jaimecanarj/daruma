<script setup lang="ts">
import Navbar from '@/components/navbar/Navbar.vue';
import { es } from '@nuxt/ui/locale';
</script>

<template>
    <UApp :locale="es">
        <Navbar />
        <UContainer class="my-5">
            <slot />
        </UContainer>
    </UApp>
</template>
